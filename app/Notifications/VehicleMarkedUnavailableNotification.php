<?php

namespace App\Notifications;

use App\Models\Vehicle;
use App\Notifications\Concerns\HasNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class VehicleMarkedUnavailableNotification extends Notification implements ShouldQueue
{
    use HasNotificationPreferences, Queueable;

    public function __construct(
        public Vehicle $vehicle,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->resolveViaChannels($notifiable, ['database']);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'vehicle_unavailable',
            'title' => "Vehicle Unavailable - {$this->vehicle->brand} {$this->vehicle->model}",
            'body' => "{$this->vehicle->brand} {$this->vehicle->model} ({$this->vehicle->plate_number}) has been marked as unavailable.",
            'icon' => 'heroicon-o-truck',
            'color' => 'danger',
            'action_url' => "/agency/vehicles/{$this->vehicle->id}/edit",
            'action_text' => 'View Vehicle',
            'model_type' => 'vehicle',
            'model_id' => $this->vehicle->id,
        ];
    }
}
