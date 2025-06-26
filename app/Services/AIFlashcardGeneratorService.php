<?php

namespace App\Services;

use App\Models\Deck;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;

class AIFlashcardGeneratorService
{
    /**
     * Generate AI flashcards for a deck
     */
    public function generateFlashcards(Deck $deck, string $theme, string $difficulty = 'beginner'): bool
    {
        try {
            $prompt = $this->buildPrompt($theme, $difficulty);
            $schema = $this->buildSchema();
            
            // Get the best available provider and model
            $provider = $this->getBestAvailableProvider();
            $model = $this->getModelForProvider($provider);
            
            if (!$provider || !$model) {
                Log::error('No AI provider available for flashcard generation');
                return false;
            }
            
            $response = Prism::structured()
                ->using($provider, $model)
                ->withPrompt($prompt)
                ->withSchema($schema)
                ->asStructured();

            Log::info('AI structured response received', [
                'deck_id' => $deck->id,
                'theme' => $theme,
                'difficulty' => $difficulty,
                'provider' => $provider->value,
                'model' => $model,
                'response_type' => gettype($response->structured),
                'response_preview' => is_array($response->structured) ? count($response->structured) . ' items' : 'not array'
            ]);

            $flashcards = $this->parseStructuredResponse($response->structured);
            
            if (empty($flashcards)) {
                Log::warning('No valid flashcards generated', [
                    'deck_id' => $deck->id,
                    'theme' => $theme,
                    'provider' => $provider->value,
                    'model' => $model
                ]);
                return false;
            }
            
            $this->saveFlashcards($deck, $flashcards);
            
            Log::info('Flashcards successfully generated and saved', [
                'deck_id' => $deck->id,
                'count' => count($flashcards),
                'provider' => $provider->value,
                'model' => $model
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('AI flashcard generation failed', [
                'deck_id' => $deck->id,
                'theme' => $theme,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Build the AI prompt for flashcard generation
     */
    protected function buildPrompt(string $theme, string $difficulty): string
    {
        $difficultyContext = [
            'beginner' => 'basic concepts and simple explanations',
            'intermediate' => 'moderate complexity with some technical details',
            'advanced' => 'complex concepts with detailed technical explanations'
        ];

        $context = $difficultyContext[$difficulty] ?? $difficultyContext['beginner'];

        return "Create exactly 10 flashcards about '{$theme}' at a {$difficulty} level focusing on {$context}. 

Guidelines:
- Make questions clear and specific
- Keep answers concise but informative
- Ensure content is appropriate for {$difficulty} level
- Cover different aspects of the topic
- Each flashcard should test a single concept or fact
- Questions should be direct and unambiguous
- Answers should be accurate and complete but not overly verbose";
    }

    /**
     * Build the schema for structured output
     */
    protected function buildSchema(): ObjectSchema
    {
        $flashcardSchema = new ObjectSchema(
            name: 'flashcard',
            description: 'A single flashcard with question and answer',
            properties: [
                new StringSchema('question', 'The question text for the flashcard'),
                new StringSchema('answer', 'The answer text for the flashcard')
            ],
            requiredFields: ['question', 'answer']
        );

        return new ObjectSchema(
            name: 'flashcards_response',
            description: 'Response containing an array of flashcards',
            properties: [
                new ArraySchema(
                    name: 'flashcards',
                    description: 'Array of flashcards for the deck',
                    items: $flashcardSchema
                )
            ],
            requiredFields: ['flashcards']
        );
    }

    /**
     * Parse structured response from Prism
     */
    protected function parseStructuredResponse($structuredData): array
    {
        try {
            // Handle the schema structure where flashcards are nested under 'flashcards' property
            if (is_array($structuredData) && isset($structuredData['flashcards'])) {
                $flashcardsData = $structuredData['flashcards'];
            } elseif (is_array($structuredData)) {
                // Fallback for direct array format
                $flashcardsData = $structuredData;
            } else {
                Log::warning('Structured response not in expected format', [
                    'data_type' => gettype($structuredData),
                    'data' => $structuredData
                ]);
                return [];
            }
            
            $validFlashcards = [];
            
            foreach ($flashcardsData as $flashcard) {
                if (is_array($flashcard) && 
                    isset($flashcard['question']) && isset($flashcard['answer']) && 
                    !empty(trim($flashcard['question'])) && !empty(trim($flashcard['answer']))) {
                    
                    $validFlashcards[] = [
                        'question' => trim($flashcard['question']),
                        'answer' => trim($flashcard['answer'])
                    ];
                }
            }
            
            return $validFlashcards;
            
        } catch (\Exception $e) {
            Log::error('Failed to parse structured response', [
                'error' => $e->getMessage(),
                'data' => $structuredData
            ]);
            
            return [];
        }
    }

    /**
     * Save flashcards to the deck
     */
    protected function saveFlashcards(Deck $deck, array $flashcards): void
    {
        foreach ($flashcards as $index => $flashcard) {
            $deck->flashcards()->create([
                'question' => $flashcard['question'],
                'answer' => $flashcard['answer'],
                'order' => $index + 1,
            ]);
        }
    }

    /**
     * Get the best available AI provider based on configured API keys
     */
    protected function getBestAvailableProvider(): ?Provider
    {
        // Priority order: OpenAI > Anthropic > Gemini
        if (!empty(config('prism.providers.openai.api_key'))) {
            return Provider::OpenAI;
        }
        
        if (!empty(config('prism.providers.anthropic.api_key'))) {
            return Provider::Anthropic;
        }
        
        if (!empty(config('prism.providers.gemini.api_key'))) {
            return Provider::Gemini;
        }
        
        return null;
    }

    /**
     * Get the appropriate model for the given provider
     */
    protected function getModelForProvider(Provider $provider): ?string
    {
        return match ($provider) {
            Provider::OpenAI => 'gpt-4o',
            Provider::Anthropic => 'claude-3-5-sonnet-20241022',
            Provider::Gemini => 'gemini-1.5-pro',
            default => null,
        };
    }

    /**
     * Check if AI generation is available
     */
    public static function isAvailable(): bool
    {
        return !empty(config('prism.providers.openai.api_key')) 
            || !empty(config('prism.providers.anthropic.api_key'))
            || !empty(config('prism.providers.gemini.api_key'));
    }
} 