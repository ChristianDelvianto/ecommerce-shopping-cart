<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Active products
        Product::factory()
                ->count(170)
                ->create();

        // Low stock products
        Product::factory()
                ->count(20)
                ->lowStock()
                ->create();

        // Empty stock products
        Product::factory()
                ->count(30)
                ->emptyStock()
                ->create();
    }
}
