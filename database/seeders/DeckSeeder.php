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

        // Import deck data
        $data = require __DIR__ . '/data/DeckData.php';
        
        $users = [
            'john' => $john,
            'jane' => $jane,
            'bob' => $bob,
        ];

        // Create a lookup array for decks by title
        $deckLookup = [];
        foreach ($data['decks'] as $deckData) {
            $deckLookup[$deckData['title']] = $deckData;
        }

        $totalDecks = 0;
        $userDeckCounts = [];

        // Create decks for each user
        foreach ($data['user_decks'] as $userKey => $deckTitles) {
            $user = $users[$userKey];
            $userDeckCounts[$userKey] = count($deckTitles);
            $totalDecks += count($deckTitles);
            
            foreach ($deckTitles as $deckTitle) {
                if (!isset($deckLookup[$deckTitle])) {
                    $this->command->warn("Deck '{$deckTitle}' not found in deck definitions, skipping");
                    continue;
                }
                
                $deckData = $deckLookup[$deckTitle];
                
                Deck::updateOrCreate(
                    ['name' => $deckTitle, 'user_id' => $user->id],
                    ['is_public' => $deckData['is_public']]
                );
            }
        }

        $this->command->info(sprintf(
            '%d decks created/updated across all users (John: %d, Jane: %d, Bob: %d)',
            $totalDecks,
            $userDeckCounts['john'],
            $userDeckCounts['jane'],
            $userDeckCounts['bob']
        ));
    }
} 