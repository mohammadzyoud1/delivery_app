<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\WebPushNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    // ==================== Create Notification ====================

    public function create(
        User $user,
        string $type,
        string $title,
        string $message,
        array $data = [],
    ): void {
        // ==================== Notification Preferences ====================

        $preferences =
            $user->notification_preferences ?? [];

        if (
            $type === 'message' &&
            !($preferences['messages'] ?? true)
        ) {
            return;
        }

        if (
            $type === 'order' &&
            !($preferences['orders'] ?? true)
        ) {
            return;
        }

        // ==================== Save Database Notification ====================

        $user->notifications()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data ?: null,
        ]);

        // ==================== Notification URL ====================

        $url = '/';

        if (
            $type === 'message' &&
            !empty($data['conversation_id'])
        ) {
            $url =
                '/messages/'
                . $data['conversation_id'];
        }

        if (
            $type === 'order' &&
            !empty($data['order_id'])
        ) {
            $url =
                '/orders/'
                . $data['order_id'];
        }

        // ==================== Send Web Push ====================

        // $user->notify(
        //    new WebPushNotification(
        //        $title,
        //        $message,
        //        $url,
        //    ),
        // );
        // ==================== Send Web Push ====================

        try {
            $user->notify(
                new WebPushNotification(
                    $title,
                    $message,
                    $url,
                ),
            );
        } catch (\Throwable $exception) {
            Log::error('WEB PUSH FAILED', [
                'user_id' => $user->id,
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
