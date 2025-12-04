<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('category',
            [
                'Processors',
                'Motherboards',
                'Graphics Cards',
                'Memory & Storage',
                'Power & Cooling',
                'Peripherals & Accessories',
                'Cases & Builds',
                'Mod Zone'
            ])
                ->after('image')
                ->default('Mod Zone');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
