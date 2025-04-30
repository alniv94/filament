<?php

namespace App\Observers;

use App\Models\Equipment;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;

class EquipmentMaintenanceObserver
{
    /**
     * Handle the Equipment "created" event.
     */
    public function created(Equipment $equipment): void
    {
        $this->checkMaintenanceStatus($equipment);
    }

    /**
     * Handle the Equipment "updated" event.
     */
    public function updated(Equipment $equipment): void
    {
        if ($equipment->isDirty('next_maintenance_date')) {
            $equipment->updateRemainingDays();
            $this->checkMaintenanceStatus($equipment);
        }
    }

    /**
     * Handle the Equipment "deleted" event.
     */
    public function deleted(Equipment $equipment): void
    {
        //
    }

    /**
     * Handle the Equipment "restored" event.
     */
    public function restored(Equipment $equipment): void
    {
        $this->checkMaintenanceStatus($equipment);
    }

    /**
     * Handle the Equipment "force deleted" event.
     */
    public function forceDeleted(Equipment $equipment): void
    {
        //
    }

    /**
     * Check maintenance status and send notifications if needed
     */
    private function checkMaintenanceStatus(Equipment $equipment): void
    {
        if ($equipment->next_maintenance_date) {
            if ($equipment->isMaintenanceDueSoon() || $equipment->isMaintenanceOverdue()) {
                $users = User::all();

                $status = $equipment->isMaintenanceOverdue() ? 'overdue' : 'due soon';

                foreach ($users as $user) {
                    FilamentNotification::make()
                        ->title('Equipment Maintenance ' . ucfirst($status))
                        ->body('Equipment ' . $equipment->equipment_number . ' is ' . $status . ' for maintenance.')
                        ->warning()
                        ->sendToDatabase($user);
                }
            }
        }
    }
}
