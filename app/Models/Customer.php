<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Customer extends Model
{

    protected $fillable = ['code', 'name', 'address'];

    public function contactPeople(): MorphMany
    {
        return $this->morphMany(ContactPeople::class, 'contactable');
    }
}
