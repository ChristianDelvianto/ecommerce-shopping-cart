<?php

namespace Tests\Feature;

use App\Jobs\NotifyLowStockQuantity;
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
        Queue::fake([
            NotifyLowStockQuantity::class
        ]);

        $user = User::factory()->roleUser()->create();
        $cart = $user->cart()->create();

        $product = Product::factory()->create(['stock_quantity' => 6]);

        $cart->items()->create([
            'quantity' => 3,
            'product_id' => $product->id
        ]);

        $this->actingAs($user)
            ->post(route('cart.checkout'));

        $this->assertDatabaseHas(Product::class, [
            'id' => $product->id,
            'stock_quantity' => 3
        ]);

        Queue::assertPushed(NotifyLowStockQuantity::class, function ($job) use ($product) {
            return $job->product->id === $product->id;
        });
    }
}
