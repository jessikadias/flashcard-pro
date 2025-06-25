<?php

namespace Database\Seeders;

use App\Models\Deck;
use App\Models\Flashcard;
use Illuminate\Database\Seeder;

class FlashcardSeeder extends Seeder
{
    public function run(): void
    {
        // Import deck data
        $data = require __DIR__ . '/data/DeckData.php';
        
        foreach ($data['decks'] as $deckData) {
            $deck = Deck::where('name', $deckData['title'])->first();
            
            if (!$deck) {
                $this->command->warn("Deck '{$deckData['title']}' not found, skipping flashcards");
                continue;
            }
            
            foreach ($deckData['cards'] as $index => $card) {
                Flashcard::updateOrCreate(
                    ['question' => $card['question'], 'deck_id' => $deck->id],
                    [
                        'answer' => $card['answer'],
                        'order' => $index + 1,
                    ]
                );
            }
        }

        $this->command->info('Flashcards created/updated for all decks');
    }
} 