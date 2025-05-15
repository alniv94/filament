<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class RentItem extends Model
{
    protected $fillable = [
        'rent_type',
        'rate',
        'quantity',
        'estimated_discount',
        'total_amount',
        'status',
        'equipment_id',
        'rent_id',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function rent(): BelongsTo
    {
        return $this->belongsTo(Rent::class);
    }
}
