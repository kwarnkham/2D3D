<?php

namespace App\Models;

use App\Contracts\PointLogable;
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

    public function jackPotReward()
    {
        return $this->belongsTo(JackPotReward::class);
    }

    public function point_log()
    {
        return $this->morphOne(PointLog::class, 'point_loggable');
    }

    public function jackPot()
    {
        return $this->hasOne(JackPot::class);
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

    public static function isMorningCheck(int $time)
    {
        return $time < (static::MORNING_DURATION + 3600 - 59);
    }

    public static function getResult()
    {
        $r = Http::get("https://wwwa1.settrade.com/C13_MarketSummary.jsp");
        $str = trim(preg_replace("/\s+|\n+|\r/", ' ', $r->body()));

        $first = "\width='34%'\>";
        $second = "\<\/td\>";
        if (preg_match("/$first(.*?)$second/", $str, $match)) {
            Log::channel('two-digit')->info("Set is " . str_replace(',', '', $match[1]));
        } else {
            Log::channel('deubg')->info('set not found');
            Log::channel('debug')->info($str);
        }

        $first = "\<td class='mktD' width='22%'\>";
        if (preg_match("/$first(.*?)$second/", $str, $match)) {
            Log::channel('two-digit')->info("Value is " . str_replace(',', '', $match[1]));
        } else {
            Log::channel('deubg')->info('value not found');
            Log::channel('debug')->info($str);
        }
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
        $today = (clone $runTime)->startOfDay();
        if (in_array($today, array_map(
            fn ($day) => new Carbon($day),
            static::CLOSED_DAYS
        ))) {
            return false;
        };
        if ($runTime->isDayOfWeek(Carbon::SATURDAY)) {
            return false;
        } else if ($runTime->isDayOfWeek(Carbon::FRIDAY)) {
            return $runTime->diffInSeconds($today) <= static::EVENING_DURATION;
        } else if ($runTime->isDayOfWeek(Carbon::SUNDAY)) {
            return static::isMorningCheckDiffDay($runTime->diffInSeconds($today));
        } else {
            return true;
        }
    }

    public static function checkTime(Carbon $runTime = null)
    {
        if (!$runTime) $runTime = now();
        $today = (clone $runTime)->startOfDay();
        $time = $runTime->diffInSeconds($today);
        if (static::isMorningCheck($time)) {
            $passed = static::isMorning($time);
            return $passed;
        } else if (static::isEveningCheck($time)) {
            $passed = static::isEvening($time);

            return $passed;
        } else if (static::isMorningCheckDiffDay($time)) {
            return true;
        }
    }

    public static function getQueryBuilderOfEffectedNumbers(Carbon $runTime = null)
    {
        if (!$runTime) $runTime = now();
        $time = $runTime->diffInSeconds(today());
        $isMorning = static::isMorning($time) || static::isMorningCheckDiffDay($time);
        if ($isMorning) {
            $startTime = today()->subDay()->addSeconds(TwoDigit::EVENING_DURATION + 1800);
            $endTime = today()->addSeconds(TwoDigit::MORNING_DURATION);

            return TwoDigit::where('created_at', '<=', $endTime)->where('created_at', '>=', $startTime);
        } else {
            $startTime = today()->addSeconds(TwoDigit::MORNING_DURATION + 3600 - 59);
            $endTime = today()->addSeconds(TwoDigit::EVENING_DURATION);

            return TwoDigit::where('created_at', '>=', $startTime)
                ->where('created_at', '<=', $endTime);
        }
    }

    public function processPrize()
    {
        if (!$this->two_digit_hit_id || !$this->settled_at) return;

        $this->user->increasePoint(Point::find($this->point_id), $this->amount * $this->twoDigitHit->rate, '2d prize', $this);
        if ($this->jack_pot_reward_id) $this->user->increasePoint(Point::find($this->point_id), $this->jackPotReward->shared_amount, 'jackpot prize', $this);
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
            if ($maxPrize < (($number['amount'] + $numberTotalAmount) * 10)) return $number['number'];
        }
        return "passed";
    }

    public static function processJackPot(TwoDigitHit $twoDigitHit)
    {
        DB::transaction(function () use ($twoDigitHit) {
            $query = TwoDigit::whereNull('jack_potted_at')->whereNull('two_digit_hit_id');
            foreach ($query->get() as $twoDigit) {
                $twoDigit->jackPot()->create([
                    'amount' => $twoDigit->amount * AppSetting::current()->config->jackpot_rate
                ]);
            }
            $query->update(['jack_potted_at' => now()]);
            Cache::forget('twoDigitJackPot');
        });
    }

    public static function getIncome(Carbon $day = null)
    {
        $query = TwoDigit::whereNotNull('settled_at')->whereNull('two_digit_hit_id');
        if ($day) {
            $startTime = $day->startOfDay()->subDay()->addSeconds(TwoDigit::EVENING_DURATION);
            $endTime = (clone $startTime)->addSeconds(86400);
            $query->where('created_at', '>', $startTime)->where('created_at', '<=', $endTime);
        }

        return $query->pluck('amount')->sum();
    }
}
