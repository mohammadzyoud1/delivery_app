<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class WebPushNotification extends Notification
{
    // ==================== Properties ====================

    public function __construct(
        private string $title,
        private string $message,
        private string $url,
    ) {}

    // ==================== Notification Channels ====================

    public function via($notifiable): array
    {
        return [
            WebPushChannel::class,
        ];
    }

    // ==================== Web Push Message ====================

    public function toWebPush(
        $notifiable,
        $notification,
    ): WebPushMessage {
        return (new WebPushMessage)
            ->title($this->title)
            ->body($this->message)
            ->icon('/pwa-192x192.png')
            ->badge('/pwa-192x192.png')
            ->data([
                'url' => $this->url,
            ]);
    }
}
