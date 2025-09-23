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
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->string('size', 50)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('material', 100)->nullable(); // like : polyester, cotton, silk
            $table->decimal('weight', 8, 2)->nullable()->comment('weight in kg');
            $table->decimal('length', 8, 2)->nullable()->comment('Dimensions in cm');
            $table->decimal('width', 8, 2)->nullable()->comment('Dimensions in cm');
            $table->decimal('height', 8, 2)->nullable()->comment('Dimensions in cm');
            $table->string('origin', 100)->nullable(); // country, region like "USA", "EU", "Asia"
            $table->string('quality', 50)->nullable(); // quality level: low, medium, high
            $table->string('packaging', 100)->nullable(); // like : Box, Bag, Bottle
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('min_stock_alert')->default(5); // alert when stock is below this value

            $table->string('sku_variant', 100)->unique()->nullable();
            $table->string('barcode', 100)->nullable(); // barcode like EAN, UPC, etc.

            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index('price');
            $table->index('stock');
            $table->index('is_active');
            $table->index('sku_variant');
            $table->index('barcode');
            $table->index('color');
            $table->index('size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_details');
    }
};
