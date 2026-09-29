<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // ==================== Add Remember Token ====================

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->rememberToken();
        });
    }

    // ==================== Remove Remember Token ====================

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropRememberToken();
        });
    }
};
