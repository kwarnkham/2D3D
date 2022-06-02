<?php

namespace App\Models;

use App\Contracts\PointLogable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Withdraw extends AppModel implements PointLogable
{
    use HasFactory;
    const RS = ['user', 'point', 'pictures', 'payment'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function point_log()
    {
        return $this->morphOne(PointLog::class, 'point_loggable');
    }

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    protected $appends = ['created_time'];

    public function pictures()
    {
        return $this->morphMany(Picture::class, 'pictureable');
    }

    public function savePictures(array $files)
    {
        $pictures = array();
        foreach ($files as $picture) {
            if (env("APP_ENV") == "testing") break;
            $pictures[] = new Picture(['name' => basename(Storage::disk('s3')->putFile(env('APP_NAME') . "/topup", $picture, 'public'))]);
        }
        $this->pictures()->saveMany($pictures);
    }

    /**
     * Scope a query to filter.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['status'] ?? false,
            fn ($q, $status) => $q->where('status', $status)
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

    public function getApproveLink()
    {
        return env('APP_CLIENT_URL') . '/admin/approve-withdraw/' . $this->id;
    }
}
