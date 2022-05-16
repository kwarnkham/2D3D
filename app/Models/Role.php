<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends AppModel
{
    use HasFactory;


    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
