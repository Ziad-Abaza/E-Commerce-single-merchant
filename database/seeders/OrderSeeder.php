<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductDetail;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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

        // Create orders for the last 6 months
        for ($i = 0; $i < 50; $i++) {
            $this->createRandomOrder($users, $productDetails);
        }
    }

    private function createRandomOrder($users, $productDetails): void
    {
        $user = $users->random();
        $orderDate = now()->subDays(rand(0, 180)); // Random date in last 6 months
        
        // Generate order number
        $orderNumber = 'ORD-' . strtoupper(Str::random(8));
        
        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => $orderNumber,
            'status' => $this->getRandomOrderStatus($orderDate),
            'tax_amount' => 0, // Will be calculated
            'shipping_amount' => $this->getRandomShippingAmount(),
            'shipping_cost' => $this->getRandomShippingAmount(),
            'discount_amount' => $this->getRandomDiscountAmount(),
            'total_amount' => 0, // Will be calculated
            'currency' => 'USD',
            'shipping_address' => $this->generateShippingAddress(),
            'notes' => $this->getRandomOrderNotes(),
            'payment_method' => $this->getRandomPaymentMethod(),
            'payment_status' => 'pending',
            'created_at' => $orderDate,
            'updated_at' => $orderDate,
        ]);

        // Create order items
        $itemCount = rand(1, 5); // 1-5 items per order
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
        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount + $order->shipping_amount - $order->discount_amount;

        // Update order with calculated totals
        $order->update([
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);

        // Create payment record
        $this->createPaymentRecord($order, $orderDate);
    }

    private function getRandomOrderStatus($orderDate): string
    {
        $daysSinceOrder = now()->diffInDays($orderDate);
        
        // Status probabilities based on order age
        if ($daysSinceOrder < 1) {
            return 'pending';
        } elseif ($daysSinceOrder < 3) {
            return rand(0, 1) ? 'pending' : 'processing';
        } elseif ($daysSinceOrder < 7) {
            $statuses = ['processing', 'shipped'];
            return $statuses[array_rand($statuses)];
        } elseif ($daysSinceOrder < 14) {
            $statuses = ['shipped', 'delivered'];
            return $statuses[array_rand($statuses)];
        } else {
            // Older orders are mostly delivered, some cancelled
            $statuses = ['delivered', 'delivered', 'delivered', 'cancelled'];
            return $statuses[array_rand($statuses)];
        }
    }

    private function getRandomShippingAmount(): float
    {
        $amounts = [0, 9.99, 14.99, 19.99]; // Free shipping, standard, express, overnight
        $weights = [40, 30, 20, 10]; // Probability weights
        
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($amounts as $index => $amount) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $amount;
            }
        }
        
        return 9.99; // Default
    }

    private function getRandomDiscountAmount(): float
    {
        // 70% chance of no discount, 30% chance of discount
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

    private function generateBillingAddress(): string
    {
        // Sometimes billing address is same as shipping
        if (rand(0, 1)) {
            return $this->generateShippingAddress();
        }
        
        // Different billing address
        $addresses = [
            '100 Business Blvd, Suite 200, New York, NY 10002',
            '200 Corporate Center, Los Angeles, CA 90211',
            '300 Office Park, Chicago, IL 60602',
            '400 Executive Plaza, Houston, TX 77002',
            '500 Commerce Street, Phoenix, AZ 85002',
        ];
        
        return $addresses[array_rand($addresses)];
    }

    private function getRandomPaymentMethod(): string
    {
        $methods = ['credit_card', 'debit_card', 'paypal', 'apple_pay', 'google_pay'];
        return $methods[array_rand($methods)];
    }

    private function getRandomOrderNotes(): ?string
    {
        $notes = [
            null, // No notes
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

    private function createPaymentRecord(Order $order, $orderDate): void
    {
        $paymentMethods = ['credit_card', 'debit_card', 'paypal', 'apple_pay', 'google_pay'];
        $paymentStatuses = ['completed', 'pending', 'failed'];
        
        $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
        $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
        
        // If order is delivered or shipped, payment should be completed
        if (in_array($order->status, ['delivered', 'shipped'])) {
            $paymentStatus = 'completed';
        }
        
        Payment::create([
            'order_id' => $order->id,
            'payment_method' => $paymentMethod,
            'status' => $paymentStatus,
            'amount' => $order->total_amount,
            'currency' => 'USD',
            'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
            'paid_at' => $paymentStatus === 'completed' ? $orderDate : null,
        ]);
    }
}
