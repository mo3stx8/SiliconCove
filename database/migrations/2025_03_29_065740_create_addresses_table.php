<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('address');
            $table->string('area');
            $table->string('region');
            // $table->string('zip_code');
            $table->timestamps();
        });
    }
    

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
