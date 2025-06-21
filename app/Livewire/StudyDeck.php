<?php

namespace App\Livewire;

use App\Models\Deck;
use Livewire\Component;
use Illuminate\Support\Collection;

class StudyDeck extends Component
{
    /**
     * The deck being studied.
     */
    public ?Deck $deck = null;
    /**
     * The user being studied.
     */
    public $user;
    /**
     * The flashcards to study.
     */
    public Collection $flashcards;
    /**
     * Current study session state.
     */
    public bool $sessionStarted = false;
    /**
     * Whether the session has been completed.
     */
    public bool $sessionCompleted = false;
    /**
     * Whether the question is being shown.
     */
    public bool $showQuestion = true;
    /**
     * The index of the current card.
     */
    public int $currentCardIndex = 0;
    /**
     * The user's answers.
     */
    public array $answers = [];
    /**
     * The number of correct answers.
     */
    public int $correctAnswers = 0;
    /**
     * The total number of cards.
     */
    public int $totalCards = 0;

    /**
     * Study session statistics.
     */
    public float $accuracy = 0;

    /**
     * Mount the component.
     */
    public function mount(Deck $deck)
    {
        $this->deck = $deck;
        $this->user = auth()->user();
        $this->loadFlashcards();
        $this->totalCards = $this->flashcards->count();
    }

    /**
     * Load flashcards for the deck.
     */
    public function loadFlashcards()
    {
        $this->flashcards = $this->deck->flashcards()->get();
    }

    /**
     * Start the study session.
     */
    public function startSession(string $order = 'sequential')
    {
        $this->studyOrder = $order;
        if ($this->studyOrder === 'random') {
            $this->flashcards = $this->flashcards->shuffle();
        }

        $this->sessionStarted = true;
        $this->currentCardIndex = 0;
        $this->answers = [];
        $this->correctAnswers = 0;
        $this->showQuestion = true;
    }

    /**
     * Flip the current card.
     */
    public function flipCard()
    {
        $this->showQuestion = !$this->showQuestion;
    }

    /**
     * Mark the current card as correct or incorrect.
     */
    public function markAnswer(bool $isCorrect)
    {
        // Save the answer for the current card.
        $this->answers[$this->currentCardIndex] = $isCorrect;

        // Increment the score if correct.
        if ($isCorrect) {
            $this->correctAnswers++;
        }

        // Move to the next card.
        $this->nextCard();
    }

    /**
     * Mark the current card as correct.
     */
    public function markCorrect()
    {
        $this->markAnswer(true);
    }

    /**
     * Mark the current card as incorrect.
     */
    public function markIncorrect()
    {
        $this->markAnswer(false);
    }

    /**
     * Move to the next card or complete the session.
     */
    public function nextCard()
    {
        $this->showQuestion = true;
        
        if ($this->currentCardIndex < $this->totalCards - 1) {
            $this->currentCardIndex++;
        } else {
            $this->completeSession();
        }
    }

    /**
     * Complete the study session.
     */
    public function completeSession()
    {
        $this->sessionCompleted = true;
        $this->accuracy = $this->totalCards > 0 ? ($this->correctAnswers / $this->totalCards) * 100 : 0;
    }

    /**
     * Get the results for the session.
     *
     * @return array
     */
    public function getResultsProperty(): array
    {
        $userName = explode(' ', $this->user->name)[0];
        $correct = $this->correctAnswers;
        $total = $this->totalCards;

        if ($this->accuracy >= 90) {
            return [
                'emoji' => '🎉',
                'title' => "Amazing work, {$userName}!",
                'subtitle' => "You got {$correct} out of {$total} cards right."
            ];
        }

        if ($this->accuracy >= 60) {
            return [
                'emoji' => '👏',
                'title' => "Nice job, {$userName}!",
                'subtitle' => "You got {$correct} out of {$total} cards. A little more practice and you'll get there!"
            ];
        }

        return [
            'emoji' => '💪',
            'title' => "Keep going, {$userName}!",
            'subtitle' => "You got {$correct} out of {$total} cards. Every session helps you improve."
        ];
    }

    /**
     * Restart the study session.
     */
    public function restartSession()
    {
        $this->sessionStarted = false;
        $this->sessionCompleted = false;
        $this->loadFlashcards();
        $this->totalCards = $this->flashcards->count();
    }

    /**
     * Get the current card.
     */
    public function getCurrentCardProperty()
    {
        return $this->flashcards->get($this->currentCardIndex);
    }

    /**
     * Get the current progress percentage.
     */
    public function getProgressPercentage()
    {
        return $this->totalCards > 0 ? (($this->currentCardIndex + 1) / $this->totalCards) * 100 : 0;
    }

    /**
     * Check if the user can access this deck.
     */
    public function canAccess(): bool
    {
        return $this->deck->user_id === auth()->id() || 
               $this->deck->sharedWithUsers()->where('shared_with_user_id', auth()->id())->exists();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        if (!$this->canAccess()) {
            abort(403);
        }

        return view('livewire.study-deck');
    }
}
