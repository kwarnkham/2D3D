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
            $today = new Carbon(explode(" ", $this->day)[0]);
            if ($this->morning) {
                $builder = TwoDigit::where('created_at', '<=', $today->addMinutes(TwoDigit::MORNING_LAST_MINUTE))
                    ->where('created_at', '>=', $today->subDay()->addMinutes(TwoDigit::EVENING_LAST_MINUTE + 60))
                    ->whereNull('settled_at');
            } else {
                $builder = TwoDigit::where('created_at', '>=', $today->addMinutes(TwoDigit::MORNING_LAST_MINUTE + 60))
                    ->where('created_at', '<=', $today->addMinutes(TwoDigit::EVENING_LAST_MINUTE))
                    ->whereNull('settled_at');
            }

            $builder->where('number', $this->number)->update(['two_digit_hit_id' => $this->id]);
            $builder->update(['settled_at' => now()]);
        });
    }
}
