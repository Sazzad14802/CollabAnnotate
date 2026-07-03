<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use Illuminate\Support\Collection;

class NotificationBell extends Component
{
    public int $unreadCount = 0;
    public bool $open = false;
    public Collection $notifications;

    public function mount(): void
    {
        $this->notifications = collect();
        $this->loadNotifications();
    }

    public function loadNotifications(): void
    {
        $this->notifications = auth()->user()
            ->notifications()
            ->latest()
            ->limit(15)
            ->get();

        $this->unreadCount = $this->notifications->whereNull('read_at')->count();
    }

    public function toggle(): void
    {
        $this->open = !$this->open;
        if ($this->open) {
            $this->loadNotifications();
        }
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }

    public function markRead(string $id): void
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notifications.notification-bell');
    }
}
