<?php

namespace App\Models;

use App\Contracts\PointLogable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TwoDigit extends Model implements PointLogable
{
    use HasFactory;
    protected $guarded = ['id'];
    const MORNING_DURATION = 18059; //05:00:59, allow till this time
    const EVENING_DURATION = 34259; //09:30:59, allow till this time
    const RS = ['point', 'twoDigitHit'];
    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return (new Carbon($date))->diffForHumans();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function point_log()
    {
        return $this->morphOne(PointLog::class, 'point_loggable');
    }

    public function jackPot()
    {
        return $this->morphOne(JackPot::class, 'jack_potable');
    }

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function twoDigitHit()
    {
        return $this->belongsTo(TwoDigitHit::class);
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

    public static function checkTime(Carbon $runTime = null)
    {
        if (!$runTime) $runTime = now();
        $time = $runTime->diffInSeconds(today());
        if (static::isMorningCheck($time)) {
            $passed = static::isMorning($time);
            Log::channel('debug')->info($runTime->format('H:i:s'));
            Log::channel('debug')->info('Morning');
            Log::channel('debug')->info($passed ? 'allow' : 'abort');
            return $passed;
        } else if (static::isEveningCheck($time)) {
            $passed = static::isEvening($time);
            Log::channel('debug')->info($runTime->format('H:i:s'));
            Log::channel('debug')->info('Evening');
            Log::channel('debug')->info($passed ? 'allow' : 'abort');
            return $passed;
        } else if (static::isMorningCheckDiffDay($time)) {
            Log::channel('debug')->info($runTime->format('H:i:s'));
            Log::channel('debug')->info('Morning Diff Day');
            Log::channel('debug')->info('allow');
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
            Log::channel('debug')->info('morning');
            return TwoDigit::where('created_at', '<=', $endTime)->where('created_at', '>=', $startTime);
        } else {
            $startTime = today()->addSeconds(TwoDigit::MORNING_DURATION + 3600 - 59);
            $endTime = today()->addSeconds(TwoDigit::EVENING_DURATION);
            Log::channel('debug')->info('evening');
            return TwoDigit::where('created_at', '>=', $startTime)
                ->where('created_at', '<=', $endTime);
        }
    }

    public function processPrize()
    {
        if (!$this->two_digit_hit_id || !$this->settled_at) return;
        $this->user->increasePoint(Point::find($this->point_id), $this->amount * $this->twoDigitHit->rate, $this->id . ', won the prize', $this->twoDigitHit);
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
            get: fn ($value) => new Carbon($value),
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
        DB::transaction(function () {
            $query = TwoDigit::whereNull('jack_potted_at')->whereNull('two_digit_hit_id');
            foreach ($query->get() as $twoDigit) {
                $twoDigit->jackPot()->create([
                    'amount' => $twoDigit->amount * 0.1
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
