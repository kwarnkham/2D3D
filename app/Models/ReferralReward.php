<?php

namespace App\Models;

use App\Contracts\PointLogable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralReward extends Model implements PointLogable
{
    use HasFactory;

    protected $guarded = ['id'];

    public function point_log()
    {
        return $this->morphOne(PointLog::class, 'point_loggable');
    }
}
