<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::where('stock_quantity', '>', 0)
                           ->get();

        User::all()->each(function ($user) use ($products) {
            $ordersCount = rand(0, 3);
            
            $orders = Order::factory($ordersCount)->create([
                'user_id' => $user->id,
                'status' => 'completed',
            ]);

            foreach ($orders as $order) {
                $orderProducts = $products->random(rand(1, min(5, $products->count())));

                $subtotal = 0;

                foreach ($orderProducts as $product) {
                    $quantity = rand(1, min(5, $product->stock_quantity));

                    $totalPrice = $product->price * $quantity;

                    OrderItem::factory()->create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_name' => $product->name,
                        'unit_price' => $product->price,
                        'total_price' => $totalPrice
                    ]);

                    $product->decrement('stock_quantity', $quantity);

                    $subtotal += $totalPrice;
                }

                $order->update(['subtotal_amount' => $subtotal]);
            }
        });
    }
}
