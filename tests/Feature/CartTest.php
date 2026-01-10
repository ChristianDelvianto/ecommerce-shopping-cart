<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_add_product_to_cart(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)
            ->put("/cart/products/{$product->id}", [
                'count' => 1
            ]);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors()
            ->assertRedirectBackWithErrors();
    }

    public function test_user_can_add_cart_item(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();

        $response = $this->actingAs($user)
            ->put("/cart/products/{$product->id}", [
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
        $user = User::factory()->create(['role' => 'user']);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();

        $cartItem = CartItem::factory()
            ->create([
                'quantity' => 1,
                'cart_id' => $cart->id,
                'product_id' => $product->id
            ]);

        $response = $this->actingAs($user)->delete("/cart/items/{$cartItem->id}");
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
        $user = User::factory()->create(['role' => 'user']);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();

        $cartItem = CartItem::factory()
            ->create([
                'quantity' => 1,
                'cart_id' => $cart->id,
                'product_id' => $product->id
            ]);

        $response = $this->actingAs($user)
            ->put("/cart/products/{$product->id}", [
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
        $user = User::factory()->create(['role' => 'user']);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();

        $response = $this->actingAs($user)
            ->put("/cart/products/{$product->id}", [
                'count' => $product->stock_quantity + 1
            ]);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors()
            ->assertRedirectBackWithErrors();
    }

    public function test_user_cannot_add_cart_item_quantity_less_than_1(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();

        $response = $this->actingAs($user)
            ->put("/cart/products/{$product->id}", [
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
        User::factory()->create(['role' => 'admin']);

        $user = User::factory()->create(['role' => 'user']);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock_quantity' => 10]);

        CartItem::factory()
            ->create([
                'quantity' => 3,
                'cart_id' => $cart->id,
                'product_id' => $product->id
            ]);

        $response = $this->actingAs($user)->post('/cart/checkout');
        $response
            ->assertStatus(302)
            ->assertSessionDoesntHaveErrors()
            ->assertRedirectBackWithoutErrors();

        $this
            ->assertDatabaseCount(Order::class, 1)
            ->assertDatabaseHas(Product::class, [
                'id' => $product->id,
                'stock_quantity' => 7
            ])
            ->assertDatabaseMissing(CartItem::class, [
                'cart_id' => $cart->id
            ]);
    }

    public function test_user_cannot_checkout_when_product_stock_insufficient(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock_quantity' => 5]);

        CartItem::factory()
            ->create([
                'quantity' => 7,
                'cart_id' => $cart->id,
                'product_id' => $product->id
            ]);

        $response = $this->actingAs($user)->post('/cart/checkout');
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors()
            ->assertRedirectBackWithErrors();

        $this->assertDatabaseEmpty(Order::class);
    }
}
