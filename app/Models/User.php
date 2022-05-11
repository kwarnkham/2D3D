<?php

namespace App\Models;

use App\Services\TelegramService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use App\Contracts\PointLogable;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Support\Facades\App;

class User extends Authenticatable implements HasLocalePreference
{
    use HasApiTokens, HasFactory, Notifiable;
    const RS = ['points', 'roles'];
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the user's preferred locale.
     *
     * @return string
     */
    public function preferredLocale()
    {
        return $this->locale;
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id', 'id');
    }

    public function referrees()
    {
        return $this->hasMany(User::class, 'referrer_id', 'id');
    }

    public function setLocale($locale)
    {
        if (in_array($locale, ['my', 'en'])) {
            App::setLocale($locale);
            $this->locale = $locale;
            $this->save();
        }
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['has_default_password', 'can_withdraw', 'last_password_change', 'referral_code'];

    public function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcrypt($value),
        );
    }

    public function referralCode(): Attribute
    {
        return Attribute::make(
            get: fn () => "888X" . $this->id * 6 . "X666" . $this->id,
        );
    }

    public static function getIdFromReferralCode($referrerCode)
    {
        $temp = substr(substr($referrerCode, 4), 0, strpos(substr($referrerCode, 4), "X"));
        if (!$temp) return;
        if ($temp % 6 != 0) return;
        return $temp / 6;
    }

    public function passwordChanges()
    {
        return $this->hasMany(PasswordChange::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function pointLogs()
    {
        return $this->hasMany(PointLog::class);
    }

    public function accountProviders()
    {
        return $this->belongsToMany(AccountProvider::class)->using(UserProvider::class)->withPivot(['provider_id', 'username', 'sent_at']);
    }

    public function twoDigits()
    {
        return $this->hasMany(TwoDigit::class);
    }

    public function points()
    {
        return $this->belongsToMany(Point::class)->withPivot(['balance', 'referrable_balance'])->withTimestamps();
    }

    public function withdraws()
    {
        return $this->hasMany(Withdraw::class);
    }

    public function mmk()
    {
        return $this->belongsToMany(Point::class)->withPivot(['balance'])->withTimestamps()->wherePivot('point_id', 2)->first();
    }

    public function hasRecentPasswordChange()
    {
        return $this->passwordChanges->count() > 1 &&
            $this->passwordChanges()->orderBy('id', 'desc')->first()->created_at->diffInMinutes(now()) < (24 * 60);
    }

    public function freePoint()
    {
        return $this->belongsToMany(Point::class)->withPivot(['balance'])->withTimestamps()->wherePivot('point_id', 1)->first();
    }

    public function hasDefaultPassword(): Attribute
    {
        return new Attribute(
            get: fn () => !$this->passwordChanges()->first(),
        );
    }

    public function canWithdraw(): Attribute
    {
        return new Attribute(
            get: fn () => !$this->hasRecentPasswordChange(),
        );
    }

    public function lastPasswordChange(): Attribute
    {
        return new Attribute(
            get: fn () => PasswordChange::where('user_id', $this->id)->orderBy('id', 'desc')->first(),
        );
    }

    public static function summon(Request $request)
    {
        $userProvider = UserProvider::where('provider_id', $request->message['from']['id'])->first();
        if ($userProvider) return [$userProvider->user, null];
        return static::register($request);
    }

    public static function register(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $password = str()->random(6);
            $data = [
                'name' => 't' . $request->message['from']['id'],
                'password' => $password
            ];
            $text = explode(" ", $request->message['text']);
            if ($text[0] == '/start' && array_key_exists(1, $text) && static::getIdFromReferralCode($text[1])) {
                $data['referrer_id'] = static::getIdFromReferralCode($text[1]);
            } else {
                $data['referrer_id'] = 1;
            }
            $user = User::create($data);
            $accountProvider = AccountProvider::where('name', 'telegram')->first();
            if (!$accountProvider) $accountProvider = AccountProvider::create(['name' => 'telegram']);
            UserProvider::create([
                'user_id' => $user->id,
                'account_provider_id' => $accountProvider->id,
                'provider_id' => $request->message['from']['id'],
                'username' => array_key_exists('username', $request->message['from']) ? $request->message['from']['username'] : null,
                'sent_at' => $request->message['date']
            ]);
            return [$user, $password];
        });
    }

    public function getBalanceByPoint(Point $point)
    {
        $userPoint = $this->points()->where('point_id', $point->id)->first();
        if (!$userPoint) return 0;
        return $userPoint->pivot->balance;
    }

    public function getReferableBalanceByPoint(Point $point)
    {
        $userPoint = $this->points()->where('point_id', $point->id)->first();
        if (!$userPoint) return 0;
        return $userPoint->pivot->referrable_balance;
    }

    public function isBanned()
    {
        return !!$this->banned_at;
    }


    public function notify($message)
    {
        foreach ($this->accountProviders as $channel) {
            if ($channel->name == 'telegram') {
                TelegramService::sendMessage($message, $channel->pivot->provider_id);
            }
        }
    }

    public function reverseResitration()
    {
        $this->pointLogs()->delete();
        DB::table('point_user')->where('user_id', $this->id)->delete();
        DB::table('account_provider_user')->where('user_id', $this->id)->delete();
        $this->delete();
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['status'] ?? false,
            fn ($q, $status) => $status != 'banned' ? $q->whereNull('banned_at') : $q->whereNotNull('banned_at')
        );

        $query->when(
            $filters['order_in'] ?? false,
            fn ($q, $orderIn) => $q->orderBy('id', $orderIn)
        );

        $query->when(
            $filters['name'] ?? false,
            fn ($q, $name) => $q->where('name', 'like', '%' . $name . '%')
        );

        $query->when(
            $filters['without_admin'] ?? false,
            fn ($q) => $q->join('role_user', 'role_user.id', '!=', 'users.id')
        );

        $query->when(
            $filters['without_user'] ?? false,
            fn ($q) => $q->join('role_user', 'role_user.id', '=', 'users.id')
        );
    }

    public function processReferrerReward(User $referree, $spentAmount, Point $point)
    {
        if ($referree->decreaseReferrablePoint($point, $spentAmount)) {
            $rate = 0.05;
            $amount = $spentAmount * $rate;

            $refReward = ReferralReward::create([
                'amount' => $amount,
                'rate' => $rate,
                'referrer_id' => $this->id,
                'referree_id' => $referree->id,
                'point_id' => $point->id,
            ]);
            $this->increasePoint(point: $point, amount: $amount, note: "referral reward", model: $refReward);
        }
    }

    public function decreaseReferrablePoint(Point $point, $amount)
    {
        $referrableBalance = $this->getReferableBalanceByPoint($point);
        if ($referrableBalance <= 0) return;
        return DB::transaction(function () use ($point, $amount, $referrableBalance) {
            return $this->points()->updateExistingPivot($point->id, [
                'referrable_balance' => $referrableBalance - $amount,
            ]);
        });
    }

    public function decreasePoint(Point $point, $amount, $note = null, PointLogable $model = null)
    {
        DB::transaction(function () use ($point, $amount, $note, $model) {
            $this->points()->updateExistingPivot($point->id, [
                'balance' => $this->getBalanceByPoint($point) - $amount,
            ]);
            //point_logs_type 1, decreasePoint
            $data = [
                'user_id' => $this->id,
                'point_id' => $point->id,
                'amount' => -$amount,
                'type' => 1,
                'note' => $note
            ];
            if (!$model) PointLog::create($data);
            else $model->point_log()->create($data);
        });
    }

    public function increasePoint(Point $point, $amount, $note = null, PointLogable $model = null, $referrable = false)
    {
        Log::alert("Increase the point $point->name of $this->name by $amount");
        DB::transaction(function () use ($point, $amount, $note, $model, $referrable) {
            if (!$this->points()->where('point_id', $point->id)->first()) {
                $this->points()->attach($point->id, ['balance' => 0, 'referrable_balance' => 0]);
            }
            $balanceData =  [
                'balance' => $this->getBalanceByPoint($point) + $amount,
            ];
            if ($referrable) $balanceData['referrable_balance'] = $this->getReferableBalanceByPoint($point) + $amount;
            $this->points()->updateExistingPivot($point->id, $balanceData);
            //point_logs_type 2, increasePoint
            $data = [
                'user_id' => $this->id,
                'point_id' => $point->id,
                'amount' => $amount,
                'type' => 2,
                'note' => $note
            ];
            if (!$model) PointLog::create($data);
            else $model->point_log()->create($data);
        });
    }

    public function isAdmin()
    {
        return $this->roles->contains(function ($role) {
            return $role->name == 'admin';
        });
    }

    public static function makeAdmin($name, $password)
    {
        if (User::where('name', $name)->exists()) return "$name is already taken";
        return DB::transaction(function () use ($name, $password) {
            return User::create(['name' => $name, 'password' => $password])->roles()->attach(Role::where('name', 'admin')->first());
        });
    }
}
