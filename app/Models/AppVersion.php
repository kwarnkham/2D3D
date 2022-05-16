<?php

namespace App\Models;

use App\Services\TelegramService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppVersion extends AppModel
{
    use HasFactory;


    protected $appends = ['telegram_bot'];
    public function telegramBot(): Attribute
    {
        return new Attribute(
            get: fn () => TelegramService::getLink()
        );
    }
}
