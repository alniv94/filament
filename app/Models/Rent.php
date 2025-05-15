<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsToMany, BelongsTo};

class Rent extends Model
{

    protected $fillable = [
        'customer_id',
        'erf_date',
        'erf_number',
        'departure_date',
        'arrival_date',
        'status',
        'notes',
    ];


    public function rentItems(): HasMany
    {
        return $this->hasMany(RentItem::class);
    }

    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
