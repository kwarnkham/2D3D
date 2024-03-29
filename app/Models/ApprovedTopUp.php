<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class ApprovedTopUp extends AppModel
{
    use HasFactory;
    protected $with = ['picture'];

    public function picture()
    {
        return $this->morphOne(Picture::class, 'pictureable');
    }

    public function top_up()
    {
        return $this->belongsTo(TopUp::class);
    }

    public function savePicture($picture)
    {
        if (config('app')['env'] == "testing") return;
        $saved = new Picture(['name' => basename(Storage::disk('s3')->putFile(config('app')['name'] . "/topup", $picture, 'public'))]);
        $this->picture()->save($saved);
    }
}
