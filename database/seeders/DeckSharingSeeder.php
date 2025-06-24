<?php

namespace Database\Seeders;

use App\Models\Deck;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        // Define sharing relationships
        $sharingData = [
            [
                'user_id' => $john->id,
                'deck_id' => $johnMathDeck->id,
                'shared_with_user_id' => $jane->id,
            ],
            [
                'user_id' => $jane->id,
                'deck_id' => $janeScienceDeck->id,
                'shared_with_user_id' => $bob->id,
            ],
            [
                'user_id' => $bob->id,
                'deck_id' => $bobProgrammingDeck->id,
                'shared_with_user_id' => $john->id,
            ],
        ];

        // Create or update sharing relationships
        foreach ($sharingData as $data) {
            DB::table('shared_decks')->updateOrInsert(
                [
                    'user_id' => $data['user_id'],
                    'deck_id' => $data['deck_id'],
                    'shared_with_user_id' => $data['shared_with_user_id'],
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Deck sharing relationships created/updated');
    }
} 