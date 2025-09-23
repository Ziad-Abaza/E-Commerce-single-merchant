<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('is_active', true)->get();
        $productDetails = ProductDetail::where('is_active', true)->where('stock', '>', 0)->get();

        if ($users->isEmpty() || $productDetails->isEmpty()) {
            $this->command->warn('No users or product details found. Please run UserSeeder and ProductDetailSeeder first.');
            return;
        }

        // Create 50 random orders
        for ($i = 0; $i < 50; $i++) {
            $this->createRandomOrder($users, $productDetails);
        }
    }

    private function createRandomOrder($users, $productDetails): void
    {
        $user = $users->random();

        // Random date in last 6 months, formatted for MySQL
        $orderDate = Carbon::now()->subDays(rand(0, 180))->format('Y-m-d H:i:s');

        $orderNumber = 'ORD-' . strtoupper(Str::random(8));

        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => $orderNumber,
            'status' => $this->getRandomOrderStatus($orderDate),
            'tax_amount' => 0, // will update later
            'shipping_amount' => $this->getRandomShippingAmount(),
            'shipping_cost' => $this->getRandomShippingAmount(),
            'discount_amount' => $this->getRandomDiscountAmount(),
            'total_amount' => 0, // will update later
            'currency' => 'USD',
            'shipping_address' => $this->generateShippingAddress(),
            'notes' => $this->getRandomOrderNotes(),
            'created_at' => $orderDate,
            'updated_at' => $orderDate,
        ]);

        // Create order items
        $itemCount = rand(1, 5);
        $subtotal = 0;

        for ($j = 0; $j < $itemCount; $j++) {
            $productDetail = $productDetails->random();
            $quantity = rand(1, 3);
            $price = $productDetail->price;
            $itemTotal = $price * $quantity;

            OrderItem::create([
                'order_id' => $order->id,
                'product_detail_id' => $productDetail->id,
                'product_name' => $productDetail->product->name,
                'product_sku' => $productDetail->sku_variant,
                'quantity' => $quantity,
                'unit_price' => $price,
                'total_price' => $itemTotal,
            ]);

            $subtotal += $itemTotal;
        }

        // Calculate totals
        $taxRate = 0.08; // 8% tax
        $taxAmount = round($subtotal * $taxRate, 2);
        $totalAmount = round($subtotal + $taxAmount + $order->shipping_amount - $order->discount_amount, 2);

        // Update order with calculated totals
        $order->update([
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);
    }

    private function getRandomOrderStatus($orderDate): string
    {
        $daysSinceOrder = Carbon::now()->diffInDays(Carbon::parse($orderDate));

        if ($daysSinceOrder < 1) {
            return 'pending';
        } elseif ($daysSinceOrder < 3) {
            return rand(0, 1) ? 'pending' : 'confirmed';
        } elseif ($daysSinceOrder < 7) {
            $statuses = ['confirmed', 'shipped'];
            return $statuses[array_rand($statuses)];
        } elseif ($daysSinceOrder < 14) {
            $statuses = ['shipped', 'delivered'];
            return $statuses[array_rand($statuses)];
        } else {
            $statuses = ['delivered', 'delivered', 'delivered', 'cancelled'];
            return $statuses[array_rand($statuses)];
        }
    }

    private function getRandomShippingAmount(): float
    {
        $amounts = [0, 9.99, 14.99, 19.99];
        $weights = [40, 30, 20, 10];
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($amounts as $index => $amount) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $amount;
            }
        }
        return 9.99;
    }

    private function getRandomDiscountAmount(): float
    {
        if (rand(1, 10) <= 7) {
            return 0;
        }
        $discounts = [5.00, 10.00, 15.00, 20.00, 25.00, 50.00];
        return $discounts[array_rand($discounts)];
    }

    private function generateShippingAddress(): string
    {
        $addresses = [
            '123 Main Street, Apt 4B, New York, NY 10001',
            '456 Oak Avenue, Los Angeles, CA 90210',
            '789 Pine Road, Chicago, IL 60601',
            '321 Elm Street, Houston, TX 77001',
            '654 Maple Drive, Phoenix, AZ 85001',
            '987 Cedar Lane, Philadelphia, PA 19101',
            '147 Birch Boulevard, San Antonio, TX 78201',
            '258 Spruce Court, San Diego, CA 92101',
            '369 Willow Way, Dallas, TX 75201',
            '741 Poplar Place, San Jose, CA 95101',
            '852 Ash Avenue, Austin, TX 78701',
            '963 Hickory Hill, Jacksonville, FL 32201',
            '159 Cherry Circle, Fort Worth, TX 76101',
            '357 Walnut Walk, Columbus, OH 43201',
            '468 Sycamore Street, Charlotte, NC 28201',
        ];

        return $addresses[array_rand($addresses)];
    }

    private function getRandomOrderNotes(): ?string
    {
        $notes = [
            null,
            'Please leave package at front door',
            'Call before delivery',
            'Leave with neighbor if not home',
            'Fragile - handle with care',
            'Gift wrapping requested',
            'Rush order - please expedite',
            'Special delivery instructions',
            'Leave at back door',
            'Call upon arrival',
        ];

        return $notes[array_rand($notes)];
    }
}
