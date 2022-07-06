<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

abstract class AppModel extends Model
{

    protected $guarded = ["id"];

    protected function serializeDate(DateTimeInterface $date)
    {
        return (new Carbon($date))->diffForHumans();
    }

    public function createdTime(): Attribute
    {
        return new Attribute(
            get: fn () => $this->created_at->timestamp,
        );
    }

    public function updatedTime(): Attribute
    {
        return new Attribute(
            get: fn () => $this->updated_at->timestamp,
        );
    }
}
