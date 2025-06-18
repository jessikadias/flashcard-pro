<?php

namespace Database\Seeders;

use App\Models\Deck;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeckSharingSeeder extends Seeder
{
    public function run(): void
    {
        $john = User::where('email', 'john@example.com')->first();
        $jane = User::where('email', 'jane@example.com')->first();
        $bob = User::where('email', 'bob@example.com')->first();

        $johnMathDeck = Deck::where('name', 'Mathematics Fundamentals')->first();
        $janeScienceDeck = Deck::where('name', 'Biology Basics')->first();
        $bobProgrammingDeck = Deck::where('name', 'Laravel Framework')->first();

        // John shares his math deck with Jane
        \DB::table('shared_decks')->updateOrInsert(
            [
                'user_id' => $john->id,
                'deck_id' => $johnMathDeck->id,
                'shared_with_user_id' => $jane->id,
            ],
            [
                'message' => 'Check out these math fundamentals!',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Jane shares her biology deck with Bob
        \DB::table('shared_decks')->updateOrInsert(
            [
                'user_id' => $jane->id,
                'deck_id' => $janeScienceDeck->id,
                'shared_with_user_id' => $bob->id,
            ],
            [
                'message' => 'Great for understanding basic biology concepts.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Bob shares his Laravel deck with John
        \DB::table('shared_decks')->updateOrInsert(
            [
                'user_id' => $bob->id,
                'deck_id' => $bobProgrammingDeck->id,
                'shared_with_user_id' => $john->id,
            ],
            [
                'message' => 'Essential Laravel commands and concepts.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Deck sharing relationships created/updated');
    }
} 