<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // ==================== Add Variant Snapshot ====================

    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('variant_name')->nullable()->after('variant_id');
        });
    }

    // ==================== Remove Variant Snapshot ====================

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('variant_name');
        });
    }
};
