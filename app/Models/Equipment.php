<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Notifications\Notifiable;
use App\Observers\EquipmentMaintenanceObserver;

#[ObservedBy(EquipmentMaintenanceObserver::class)]

class Equipment extends Model
{
    use HasFactory, Notifiable;

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

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function calculateRemainingDays(): int
    {
        if (!$this->next_maintenance_date) {
            return 0;
        }

        return Carbon::now()->diffInDays($this->next_maintenance_date, false);
    }

    public function updateRemainingDays(): void
    {
        $this->remaining_days_for_maintenance = $this->calculateRemainingDays();
        $this->saveQuietly();
    }

    public function isMaintenanceDueSoon(): bool
    {
        $remainingDays = $this->calculateRemainingDays();
        return $remainingDays >= 0 && $remainingDays <= 7;
    }

    public function isMaintenanceOverdue(): bool
    {
        return $this->calculateRemainingDays() < 0;
    }
}
