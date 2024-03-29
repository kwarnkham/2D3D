<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class PointLog extends AppModel
{
    const RS = ['point_loggable', 'point'];
    use HasFactory;

    public function point_loggable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function note(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => __("messages.$value"),
        );
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['point_id'] ?? false,
            fn ($q, $pointId) => $q->where('point_id', $pointId)
        );

        $query->when(
            $filters['order_in'] ?? false,
            fn ($q, $orderIn) => $q->orderBy('id', $orderIn)
        );
    }

    public function scopeOf($query, User $user)
    {
        $query->where('user_id', $user->id);
    }
}
