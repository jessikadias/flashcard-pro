<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample users (update if they exist)
        $john = User::updateOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password'),
                'onboarding_completed' => false,
                'api_token' => Str::random(80),
            ]
        );

        $jane = User::updateOrCreate(
            ['email' => 'jane@example.com'],
            [
                'name' => 'Jane Smith',
                'password' => bcrypt('password'),
                'onboarding_completed' => false,
                'api_token' => Str::random(80),
            ]
        );

        $bob = User::updateOrCreate(
            ['email' => 'bob@example.com'],
            [
                'name' => 'Bob Wilson',
                'password' => bcrypt('password'),
                'onboarding_completed' => false,
                'api_token' => Str::random(80),
            ]
        );

        // Create test user for development (new user experience)
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'onboarding_completed' => false, // New user experience
                'api_token' => Str::random(80),
            ]
        );

        $this->command->info('Users created/updated: john@example.com, jane@example.com, bob@example.com, test@example.com');
    }
} 