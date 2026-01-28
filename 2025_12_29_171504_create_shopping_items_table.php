<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shopping_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('price');
            $table->string('original_price')->nullable();
            $table->string('platform'); // lazada, shopee, etc
            $table->text('product_link');
            $table->text('image_url')->nullable();
            $table->enum('status', ['wishlist', 'in_cart', 'purchased', 'removed'])->default('wishlist');
            $table->boolean('price_alert')->default(false);
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('shopping_items');
    }
};