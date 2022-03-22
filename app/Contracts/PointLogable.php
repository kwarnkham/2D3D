<?php

namespace App\Contracts;

interface PointLogable
{
    public function point_logs();
    // {
    //     return $this->morphMany(PointLog::class, 'point_logable');
    // }
}
