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

        $cartItemQuantity = 1;
        $response = $this->actingAs($admin)
            ->put("/cart/products/{$product->id}", [
                'count' => $cartItemQuantity
            ]);

        $response->assertRedirectBackWithErrors();
    }

    public function test_user_can_add_cart_item(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $product = Product::factory()->create();

        $cartItemQuantity = 1;
        $response = $this->actingAs($user)
            ->put("/cart/products/{$product->id}", [
                'count' => $cartItemQuantity
            ]);

        $this->assertDatabaseHas(CartItem::class, [
            'quantity' => $cartItemQuantity,
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);
        $response->assertRedirectBackWithoutErrors();
    }

    public function test_user_can_delete_cart_item(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas(Cart::class, [
            'id' => $cart->id,
            'user_id' => $user->id
        ]);

        $product = Product::factory()->create();

        $cartItemQuantity = 1;
        $cartItem = CartItem::factory()
            ->create([
                'quantity' => $cartItemQuantity,
                'cart_id' => $cart->id,
                'product_id' => $product->id
            ]);

        $this->assertDatabaseHas(CartItem::class, [
            'quantity' => $cartItemQuantity,
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);

        $response = $this->actingAs($user)->delete("/cart/items/{$cartItem->id}");

        $this->assertDatabaseMissing(CartItem::class, [
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);

        $response->assertRedirectBackWithoutErrors();
    }

    public function test_user_can_update_cart_item(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $product = Product::factory()->create();

        $cartItemQuantity = 1;
        $cartItem = CartItem::factory()
                    ->create([
                        'quantity' => $cartItemQuantity,
                        'cart_id' => $cart->id,
                        'product_id' => $product->id
                    ]);

        $this->assertDatabaseHas(CartItem::class, [
            'quantity' => $cartItemQuantity,
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);

        $newCartItemQuantity = 4;
        $response = $this->actingAs($user)
            ->put("/cart/products/{$product->id}", [
                'count' => $newCartItemQuantity
            ]);

        $this->assertDatabaseHas(CartItem::class, [
            'quantity' => $newCartItemQuantity,
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);
    }

    public function test_user_cannot_add_cart_item_quantity_more_than_product_stock(): void
    {
        $user = User::factory()
                ->has(Cart::factory(), 'cart')
                ->create(['role' => 'user']);

        $product = Product::factory()->create();

        $cartItemQuantity = $product->stock_quantity + 10;
        $response = $this->actingAs($user)
            ->put("/cart/products/{$product->id}", [
                'count' => $cartItemQuantity
            ]);

        $response
            ->assertSessionHasErrors()
            ->assertRedirectBackWithErrors();
    }

    public function test_user_cannot_add_cart_item_quantity_less_than_1(): void
    {
        $user = User::factory()
                ->has(Cart::factory(), 'cart')
                ->create(['role' => 'user']);

        $product = Product::factory()->create();

        $cartItemQuantity = 0;
        $response = $this->actingAs($user)->put("/cart/products/{$product->id}", [
            'count' => $cartItemQuantity
        ]);

        $response
            ->assertSessionHasErrors()
            ->assertRedirectBackWithErrors();
    }

    public function test_user_can_checkout(): void
    {
        User::factory()->create(['role' => 'admin']);

        $user = User::factory()->create(['role' => 'user']);

        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $productStockQuantity = 6;
        $product = Product::factory()->create(['stock_quantity' => $productStockQuantity]);

        $cartItemQuantity = 3;
        $cartItem = CartItem::factory()
            ->create([
                'quantity' => $cartItemQuantity,
                'cart_id' => $cart->id,
                'product_id' => $product->id
            ]);

        $this->assertDatabaseHas(CartItem::class, [
            'quantity' => $cartItemQuantity,
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);

        $response = $this->actingAs($user)->post('/cart/checkout');

        $this->assertDatabaseCount(Order::class, 1);

        $response->assertRedirectBackWithoutErrors();
    }

    public function test_user_cannot_checkout_when_product_stock_insufficient(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $productStockQuantity = 5;
        $product = Product::factory()->create(['stock_quantity' => $productStockQuantity]);

        $cartItemQuantity = 7;
        $cartItem = CartItem::factory()
            ->create([
                'quantity' => $cartItemQuantity,
                'cart_id' => $cart->id,
                'product_id' => $product->id
            ]);

        $response = $this->actingAs($user)->post('/cart/checkout');

        $this->assertDatabaseEmpty(Order::class);

        $response->assertRedirectBackWithErrors();
    }
}
