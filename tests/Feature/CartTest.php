<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Override;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->roleUser()->create();
    }

    public function test_admin_cannot_add_product_to_cart(): void
    {
        $admin = User::factory()->roleAdmin()->create();

        $product = Product::factory()->create();

        $response = $this->actingAs($admin)
                    ->put(route('cart.upsert', ['product' => $product->id]), [
                        'count' => 1
                    ]);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors()
            ->assertRedirectBackWithErrors();
    }

    public function test_user_can_add_cart_item(): void
    {
        $cart = $this->user->cart()->create();

        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)
                    ->put(route('cart.upsert', ['product' => $product->id]), [
                        'count' => 1
                    ]);

        $response
            ->assertSessionDoesntHaveErrors()
            ->assertRedirectBackWithoutErrors();

        $this->assertDatabaseHas(CartItem::class, [
            'quantity' => 1,
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);
    }

    public function test_user_can_delete_cart_item(): void
    {
        $cart = $this->user->cart()->create();

        $product = Product::factory()->create();

        $cartItem = $cart->items()->create([
                        'quantity' => 1,
                        'cart_id' => $cart->id,
                        'product_id' => $product->id
                    ]);

        $response = $this->actingAs($this->user)
                    ->delete(route('cart.destroy', ['cart_item' => $cartItem->id]));

        $response
            ->assertSessionDoesntHaveErrors()
            ->assertRedirectBackWithoutErrors();

        $this->assertDatabaseMissing(CartItem::class, [
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);
    }

    public function test_user_can_update_cart_item(): void
    {
        $cart = $this->user->cart()->create();

        $product = Product::factory()->create();

        $cart->items()->create([
            'quantity' => 1,
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);

        $response = $this->actingAs($this->user)
                    ->put(route('cart.upsert', ['product' => $product->id]), [
                        'count' => 4
                    ]);

        $response
            ->assertSessionDoesntHaveErrors()
            ->assertRedirectBackWithoutErrors();

        $this->assertDatabaseHas(CartItem::class, [
            'quantity' => 4,
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);
    }

    public function test_user_cannot_add_cart_item_quantity_more_than_product_stock(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)
                    ->put(route('cart.upsert', ['product' => $product->id]), [
                        'count' => $product->stock_quantity + 1
                    ]);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors()
            ->assertRedirectBackWithErrors();
    }

    public function test_user_cannot_add_cart_item_quantity_less_than_1(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)
                    ->put(route('cart.upsert', ['product' => $product->id]), [
                        'count' => 0
                    ]);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('count')
            ->assertRedirectBackWithErrors();

        $this->assertDatabaseEmpty(CartItem::class);
    }

    public function test_checkout_creates_order_and_reduces_stock(): void
    {
        $cart = $this->user->cart()->create();

        $product = Product::factory()->create(['stock_quantity' => 10]);

        $cart->items()->create([
            'quantity' => 3,
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);

        $response = $this->actingAs($this->user)
                    ->post(route('cart.checkout'));

        $response
            ->assertStatus(302)
            ->assertSessionDoesntHaveErrors()
            ->assertRedirectBackWithoutErrors();

        $this->assertDatabaseCount(Order::class, 1);

        $this->assertDatabaseHas(Product::class, [
            'id' => $product->id,
            'stock_quantity' => 7
        ]);

        $this->assertDatabaseMissing(CartItem::class, ['cart_id' => $cart->id]);
    }

    public function test_user_cannot_checkout_when_product_stock_insufficient(): void
    {
        $cart = $this->user->cart()->create();

        $product = Product::factory()->create(['stock_quantity' => 5]);

        $cart->items()->create([
            'quantity' => 7,
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);

        $response = $this->actingAs($this->user)
                    ->post(route('cart.checkout'));

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors()
            ->assertRedirectBackWithErrors();

        $this->assertDatabaseEmpty(Order::class);
    }
}
