<?php

namespace App\Models;

use App\Contracts\PointLogable;
use App\Enums\ResponseStatus;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function point_logs()
    {
        return $this->morphMany(PointLog::class, 'point_logable');
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
            if (static::MORNING_LAST_MINUTE - $time < 0) abort(ResponseStatus::BAD_REQUEST->value, "Morning order is closed. Evening order starts at 12:30 PM");
        } else if ($time >= (static::MORNING_LAST_MINUTE + 60) && $time < (static::EVENING_LAST_MINUTE + 60)) {
            if (static::EVENING_LAST_MINUTE - $time < 0) abort(ResponseStatus::BAD_REQUEST->value, "Evening order is closed. Next order starts at 05:00 PM");
        }
    }

    public function processPrize()
    {
        if (!$this->two_digit_hit_id || !$this->settled_at) return;
        $this->user->increasePoint(Point::find($this->point_id), $this->amount * $this->twoDigitHit->rate, 'won the prize', $this);
    }


    public function scopeOf($query, User $user)
    {
        $query->where('user_id', $user->id);
    }
}
