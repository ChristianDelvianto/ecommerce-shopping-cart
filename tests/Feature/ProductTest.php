<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Override;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->user = User::factory()->roleUser()->create();
    }

    public function test_product_index_shows_only_products_with_stock(): void
    {
        $inStockProduct = Product::factory()->create(['stock_quantity' => 100]);

        // Create product with no stock
        Product::factory()->emptyStock()->create();

        $response = $this->actingAs($this->user)
                    ->get(route('products.index'), [
                        'X-Inertia' => true,
                        'Accept' => 'application/json'
                    ]);

        $response
            ->assertOk()
            ->assertJsonPath('component', 'Products/ProductList')
            ->assertJsonCount(1, 'props.products.data')
            ->assertJsonPath('props.products.data.0.id', $inStockProduct->id);
    }

    public function test_product_show_loads_product_and_recommended_products(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 5]);

        Product::factory(3, ['stock_quantity' => 10])->create();

        $response = $this->actingAs($this->user)
                    ->get(route('products.show', ['product' => $product->id]), [
                        'X-Inertia' => true,
                        'Accept' => 'application/json'
                    ]);

        $response
            ->assertOk()
            ->assertJsonPath('component', 'Products/ProductShow')
            ->assertJsonPath('props.product.id', $product->id)
            ->assertJsonCount(3, 'props.recommended');
    }

    public function test_product_show_does_not_include_out_of_stock_recommendations(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 5]);

        // Product with no stock should not appear to user
        Product::factory()->emptyStock()->create();

        // This product should appear to user
        Product::factory()->create(['stock_quantity' => 10]);

        $response = $this->actingAs($this->user)
                    ->get(route('products.show', ['product' => $product->id]), [
                        'X-Inertia' => true,
                        'Accept' => 'application/json'
                    ]);

        $recommended = collect($response->json('props.recommended'));

        // Make sure every product has stock
        $this->assertTrue($recommended->every(fn ($p) => $p['stock_quantity'] > 0));
    }
}
