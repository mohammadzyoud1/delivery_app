<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // ==================== Make Total Amount Nullable ====================

    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->nullable()->change();
        });
    }

    // ==================== Restore Required Total Amount ====================

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->nullable(false)->change();
        });
    }
};
