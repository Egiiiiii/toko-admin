<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Admin
        \App\Models\User::factory()->create([
            'name' => 'Admin Toko',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        // 2. Buat Data Master
        $brands = Brand::factory(10)->create();
        $categories = Category::factory(10)->create();
        $customers = Customer::factory(50)->create();

        // 3. Buat Products
        foreach (range(1, 50) as $i) {
            Product::factory()->create([
                'brand_id' => $brands->random()->id,
                'category_id' => $categories->random()->id,
            ]);
        }
        
        $products = Product::all();

        // 4. Generate Transaksi
        foreach (range(1, 500) as $i) {
            
            $orderDate = fake()->dateTimeBetween('-1 year', 'now');
            $status = fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']);
            
            $order = Order::create([
                'customer_id' => $customers->random()->id,
                'number' => 'ORD-' . strtoupper(fake()->bothify('??#####')),
                'total_price' => 0,
                'status' => $status,
                'notes' => fake()->sentence(),
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            $totalPrice = 0;
            $itemsCount = rand(1, 5);
            
            for ($j = 0; $j < $itemsCount; $j++) {
                $product = $products->random();
                $qty = rand(1, 3);
                $price = $product->price;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                $totalPrice += ($price * $qty);
            }

            $order->update(['total_price' => $totalPrice]);

            // Fix: Hapus kolom 'status' di sini
            if ($status === 'completed' || $status === 'processing') {
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $totalPrice,
                    'payment_method' => fake()->randomElement(['transfer', 'credit_card', 'ewallet']),
                    // 'status' => 'success', <--- INI SUDAH DIHAPUS
                    'paid_at' => $orderDate,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
            }
        }
    }
}