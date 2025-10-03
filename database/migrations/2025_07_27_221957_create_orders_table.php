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
        Schema::create(
            'orders',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('order_number', 50)->unique(); // Unique order number
                $table->foreignId('promo_code_id')
                    ->nullable()
                    ->constrained('promo_codes')
                    ->nullOnDelete();
                $table->enum('status', ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled', 'refunded'])
                    ->default('pending');
                $table->decimal('total_amount', 12, 2); // Total amount of the order like 100.00
                $table->decimal('shipping_amount', 8, 2)->default(0); // Shipping cost like 10.00
                $table->decimal('shipping_cost', 8, 2); // Shipping cost like 10.00
                $table->decimal('tax_amount', 8, 2)->default(0); // Tax amount like 10.00
                $table->string('phone', 20)->nullable();
                $table->decimal('discount_amount', 8, 2)->default(0);
                $table->char('currency', 3)->default('EGP');
                $table->text('shipping_address');
                $table->text('notes')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamps();

                $table->index('user_id', 'idx_orders_user');
                $table->index('order_number', 'idx_orders_number');
                $table->index('status', 'idx_orders_status');
                $table->index('created_at', 'idx_orders_created');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
