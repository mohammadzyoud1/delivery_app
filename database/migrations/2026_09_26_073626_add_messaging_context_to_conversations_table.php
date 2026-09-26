<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // ==================== Add Messaging Context ====================

    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table
                ->string('type')
                ->default('support')
                ->after('id');

            $table
                ->foreignId('order_id')
                ->nullable()
                ->after('type')
                ->constrained('orders')
                ->nullOnDelete();

            $table->index(['type', 'order_id']);
        });
    }

    // ==================== Remove Messaging Context ====================

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropIndex(['type', 'order_id']);
            $table->dropColumn(['type', 'order_id']);
        });
    }
};
