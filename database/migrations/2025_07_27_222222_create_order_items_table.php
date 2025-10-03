<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_detail_id')
                ->constrained('product_details')
                ->onDelete('restrict'); // restrict deletion if there's an order item referencing it
            $table->string('product_name', 255);
            $table->string('product_sku', 100)->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2); // the price of one unit of the product
            $table->decimal('total_price', 10, 2);
            $table->timestamps();

            $table->index('order_id', 'idx_order_items_order');
            $table->index('product_detail_id', 'idx_order_items_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
