<?php

namespace App\Models;

use App\Contracts\PointLogable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class JackpotReward extends AppModel implements PointLogable
{
    use HasFactory;

    public function twoDigits()
    {
        return $this->hasMany(TwoDigit::class);
    }

    public function point_log()
    {
        return $this->morphOne(PointLog::class, 'point_loggable');
    }
}
