<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users
        User::factory(10)->create();

        // Create a specific test user
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create wallets for all users
        User::all()->each(function ($user) {
            Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => rand(50000, 1000000)] // Random initial balance
            );
        });
    }
}
