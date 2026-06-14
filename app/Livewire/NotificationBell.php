<?php

namespace App\Livewire;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    public array $recentNotifications = [];

    public function mount(): void
    {
        $this->refreshNotifications();
    }

    public function refreshNotifications(): void
    {
        $user = Filament::auth()->user();

        if (! $user) {
            $this->unreadCount = 0;
            $this->recentNotifications = [];

            return;
        }

        $this->unreadCount = $user->unreadNotifications()->count();
        $this->recentNotifications = $user->unreadNotifications()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($notification) => [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'Notification',
                'body' => $notification->data['body'] ?? '',
                'icon' => $notification->data['icon'] ?? 'heroicon-o-bell',
                'color' => $notification->data['color'] ?? 'gray',
                'action_url' => $notification->data['action_url'] ?? '#',
                'action_text' => $notification->data['action_text'] ?? 'View',
                'created_at' => $notification->created_at->diffForHumans(),
                'model_type' => $notification->data['model_type'] ?? null,
                'model_id' => $notification->data['model_id'] ?? null,
            ])
            ->toArray();
    }

    public function markAsRead(string $notificationId): void
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return;
        }

        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
        }

        $this->refreshNotifications();
    }

    public function markAllAsRead(): void
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return;
        }

        $user->unreadNotifications()->update(['read_at' => now()]);

        Notification::make()
            ->title('All notifications marked as read.')
            ->success()
            ->send();

        $this->refreshNotifications();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
