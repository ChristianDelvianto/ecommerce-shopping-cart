<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Queue\Queueable;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_index_shows_only_products_with_stock(): void
    {
        $this->withoutVite();

        $user = User::factory()->create();
        $inStockProduct = Product::factory()->create(['stock_quantity' => 100]);
        Product::factory()->create(['stock_quantity' => 0]);

        $response = $this->actingAs($user)->get('/products', [
            'X-Inertia' => 'true',
            'Accept' => 'application/json',
        ]);
        $response
            ->assertOk()
            ->assertJsonPath('component', 'Products/ProductList')
            ->assertJsonCount(1, 'props.products.data')
            ->assertJsonPath('props.products.data.0.id', $inStockProduct->id);
    }

    public function test_product_show_loads_product_and_recommended_products(): void
    {
        $this->withoutVite();

        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 5]);

        Product::factory()->count(3)->create(['stock_quantity' => 10]);

        $response = $this->actingAs($user)->get("/products/{$product->id}", [
            'X-Inertia' => 'true',
            'Accept' => 'application/json',
        ]);
        $response
            ->assertOk()
            ->assertJsonPath('component', 'Products/ProductShow')
            ->assertJsonPath('props.product.id', $product->id)
            ->assertJsonCount(3, 'props.recommended');
    }

    public function test_product_show_does_not_include_out_of_stock_recommendations(): void
    {
        $this->withoutVite();

        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 5]);

        Product::factory()->create(['stock_quantity' => 0]);
        Product::factory()->create(['stock_quantity' => 10]);

        $response = $this->actingAs($user)->get("/products/{$product->id}", [
            'X-Inertia' => 'true',
            'Accept' => 'application/json',
        ]);

        $recommended = collect($response->json('props.recommended'));

        $this->assertTrue($recommended->every(fn ($p) => $p['stock_quantity'] > 0));
    }
}
