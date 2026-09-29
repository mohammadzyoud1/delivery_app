<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

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
        // ==================== Check User Preference ====================

        $preferences = $user->notification_preferences ?? [];

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

        // ==================== Create Notification ====================

        $user->notifications()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data ?: null,
        ]);
    }
}
