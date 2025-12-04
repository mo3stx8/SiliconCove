<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Note: Enum changes aren't directly supported in some DBs (like MySQL) without raw SQL
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'approved', 'in progress', 'delivered', 'cancelled', 'rejected') DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'approved', 'cancelled', 'rejected') DEFAULT 'pending'");
        });
    }
};
