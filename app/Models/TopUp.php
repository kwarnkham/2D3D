<?php

namespace App\Models;

use App\Contracts\PointLogable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class TopUp extends AppModel implements PointLogable
{
    use HasFactory;
    const RS = ['pictures', 'user', 'payment', 'approved_top_up'];

    public function pictures()
    {
        return $this->morphMany(Picture::class, 'pictureable');
    }

    public function approved_top_up()
    {
        return $this->hasOne(ApprovedTopUp::class);
    }

    public function point_log()
    {
        return $this->morphOne(PointLog::class, 'point_loggable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    protected $appends = ['created_time'];

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
        return env('APP_CLIENT_URL') . '/admin/approve-top-up/' . $this->id;
    }
}
