<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // ==================== Set Default Preferences ====================

    public function up(): void
    {
        // ==================== Existing Users ====================

        DB::table('users')
            ->whereNull('notification_preferences')
            ->update([
                'notification_preferences' => json_encode([
                    'messages' => true,
                    'orders' => true,
                ]),
            ]);

        // ==================== Make Column Required ====================

        Schema::table('users', function (Blueprint $table) {
            $table
                ->json('notification_preferences')
                ->nullable(false)
                ->change();
        });
    }

    // ==================== Restore Nullable Column ====================

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table
                ->json('notification_preferences')
                ->nullable()
                ->change();
        });
    }
};
