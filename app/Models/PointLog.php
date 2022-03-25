<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PointLog extends Model
{
    const RS = ['point_loggable', 'point'];
    use HasFactory;

    protected $guarded = ['id'];

    public function point_loggable()
    {
        return $this->morphTo();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return (new Carbon($date))->diffForHumans();
    }

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['point_id'] ?? false,
            fn ($q, $pointId) => $q->where('point_id', $pointId)
        );
    }

    public function scopeOf($query, User $user)
    {
        $query->where('user_id', $user->id);
    }
}
