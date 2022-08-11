<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Picture extends AppModel
{
    use HasFactory;

    public function pictureable()
    {
        return $this->morphTo();
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => config('filesystems')['disks']['s3']['url'] . "/" . config('app')['name'] . "/topup/" . $value,
        );
    }
}
