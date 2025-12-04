<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Make user_id nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();
            
            // Add admin_id column after user_id
            $table->unsignedBigInteger('admin_id')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remove admin_id column
            $table->dropColumn('admin_id');
            
            // Make user_id required again
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
