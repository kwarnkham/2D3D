<?php

namespace App\Models;

use App\Contracts\PointLogable;
use App\Enums\ResponseStatus;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TwoDigit extends Model implements PointLogable
{
    use HasFactory;
    protected $guarded = ['id'];
    const MORNING_LAST_MINUTE = 300;
    const EVENING_LAST_MINUTE = 570;
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

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function twoDigitHit()
    {
        return $this->belongsTo(TwoDigitHit::class);
    }

    public static function checkTime()
    {
        $time = now()->diffInMinutes(today());
        if ($time < (static::MORNING_LAST_MINUTE + 60)) {
            Log::channel('debug')->info(static::MORNING_LAST_MINUTE - $time >= 0 ? 'allow' : 'abort');
            return static::MORNING_LAST_MINUTE - $time >= 0;
            // abort_if(static::MORNING_LAST_MINUTE - $time < 0, ResponseStatus::BAD_REQUEST->value, "Morning order is closed. Evening order starts at 12:30 PM");
        } else if ($time >= (static::MORNING_LAST_MINUTE + 60) && $time < (static::EVENING_LAST_MINUTE + 60)) {
            Log::channel('debug')->info(static::EVENING_LAST_MINUTE - $time >= 0 ? 'allow' : 'abort');
            return static::EVENING_LAST_MINUTE - $time >= 0;
            // abort_if(static::EVENING_LAST_MINUTE - $time < 0, ResponseStatus::BAD_REQUEST->value, "Evening order is closed. Next order starts at 05:00 PM");
        } else {
            Log::channel('debug')->info('out of consider time, allow');
            return true;
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

    public static function getQueryBuilderOfEffectedNumbers()
    {

        // should get today evening if run time is today evening
        $isSameDay = now()->diffInMinutes(today()) <= TwoDigit::MORNING_LAST_MINUTE;
        $isMorning = today()->diffInMinutes(now()) <= TwoDigit::MORNING_LAST_MINUTE || now()->greaterThanOrEqualTo(today()->addMinutes(TwoDigit::EVENING_LAST_MINUTE + 60));
        if ($isMorning) {
            $morningStartMinute = today()->addMinutes(TwoDigit::EVENING_LAST_MINUTE + 30);
            if (!$isSameDay) $morningStartMinute->subDay();
            $morningLastMinute = today()->addMinutes(TwoDigit::MORNING_LAST_MINUTE)->addSeconds(59);
            $query = TwoDigit::where('created_at', '<=', $morningLastMinute);
            if (!$isSameDay) $query->where('created_at', '>=', $morningStartMinute);
            else $query->where('created_at', '>=', today());
            Log::channel('debug')->info("getMaxPrize is morning");
            return $query;
        } else {
            $eveningStartMinute = today()->addMinutes(TwoDigit::MORNING_LAST_MINUTE + 60);
            $eveningLastMinute = today()->addMinutes(TwoDigit::EVENING_LAST_MINUTE)->addSeconds(59);
            Log::channel('debug')->info("getMaxPrize is not morning");
            return TwoDigit::where('created_at', '>=', $eveningStartMinute)
                ->where('created_at', '<=', $eveningLastMinute);
        }
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
}
