<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DeckSeeder::class,
            FlashcardSeeder::class,
            DeckSharingSeeder::class,
        ]);

        $this->command->info('FlashcardPro sample data created successfully!');
        $this->command->info('Sample users:');
        $this->command->info('- john@example.com (password: password)');
        $this->command->info('- jane@example.com (password: password)');
        $this->command->info('- bob@example.com (password: password)');
        $this->command->info('- test@example.com (password: password)');
    }
}
