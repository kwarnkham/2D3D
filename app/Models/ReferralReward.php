<?php

namespace App\Models;

use App\Contracts\PointLogable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReferralReward extends AppModel implements PointLogable
{
    use HasFactory;

    const RS = ['point', 'referee'];
    protected $appends = ['created_time'];
    public function point_log()
    {
        return $this->morphOne(PointLog::class, 'point_loggable');
    }

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function referee()
    {
        return $this->belongsTo(User::class, "referee_id");
    }
}
