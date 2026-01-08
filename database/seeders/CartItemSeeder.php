<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::where('stock_quantity', '>', 0)->get();

        Cart::all()->each(function ($cart) use ($products) {
            $cartProducts = $products->random(rand(1, min(5, $products->count())));

            foreach ($cartProducts as $product) {
                CartItem::factory()->create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, min(5, $product->stock_quantity)),
                ]);
            }
        });
    }
}
