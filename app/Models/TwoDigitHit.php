<?php

namespace App\Models;

use App\Contracts\PointLogable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TwoDigitHit extends Model implements PointLogable
{
    use HasFactory;

    protected $guarded = ['id'];

    public function twoDigits()
    {
        return $this->hasMany(TwoDigit::class);
    }

    public function point_log()
    {
        return $this->morphMany(PointLog::class, 'point_loggable');
    }

    public function settle()
    {
        return DB::transaction(function () {
            if ($this->morning) {
                $morningStartTime = (new Carbon($this->day))->subDay()->addSeconds(TwoDigit::EVENING_DURATION + 3600 - 59);
                $morningEndTime = (new Carbon($this->day))->addSeconds(TwoDigit::MORNING_DURATION);
                $builder = TwoDigit::where('created_at', '>=', $morningStartTime)
                    ->where('created_at', '<=', $morningEndTime)
                    ->whereNull('settled_at');
            } else {
                $eveningStartTime = (new Carbon($this->day))->addSeconds(TwoDigit::MORNING_DURATION + 3600 - 59);
                $eveningEndTime = (new Carbon($this->day))->addSeconds(TwoDigit::EVENING_DURATION);
                $builder = TwoDigit::where('created_at', '>=', $eveningStartTime)
                    ->where('created_at', '<=', $eveningEndTime)
                    ->whereNull('settled_at');
            }
            (clone $builder)->where('number', $this->number)->update(['two_digit_hit_id' => $this->id]);


            $builder->update(['settled_at' => now()]);
            foreach ($this->twoDigits as $twoDigit) {
                $twoDigit->processPrize();
            }
            TwoDigit::processJackPot($this);
        });
    }
}
