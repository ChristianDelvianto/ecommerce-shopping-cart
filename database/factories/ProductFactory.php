<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(rand(3, 12), true),
            'price' => fake()->numberBetween(10000, 50000), // In cents
            'stock_quantity' => fake()->numberBetween(10, 100)
        ];
    }

    /**
     * Low stock product
     */
    public function lowStock(): static
    {
        return $this->state(fn () => [
            'stock_quantity' => $this->faker->numberBetween(1, 5),
        ]);
    }

    public function emptyStock(): static
    {
        return $this->state(fn () => [
            'stock_quantity' => 0,
        ]);
    }
}
