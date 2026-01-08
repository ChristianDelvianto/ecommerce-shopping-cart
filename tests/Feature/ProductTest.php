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

    public function test_admin_can_update_product(): void
    {
        $admin = User::factory()
                ->create(['role' => 'admin']);

        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->patch("/products/{$product->id}", [
            'name' => 'Test update'
        ]);

        $response->assertRedirectBackWithoutErrors();
    }

    public function test_user_cannot_update_product(): void
    {
        $user = User::factory()
                ->create(['role' => 'user']);

        $product = Product::factory()->create();

        $response = $this->actingAs($user)->patch("/products/{$product->id}", [
            'name' => 'Test update'
        ]);

        $response->assertSessionHasErrors();
        $response->assertRedirectBackWithErrors();
    }
}
