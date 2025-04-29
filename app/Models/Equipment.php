<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Status;

class Equipment extends Model
{
    protected $fillable = [
        'equipment_number',
        'plate_number',
        'model_name',
        'description',
        'date_purchased',
        'cost',
        'last_maintenance_date',
        'next_maintenance_date',
        'remaining_days_for_maintenance',
        'fuel_consumption_number',
        'size_number',
        'capacity_max',
        'capacity_tip',
        'acel_rate_dry',
        'acel_rate_hour',
        'nsjbi_rate_dry',
        'nsjbi_rate_hour',
        'bare_month',
        'per_trip',
        'est_repair_cost',
        'remarks',
        'date_issued',
        'status',
        'brand_id',
    ];

    protected $casts = [
        'status' => Status::class,
        'date_purchased' => 'datetime',
        'last_maintenance_date' => 'datetime',
        'next_maintenance_date' => 'datetime',
        'date_issued' => 'datetime',
        'cost' => 'decimal:2',
        'fuel_consumption_number' => 'decimal:2',
        'size_number' => 'decimal:2',
        'capacity_max' => 'decimal:2',
        'capacity_tip' => 'decimal:2',
        'acel_rate_dry' => 'decimal:2',
        'acel_rate_hour' => 'decimal:2',
        'nsjbi_rate_dry' => 'decimal:2',
        'nsjbi_rate_hour' => 'decimal:2',
        'bare_month' => 'decimal:2',
        'per_trip' => 'decimal:2',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
