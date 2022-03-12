<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoDigit extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

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

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function twoDigitHit()
    {
        return $this->belongsTo(TwoDigitHit::class);
    }
}
