<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountProvider extends AppModel
{
    use HasFactory;

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->using(UserProvider::class)->withPivot(['provider_id', 'username', 'sent_at']);
    }
}
