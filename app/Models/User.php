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

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => bcrypt($value),
        );
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
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
        return $this->belongsToMany(Point::class)->withPivot(['balance'])->withTimestamps();
    }

    public function mmk()
    {
        return $this->belongsToMany(Point::class)->withPivot(['balance'])->withTimestamps()->wherePivot('point_id', 2)->first();
    }

    public function freePoint()
    {
        return $this->belongsToMany(Point::class)->withPivot(['balance'])->withTimestamps()->wherePivot('point_id', 1)->first();
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
            $user = User::create([
                'name' => 't' . $request->message['from']['username'],
                'password' => $password
            ]);
            $accountProvider = AccountProvider::where('name', 'telegram')->first();
            if (!$accountProvider) $accountProvider = AccountProvider::create(['name' => 'telegram']);
            UserProvider::create([
                'user_id' => $user->id,
                'account_provider_id' => $accountProvider->id,
                'provider_id' => $request->message['from']['id'],
                'username' => $request->message['from']['username'],
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


    public function notify(string $message)
    {
        foreach ($this->accountProviders as $channel) {
            if ($channel->name == 'telegram') {
                TelegramService::sendMessage($message, $channel->pivot->provider_id);
            }
        }
    }

    public function decreasePoint(Point $point, $amount, $note = null)
    {
        DB::transaction(function () use ($point, $amount, $note) {
            $this->points()->updateExistingPivot($point->id, [
                'balance' => $this->getBalanceByPoint($point) - $amount,
            ]);
            //point_logs_type 1, decreasePoint
            PointLog::create([
                'user_id' => $this->id,
                'point_id' => $point->id,
                'amount' => -$amount,
                'type' => 1,
                'note' => $note
            ]);
        });
    }

    public function increasePoint(Point $point, $amount, $note = null)
    {
        Log::alert("Increase the point $point->name of $this->name by $amount");
        DB::transaction(function () use ($point, $amount, $note) {
            if (!$this->points()->where('point_id', $point->id)->first()) {
                $this->points()->attach($point->id, ['balance' => 0]);
            }
            $this->points()->updateExistingPivot($point->id, [
                'balance' => $this->getBalanceByPoint($point) + $amount,
            ]);
            //point_logs_type 2, increasePoint
            PointLog::create([
                'user_id' => $this->id,
                'point_id' => $point->id,
                'amount' => $amount,
                'type' => 2,
                'note' => $note
            ]);
        });
    }

    public static function makeAdmin($name, $password)
    {
        if (User::where('name', $name)->exists()) return "$name is already taken";
        return Role::where('name', 'admin')->first()->users()->attach(User::create(['name' => $name, 'password' => bcrypt($password)])->id);
    }
}
