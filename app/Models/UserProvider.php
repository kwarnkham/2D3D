<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserProvider extends Pivot
{
    use HasFactory;

    protected $table = 'account_provider_user';

    public $incrementing = true;

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
