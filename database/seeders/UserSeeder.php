<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
        ->create([
            'role' => 'admin',
            'name' => 'Admin 1',
            'email' => 'admin1@example.com',
        ]);

        User::factory()
        ->create([
            'role' => 'admin',
            'name' => 'Admin 2',
            'email' => 'admin2@example.com',
        ]);

        User::factory()
        ->has(Cart::factory(), 'cart')
        ->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory(50)
        ->has(Cart::factory(), 'cart')
        ->create();
    }
}
