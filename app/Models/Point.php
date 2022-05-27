<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Point extends AppModel
{
    use HasFactory;
    protected static function booted()
    {
        static::created(function ($point) {
            Cache::forget('points');
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(['balance', 'referrable_balance']);
    }
}
