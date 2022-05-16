<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppSetting extends AppModel
{
    use HasFactory;

    public static function current()
    {
        return AppSetting::orderBy('id', 'desc')->first();
    }

    public function config(): Attribute
    {
        return new Attribute(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }
}
