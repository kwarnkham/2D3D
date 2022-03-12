<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        $time = now();
        TwoDigit::where('number', $this->number)
            ->whereNull('settled_at')
            ->update(['two_digit_hit_id' => $this->id]);
        //todo more thoughts on the date
        TwoDigit::whereDate('created_at', $time)
            ->whereNull('settled_at')
            ->update(['settled_at' => $time]);
    }
}
