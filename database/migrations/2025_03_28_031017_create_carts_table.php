<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who owns the cart
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Product added to cart
            $table->integer('quantity')->default(1); // Quantity of the product
            $table->decimal('total_price', 10, 2)->default(0.00); // Total price of the item in cart
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('carts');
    }
};
