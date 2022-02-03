<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountProvider extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->using(UserProvider::class);
    }
}
