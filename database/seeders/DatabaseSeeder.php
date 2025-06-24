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

        $this->command->info('🎉 FlashcardPro sample data created successfully!');
        $this->command->info('');
        $this->command->info('👥 Sample users created:');
        $this->command->info('   • john@example.com (password: password) - Experienced user');
        $this->command->info('   • jane@example.com (password: password) - Experienced user');
        $this->command->info('   • bob@example.com (password: password) - Experienced user');
        $this->command->info('   • test@example.com (password: password) - New user (will see onboarding)');
        $this->command->info('');
        $this->command->info('📚 Sample decks with flashcards:');
        $this->command->info('   • Mathematics Fundamentals (John) - Public');
        $this->command->info('   • World History (John) - Private');
        $this->command->info('   • Biology Basics (Jane) - Public');
        $this->command->info('   • Spanish Vocabulary (Jane) - Private');
        $this->command->info('   • Laravel Framework (Bob) - Public');
        $this->command->info('');
        $this->command->info('🤝 Deck sharing relationships created between users');
        $this->command->info('🔑 All users have API tokens generated');
    }
}
