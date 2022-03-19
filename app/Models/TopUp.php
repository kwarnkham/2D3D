<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TopUp extends Model
{
    use HasFactory;
    const RS = ['pictures', 'user', 'payment'];
    protected $guarded = ['id'];

    public function pictures()
    {
        return $this->morphMany(Picture::class, 'pictureable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function savePictures(array $files)
    {
        $pictures = array();
        foreach ($files as $picture) {
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
    }
}
