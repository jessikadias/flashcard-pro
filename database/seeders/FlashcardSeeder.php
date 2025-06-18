<?php

namespace Database\Seeders;

use App\Models\Deck;
use App\Models\Flashcard;
use Illuminate\Database\Seeder;

class FlashcardSeeder extends Seeder
{
    public function run(): void
    {
        $johnMathDeck = Deck::where('name', 'Mathematics Fundamentals')->first();
        $johnHistoryDeck = Deck::where('name', 'World History')->first();
        $janeScienceDeck = Deck::where('name', 'Biology Basics')->first();
        $janeLanguageDeck = Deck::where('name', 'Spanish Vocabulary')->first();
        $bobProgrammingDeck = Deck::where('name', 'Laravel Framework')->first();

        // Math flashcards
        $mathFlashcards = [
            ['question' => 'What is 2 + 2?', 'answer' => '4'],
            ['question' => 'What is the square root of 16?', 'answer' => '4'],
            ['question' => 'What is 5 × 7?', 'answer' => '35'],
            ['question' => 'What is 100 ÷ 4?', 'answer' => '25'],
            ['question' => 'What is the area of a circle with radius 3?', 'answer' => '28.27 (π × r²)'],
        ];

        foreach ($mathFlashcards as $index => $card) {
            Flashcard::updateOrCreate(
                ['question' => $card['question'], 'deck_id' => $johnMathDeck->id],
                [
                    'answer' => $card['answer'],
                    'order' => $index + 1,
                ]
            );
        }

        // History flashcards
        $historyFlashcards = [
            ['question' => 'When did World War II end?', 'answer' => '1945'],
            ['question' => 'Who was the first President of the United States?', 'answer' => 'George Washington'],
            ['question' => 'When did the Berlin Wall fall?', 'answer' => '1989'],
            ['question' => 'What year did Columbus discover America?', 'answer' => '1492'],
        ];

        foreach ($historyFlashcards as $index => $card) {
            Flashcard::updateOrCreate(
                ['question' => $card['question'], 'deck_id' => $johnHistoryDeck->id],
                [
                    'answer' => $card['answer'],
                    'order' => $index + 1,
                ]
            );
        }

        // Biology flashcards
        $biologyFlashcards = [
            ['question' => 'What is the powerhouse of the cell?', 'answer' => 'Mitochondria'],
            ['question' => 'What is the process by which plants make food?', 'answer' => 'Photosynthesis'],
            ['question' => 'What is the largest organ in the human body?', 'answer' => 'Skin'],
            ['question' => 'What are the building blocks of proteins?', 'answer' => 'Amino acids'],
            ['question' => 'What is the study of heredity called?', 'answer' => 'Genetics'],
        ];

        foreach ($biologyFlashcards as $index => $card) {
            Flashcard::updateOrCreate(
                ['question' => $card['question'], 'deck_id' => $janeScienceDeck->id],
                [
                    'answer' => $card['answer'],
                    'order' => $index + 1,
                ]
            );
        }

        // Spanish flashcards
        $spanishFlashcards = [
            ['question' => 'How do you say "Hello" in Spanish?', 'answer' => 'Hola'],
            ['question' => 'How do you say "Thank you" in Spanish?', 'answer' => 'Gracias'],
            ['question' => 'How do you say "Goodbye" in Spanish?', 'answer' => 'Adiós'],
            ['question' => 'How do you say "Please" in Spanish?', 'answer' => 'Por favor'],
        ];

        foreach ($spanishFlashcards as $index => $card) {
            Flashcard::updateOrCreate(
                ['question' => $card['question'], 'deck_id' => $janeLanguageDeck->id],
                [
                    'answer' => $card['answer'],
                    'order' => $index + 1,
                ]
            );
        }

        // Laravel flashcards
        $laravelFlashcards = [
            ['question' => 'What is the command to create a new Laravel project?', 'answer' => 'composer create-project laravel/laravel project-name'],
            ['question' => 'What is the command to run Laravel migrations?', 'answer' => 'php artisan migrate'],
            ['question' => 'What is the command to create a new model with migration?', 'answer' => 'php artisan make:model ModelName -m'],
            ['question' => 'What is the command to start the development server?', 'answer' => 'php artisan serve'],
            ['question' => 'What is the command to clear the application cache?', 'answer' => 'php artisan cache:clear'],
            ['question' => 'What is the command to create a new controller?', 'answer' => 'php artisan make:controller ControllerName'],
        ];

        foreach ($laravelFlashcards as $index => $card) {
            Flashcard::updateOrCreate(
                ['question' => $card['question'], 'deck_id' => $bobProgrammingDeck->id],
                [
                    'answer' => $card['answer'],
                    'order' => $index + 1,
                ]
            );
        }

        $this->command->info('Flashcards created/updated for all decks');
    }
} 