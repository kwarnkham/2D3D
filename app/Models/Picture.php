<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function pictureable()
    {
        return $this->morphTo();
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => env("AWS_URL") . "/" . env("APP_NAME") . "/topup/" . $value,
        );
    }
}
