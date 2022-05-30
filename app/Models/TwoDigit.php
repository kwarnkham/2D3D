<?php

namespace App\Models;

use App\Contracts\PointLogable;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwoDigit extends AppModel implements PointLogable
{
    use HasFactory;
    const MORNING_DURATION = 18059; //05:00:59, allow till this time
    const EVENING_DURATION = 34259; //09:30:59, allow till this time
    const RS = ['point', 'twoDigitHit', 'user'];
    const CLOSED_DAYS = [
        '2022-01-03',
        '2022-02-16',
        '2022-04-06',
        '2022-04-13',
        '2022-04-14',
        '2022-04-15',
        '2022-05-02',
        '2022-05-04',
        '2022-05-16',
        '2022-06-03',
        '2022-07-13',
        '2022-07-28',
        '2022-07-29',
        '2022-08-12',
        '2022-10-13',
        '2022-10-14',
        '2022-10-24',
        '2022-12-05',
        '2022-12-12'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jackpotReward()
    {
        return $this->belongsTo(JackpotReward::class);
    }

    public function point_log()
    {
        return $this->morphOne(PointLog::class, 'point_loggable');
    }

    public function jackpot()
    {
        return $this->hasOne(Jackpot::class);
    }

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function twoDigitHit()
    {
        return $this->belongsTo(TwoDigitHit::class);
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['created_day', 'created_time'];

    public function createdDay(): Attribute
    {
        return new Attribute(
            get: fn () => $this->created_at->format('d/m/Y'),
        );
    }

    public static function getResult(Carbon $time = null, $notify = true)
    {
        if (!$time) $time = now();
        $isMorningTime = $time->lessThanOrEqualTo((clone $time)->startOfDay()->addHours(6)->addMinutes(30));
        // $isEveningTime = $time->greaterThan((clone $time)->startOfDay()->addHours(6)->addMinutes(30)) && $time->lessThanOrEqualTo((clone $time)->startOfDay()->addHours(10)->addMinutes(30));
        $resposne = Http::get('https://www.myanmar123.com/two-d');
        $str = trim(preg_replace("/\s+|\n+|\r/", ' ', $resposne->body()));
        if ($isMorningTime)
            $first = '\<tr\> \<td\>' . str_replace('/', '\/', $time->format("d/m/Y")) . '\<\/td\> \<td class="text-center"\>12\:01\:00 PM\<\/td\> ';
        else $first = '\<tr\> \<td\>' . str_replace('/', '\/', $time->format("d/m/Y")) . '\<\/td\> \<td class="text-center"\>04\:31\:00 PM\<\/td\> ';
        $second = '\<\/tr\>';
        $number = null;
        $set = null;
        $value = null;
        if (preg_match("/$first(.*?)$second/", $str, $match)) {
            $first = '"\>';
            $second = '\<\/td\>';
            if (preg_match_all("/$first(.*?)$second/", $match[1], $found)) {
                $found[1] = array_map(fn ($value) => str_replace(',', '', $value), $found[1]);
                if (is_numeric($found[1][0]) && is_numeric($found[1][1]) && is_numeric($found[1][2])) {
                    $number = $found[1][0];
                    $set = $found[1][1];
                    $value = $found[1][2];
                    // echo $set;
                    // echo PHP_EOL;
                    // echo $value;
                    // echo PHP_EOL;
                    // echo $number;
                }
            }
        }
        if (!$number) return;

        $data = [
            'number' => $number,
            'rate' => AppSetting::current()->rate,
            'day' => (clone $time)->startOfDay(),
            'morning' => $isMorningTime,
            'set' => $set,
            'value' => $value
        ];
        if ($notify)
            TelegramService::sendAdminMessage(json_encode($data));
        return $number;
    }

    public static function isMorningCheck(int $time)
    {
        return $time < (static::MORNING_DURATION + 3600 - 59);
    }

    public static function isEveningCheck(int $time)
    {
        return $time >= (static::MORNING_DURATION + 3600 - 59) && $time < (static::EVENING_DURATION + 3600 - 59);
    }

    public static function isMorningCheckDiffDay(int $time)
    {
        return $time >= (static::EVENING_DURATION + 3600 - 59);
    }

    public static function isMorning(int $time)
    {
        return static::MORNING_DURATION >= $time;
    }

    public static function isEvening(int $time)
    {
        return static::EVENING_DURATION >= $time;
    }



    public static function checkDay(Carbon $runTime = null)
    {

        if (!$runTime) $runTime = now();
        if ($runTime->isDayOfWeek(Carbon::SATURDAY)) return false;
        $today = (clone $runTime)->startOfDay();
        if (in_array($today, array_map(
            fn ($day) => (new Carbon($day))->subDay(),
            static::CLOSED_DAYS
        ))) {
            if (
                $today->isDayOfWeek(Carbon::SUNDAY)
                || $today->isDayOfWeek(Carbon::SATURDAY)
                || in_array($today, array_map(
                    fn ($day) => new Carbon($day),
                    static::CLOSED_DAYS
                ))
            ) return false;
            else return $runTime->diffInSeconds($today) <= static::EVENING_DURATION;
        };
        if (in_array($today, array_map(
            fn ($day) => new Carbon($day),
            static::CLOSED_DAYS
        ))) {
            if ($today->isDayOfWeek(Carbon::FRIDAY) || $today->isDayOfWeek(Carbon::SATURDAY) || in_array((clone $today)->addDay(), array_map(fn ($value) => new Carbon($value), TwoDigit::CLOSED_DAYS))) return false;
            else return static::isMorningCheckDiffDay($runTime->diffInSeconds($today));
        };
        if ($runTime->isDayOfWeek(Carbon::FRIDAY)) {
            return $runTime->diffInSeconds($today) <= static::EVENING_DURATION;
        } else if ($runTime->isDayOfWeek(Carbon::SUNDAY)) {
            return static::isMorningCheckDiffDay($runTime->diffInSeconds($today));
        } else {
            return true;
        }
    }

    public static function checkTime(Carbon $runTime = null)
    {
        if (!static::checkDay($runTime)) return;
        if (!$runTime) $runTime = now();
        $today = (clone $runTime)->startOfDay();
        $time = $runTime->diffInSeconds($today);
        // b4 12:30:00
        if (static::isMorningCheck($time)) {
            // b4 11:30:59(inclusive)
            return static::isMorning($time);
        }
        // after 12:30:00(inclusive) and b4 05:00:00
        else if (static::isEveningCheck($time)) {
            // b4 04:00:59(inclusive)
            return static::isEvening($time);
        }
        // after 05:00:00(inclusive)
        else if (static::isMorningCheckDiffDay($time)) {
            return true;
        }
    }

    public static function getQueryBuilderOfEffectedNumbers(Carbon $runTime = null)
    {
        if (!$runTime) $runTime = now();
        $time = $runTime->diffInSeconds((clone $runTime)->startOfDay());
        if (static::isMorning($time)) {
            $startTime = (clone $runTime)->startOfDay()->subDay()->addSeconds(TwoDigit::EVENING_DURATION + 1800);
            $endTime = (clone $runTime)->startOfDay()->addSeconds(TwoDigit::MORNING_DURATION);
            return TwoDigit::where('created_at', '>=', $startTime)->where('created_at', '<=', $endTime);
        } else if (static::isMorningCheckDiffDay($time)) {
            $startTime = (clone $runTime)->startOfDay()->addSeconds(TwoDigit::EVENING_DURATION + 1800);
            return TwoDigit::where('created_at', '>=', $startTime);
        } else {
            $startTime = (clone $runTime)->startOfDay()->addSeconds(TwoDigit::MORNING_DURATION + 3600 - 59);
            $endTime = (clone $runTime)->startOfDay()->addSeconds(TwoDigit::EVENING_DURATION);
            return TwoDigit::where('created_at', '>=', $startTime)->where('created_at', '<=', $endTime);
        }
    }

    public function processPrize()
    {
        if (!$this->two_digit_hit_id || !$this->settled_at) return;

        $this->user->increasePoint(Point::find($this->point_id), $this->amount * $this->twoDigitHit->rate, '2d prize', $this);
        if ($this->jackpot_reward_id) $this->user->increasePoint(Point::find($this->point_id), $this->jackpotReward->shared_amount, 'jackpot prize', $this);
    }


    public function scopeOf($query, User $user)
    {
        $query->where('user_id', $user->id);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['settled'] ?? false,
            fn ($q, $settled) => $settled == 'yes' ? $q->whereNotNull('settled_at') : $q->whereNull('settled_at')
        );

        $query->when(
            $filters['order_in'] ?? false,
            fn ($q, $orderIn) => $q->orderBy('id', $orderIn)
        );

        $query->when(
            $filters['point_id'] ?? false,
            fn ($q, $pointId) => $q->where('point_id', $pointId)
        );
    }

    public function settledAt(): Attribute
    {

        return Attribute::make(
            get: fn ($value) => $value ? new Carbon($value) : $value,
        );
    }



    public static function getMaxPrize(int $number)
    {
        $income = static::getQueryBuilderOfEffectedNumbers()
            ->where('number', '!=', $number)
            ->where('point_id', 2)->pluck('amount')->sum();
        $capital = 1000000;
        return $income + $capital;
    }

    public static function checkMaxPrize(array $numbers)
    {
        foreach ($numbers as $number) {
            $maxPrize = static::getMaxPrize($number['number']) + (int)collect($numbers)->filter(fn ($value) => $value['number'] != $number['number'])->reduce(fn ($carry, $value) => $value['amount'] + $carry, 0);
            $numberTotalAmount = static::getQueryBuilderOfEffectedNumbers()->where('number', $number['number'])->pluck('amount')->sum();
            if ($maxPrize < (($number['amount'] + $numberTotalAmount) * 85)) return $number['number'];
        }
        return "passed";
    }

    public static function processJackpot(TwoDigitHit $twoDigitHit)
    {
        DB::transaction(function () use ($twoDigitHit) {
            $query = TwoDigit::whereNull('jackpotted_at')->whereNull('two_digit_hit_id');
            foreach ($query->get() as $twoDigit) {
                $twoDigit->jackpot()->create([
                    'amount' => $twoDigit->amount * AppSetting::current()->config->jackpot_rate
                ]);
            }
            $query->update(['jackpotted_at' => now()]);
            Cache::forget('twoDigitJackpot');
        });
    }

    public static function getIncome(Carbon $day = null)
    {
        $query = TwoDigit::whereNotNull('settled_at')->whereNull('two_digit_hit_id');
        if ($day) {
            $startTime = (clone $day)->startOfDay()->subDay()->addSeconds(TwoDigit::EVENING_DURATION);
            $endTime = (clone $startTime)->addSeconds(86400);
            $query->where('created_at', '>', $startTime)->where('created_at', '<=', $endTime);
        }

        return $query->pluck('amount')->sum();
    }
}
