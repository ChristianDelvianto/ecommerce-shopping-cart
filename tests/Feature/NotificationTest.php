<?php

namespace Tests\Feature;

use App\Jobs\NotifyLowStockQuantity;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_notification_pushed(): void
    {
        Queue::fake();

        User::factory()->create(['role' => 'admin']);

        $user = User::factory()->create(['role' => 'user']);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock_quantity' => 6]);

        $cartItem = CartItem::factory()
            ->create([
                'quantity' => 3,
                'cart_id' => $cart->id,
                'product_id' => $product->id
            ]);

        $this->actingAs($user)->post('/cart/checkout');
        $this->assertDatabaseHas(Product::class, [
            'id' => $product->id,
            'stock_quantity' => 3
        ]);

        Queue::assertPushed(NotifyLowStockQuantity::class);
    }
}
