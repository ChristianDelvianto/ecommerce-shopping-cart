<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
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
        $admin = User::factory()
                ->create(['role' => 'admin']);

        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->put("/cart/products/{$product->id}", [
            'count' => 1
        ]);

        $response->assertRedirectBackWithErrors();
    }

    public function test_user_can_add_cart_item(): void
    {
        $user = User::factory()
                ->create(['role' => 'user']);

        $cart = Cart::factory()
                ->create(['user_id' => $user->id]);

        $product = Product::factory()->create();

        $quantity = 1;

        $response = $this->actingAs($user)->put("/cart/products/{$product->id}", [
            'count' => $quantity
        ]);

        $this->assertDatabaseHas(CartItem::class, [
            'quantity' => $quantity,
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);
        $response->assertRedirectBackWithoutErrors();
    }

    public function test_user_can_delete_cart_item(): void
    {
        $user = User::factory()
                ->create(['role' => 'user']);

        $cart = Cart::factory()
                ->create(['user_id' => $user->id]);

        $this->assertDatabaseHas(Cart::class, [
            'id' => $cart->id,
            'user_id' => $user->id
        ]);

        $product = Product::factory()
                    ->create();

        $quantity = 1;

        $cartItem = CartItem::factory()
                    ->create([
                        'quantity' => $quantity,
                        'cart_id' => $cart->id,
                        'product_id' => $product->id
                    ]);

        $this->assertDatabaseHas(CartItem::class, [
            'quantity' => $quantity,
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
        $user = User::factory()
                ->create(['role' => 'user']);

        $cart = Cart::factory()
                ->create(['user_id' => $user->id]);

        $product = Product::factory()
                    ->create();

        $quantity = 1;

        $cartItem = CartItem::factory()
                    ->create([
                        'quantity' => $quantity,
                        'cart_id' => $cart->id,
                        'product_id' => $product->id
                    ]);

        $this->assertDatabaseHas(CartItem::class, [
            'quantity' => $quantity,
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);

        $newQuantity = 4;

        $response = $this->actingAs($user)->put("/cart/products/{$product->id}", [
            'count' => $newQuantity
        ]);

        $this->assertDatabaseHas(CartItem::class, [
            'quantity' => $newQuantity,
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);
    }

    public function test_user_cannot_add_quantity_more_than_product_stock(): void
    {
        $user = User::factory()
                ->has(Cart::factory(), 'cart')
                ->create(['role' => 'user']);

        $product = Product::factory()->create();

        $quantity = $product->stock_quantity + 10;

        $response = $this->actingAs($user)->put("/cart/products/{$product->id}", [
            'count' => $quantity
        ]);

        $response->assertSessionHasErrors();
        $response->assertRedirectBackWithErrors();
    }

    public function test_user_cannot_add_quantity_less_than_1(): void
    {
        $user = User::factory()
                ->has(Cart::factory(), 'cart')
                ->create(['role' => 'user']);

        $product = Product::factory()->create();

        $quantity = 0;

        $response = $this->actingAs($user)->put("/cart/products/{$product->id}", [
            'count' => $quantity
        ]);

        $response->assertSessionHasErrors();
        $response->assertRedirectBackWithErrors();
    }

    public function test_can_create_order(): void
    {
        User::factory()
        ->create(['role' => 'admin']);

        $user = User::factory()
                ->create(['role' => 'user']);

        $cart = Cart::factory()
                ->create(['user_id' => $user->id]);

        $productStockQuantity = 6;
        $product = Product::factory()
                    ->create(['stock_quantity' => $productStockQuantity]);

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

        $response->assertRedirectBackWithoutErrors();
    }

    public function test_cannot_create_order_when_product_stock_insufficient(): void
    {
        $user = User::factory()
                ->create(['role' => 'user']);

        $cart = Cart::factory()
                ->create(['user_id' => $user->id]);

        $productStockQuantity = 5;
        $product = Product::factory()
                    ->create(['stock_quantity' => $productStockQuantity]);

        $cartItemQuantity = 7;
        $cartItem = CartItem::factory()
                    ->create([
                        'quantity' => $cartItemQuantity,
                        'cart_id' => $cart->id,
                        'product_id' => $product->id
                    ]);

        $response = $this->actingAs($user)->post('/cart/checkout');

        $response->assertRedirectBackWithErrors();
    }
}
