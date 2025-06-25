<?php

namespace Database\Factories;

use App\Models\Deck;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flashcard>
 */
class FlashcardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => fake()->sentence() . '?',
            'answer' => fake()->sentence(),
            'order' => null, // Will be auto-assigned by the model
            'deck_id' => Deck::factory(),
        ];
    }

    /**
     * Create a flashcard with a specific question.
     */
    public function withQuestion(string $question): static
    {
        return $this->state(fn (array $attributes) => [
            'question' => $question,
        ]);
    }

    /**
     * Create a flashcard with a specific answer.
     */
    public function withAnswer(string $answer): static
    {
        return $this->state(fn (array $attributes) => [
            'answer' => $answer,
        ]);
    }

    /**
     * Create a flashcard with a specific order.
     */
    public function withOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'order' => $order,
        ]);
    }
}
