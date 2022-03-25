<?php

namespace App\Contracts;

interface PointLogable
{
    public function point_log();
    // {
    //     return $this->morphOne(PointLog::class, 'point_loggable');
    // }

}
