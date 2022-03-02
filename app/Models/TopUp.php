<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TopUp extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function pictures()
    {
        return $this->morphMany(Picture::class, 'pictureable');
    }

    public function savePictures(array $files)
    {
        $pictures = array();
        foreach ($files as $picture) {
            $pictures[] = new Picture(['name' => basename(Storage::disk('s3')->putFile(env('APP_NAME') . "/topup", $picture, 'public'))]);
        }
        $this->pictures()->saveMany($pictures);
    }
}
