<?php

namespace Database\Seeders;

use App\Models\Deck;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeckSeeder extends Seeder
{
    public function run(): void
    {
        $john = User::where('email', 'john@example.com')->first();
        $jane = User::where('email', 'jane@example.com')->first();
        $bob = User::where('email', 'bob@example.com')->first();

        // Create decks for John
        $johnMathDeck = Deck::updateOrCreate(
            ['name' => 'Mathematics Fundamentals', 'user_id' => $john->id],
            [
                'description' => 'Basic math concepts and formulas',
                'is_public' => true,
            ]
        );

        $johnHistoryDeck = Deck::updateOrCreate(
            ['name' => 'World History', 'user_id' => $john->id],
            [
                'description' => 'Important historical events and dates',
                'is_public' => false,
            ]
        );

        // Create decks for Jane
        $janeScienceDeck = Deck::updateOrCreate(
            ['name' => 'Biology Basics', 'user_id' => $jane->id],
            [
                'description' => 'Introduction to biological concepts',
                'is_public' => true,
            ]
        );

        $janeLanguageDeck = Deck::updateOrCreate(
            ['name' => 'Spanish Vocabulary', 'user_id' => $jane->id],
            [
                'description' => 'Essential Spanish words and phrases',
                'is_public' => false,
            ]
        );

        // Create decks for Bob
        $bobProgrammingDeck = Deck::updateOrCreate(
            ['name' => 'Laravel Framework', 'user_id' => $bob->id],
            [
                'description' => 'Laravel PHP framework concepts and best practices',
                'is_public' => true,
            ]
        );

        $this->command->info('Decks created/updated for all users');
    }
} 