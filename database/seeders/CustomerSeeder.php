<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test customer with known credentials
        Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '0987654321',
            'password' => Hash::make('password'),
            'point' => 0,
        ]);

        // Create 50 random customers using the factory
        Customer::factory()->count(50)->create();
    }
}
