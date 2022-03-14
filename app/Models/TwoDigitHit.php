<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TwoDigitHit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function twoDigits()
    {
        return $this->hasMany(TwoDigit::class);
    }

    public function settle()
    {
        return DB::transaction(function () {
            $morningLastMinute = (new Carbon(explode(" ", $this->day)[0]))->addMinutes(TwoDigit::MORNING_LAST_MINUTE)->addSeconds(59);
            $morningStartMinute = (new Carbon(explode(" ", $this->day)[0]))->subDay()->addMinutes(TwoDigit::EVENING_LAST_MINUTE + 60);
            $eveningStartMinute = (new Carbon(explode(" ", $this->day)[0]))->addMinutes(TwoDigit::MORNING_LAST_MINUTE + 60);
            $eveningLastMinute = (new Carbon(explode(" ", $this->day)[0]))->addMinutes(TwoDigit::EVENING_LAST_MINUTE)->addSeconds(59);

            if ($this->morning) {
                $builder = TwoDigit::where('created_at', '<=', $morningLastMinute)
                    ->where('created_at', '>=', $morningStartMinute)
                    ->whereNull('settled_at');
            } else {
                $builder = TwoDigit::where('created_at', '>=', $eveningStartMinute)
                    ->where('created_at', '<=', $eveningLastMinute)
                    ->whereNull('settled_at');
            }
            (clone $builder)->where('number', $this->number)->update(['two_digit_hit_id' => $this->id]);
            $builder->update(['settled_at' => now()]);
            foreach ($this->twoDigits as $twoDigit) {
                $twoDigit->processPrize();
            }
        });
    }
}
