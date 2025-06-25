<?php

use App\Livewire\DeckStudy;
use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->deck = Deck::factory()->create(['user_id' => $this->user->id]);
    $this->flashcards = Flashcard::factory()->count(5)->create(['deck_id' => $this->deck->id]);
    $this->actingAs($this->user);
});

it('mounts with correct initial state', function () {
    Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->assertSet('deck.id', $this->deck->id)
        ->assertSet('sessionStarted', false)
        ->assertSet('sessionCompleted', false)
        ->assertSet('showQuestion', true)
        ->assertSet('currentCardIndex', 0)
        ->assertSet('correctAnswers', 0)
        ->assertSet('totalCards', 5)
        ->assertSet('accuracy', 0);
});

it('loads flashcards on mount', function () {
    $component = Livewire::test(DeckStudy::class, ['deck' => $this->deck]);
    
    $flashcards = $component->get('flashcards');
    expect($flashcards)->toHaveCount(5);
});

it('starts session with sequential order', function () {
    $component = Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->call('startSession', 'sequential')
        ->assertSet('sessionStarted', true)
        ->assertSet('currentCardIndex', 0)
        ->assertSet('correctAnswers', 0)
        ->assertSet('showQuestion', true);
    
    // Check that flashcards are in original order
    $flashcards = $component->get('flashcards');
    expect($flashcards->first()->id)->toBe($this->flashcards->first()->id);
});

it('starts session with random order', function () {
    Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->call('startSession', 'random')
        ->assertSet('sessionStarted', true)
        ->assertSet('currentCardIndex', 0)
        ->assertSet('correctAnswers', 0)
        ->assertSet('showQuestion', true);
    
    // Note: We can't easily test randomization in a unit test
    // but we can verify the session started correctly
});

it('flips card between question and answer', function () {
    Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->call('startSession')
        ->assertSet('showQuestion', true)
        ->call('flipCard')
        ->assertSet('showQuestion', false)
        ->call('flipCard')
        ->assertSet('showQuestion', true);
});

it('marks answer as correct and moves to next card', function () {
    Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->call('startSession')
        ->call('markCorrect')
        ->assertSet('correctAnswers', 1)
        ->assertSet('currentCardIndex', 1)
        ->assertSet('showQuestion', true);
});

it('marks answer as incorrect and moves to next card', function () {
    Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->call('startSession')
        ->call('markIncorrect')
        ->assertSet('correctAnswers', 0)
        ->assertSet('currentCardIndex', 1)
        ->assertSet('showQuestion', true);
});

it('completes session when all cards are answered', function () {
    $component = Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->call('startSession');
    
    // Answer all 5 cards
    for ($i = 0; $i < 5; $i++) {
        $component->call('markCorrect');
    }
    
    $component
        ->assertSet('sessionCompleted', true)
        ->assertSet('correctAnswers', 5)
        ->assertSet('accuracy', 100);
});

it('calculates accuracy correctly', function () {
    $component = Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->call('startSession');
    
    // Answer 3 correct, 2 incorrect
    $component->call('markCorrect');    // Card 1: correct
    $component->call('markCorrect');    // Card 2: correct
    $component->call('markIncorrect');  // Card 3: incorrect
    $component->call('markCorrect');    // Card 4: correct
    $component->call('markIncorrect');  // Card 5: incorrect
    
    $component
        ->assertSet('sessionCompleted', true)
        ->assertSet('correctAnswers', 3)
        ->assertSet('accuracy', 60); // 3/5 * 100 = 60%
});

it('returns amazing results for high accuracy', function () {
    $component = Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->call('startSession');
    
    // Answer all cards correctly for 100% accuracy
    for ($i = 0; $i < 5; $i++) {
        $component->call('markCorrect');
    }
    
    $results = $component->instance()->results;
    
    expect($results['emoji'])->toBe('🎉');
    expect($results['title'])->toContain('Amazing work');
    expect($results['subtitle'])->toContain('5 out of 5');
});

it('returns nice job results for medium accuracy', function () {
    $component = Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->call('startSession');
    
    // Answer 3 out of 5 correctly (60% accuracy)
    $component->call('markCorrect');
    $component->call('markCorrect');
    $component->call('markCorrect');
    $component->call('markIncorrect');
    $component->call('markIncorrect');
    
    $results = $component->instance()->results;
    
    expect($results['emoji'])->toBe('👏');
    expect($results['title'])->toContain('Nice job');
    expect($results['subtitle'])->toContain('3 out of 5');
});

it('returns encouraging results for low accuracy', function () {
    $component = Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->call('startSession');
    
    // Answer only 1 out of 5 correctly (20% accuracy)
    $component->call('markCorrect');
    $component->call('markIncorrect');
    $component->call('markIncorrect');
    $component->call('markIncorrect');
    $component->call('markIncorrect');
    
    $results = $component->instance()->results;
    
    expect($results['emoji'])->toBe('💪');
    expect($results['title'])->toContain('Keep going');
    expect($results['subtitle'])->toContain('1 out of 5');
});

it('gets current card correctly', function () {
    $component = Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->call('startSession');
    
    $currentCard = $component->instance()->currentCard;
    
    expect($currentCard->id)->toBe($this->flashcards->first()->id);
    
    // Move to next card
    $component->call('markCorrect');
    
    $currentCard = $component->instance()->currentCard;
    expect($currentCard->id)->toBe($this->flashcards->skip(1)->first()->id);
});

it('calculates progress percentage correctly', function () {
    $component = Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->call('startSession');
    
    // At start (card 1 of 5)
    expect($component->instance()->getProgressPercentage())->toBe(20.0);
    
    // Move to card 2
    $component->call('markCorrect');
    expect($component->instance()->getProgressPercentage())->toBe(40.0);
    
    // Move to card 3
    $component->call('markCorrect');
    expect($component->instance()->getProgressPercentage())->toBe(60.0);
});

it('restarts session correctly', function () {
    $component = Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->call('startSession')
        ->call('markCorrect')
        ->call('markCorrect')
        ->call('restartSession')
        ->assertSet('sessionStarted', false)
        ->assertSet('sessionCompleted', false)
        ->assertSet('totalCards', 5);
});

it('allows deck owner to access', function () {
    $component = Livewire::test(DeckStudy::class, ['deck' => $this->deck]);
    
    expect($component->instance()->canAccess())->toBeTrue();
});

it('allows shared user to access', function () {
    $sharedUser = User::factory()->create();
    
    // Create shared deck relationship properly
    $this->deck->sharedWithUsers()->attach($sharedUser->id, [
        'user_id' => $this->user->id, // The deck owner
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    $this->actingAs($sharedUser);
    
    $component = Livewire::test(DeckStudy::class, ['deck' => $this->deck]);
    
    expect($component->instance()->deck->id)->toBe($this->deck->id);
});

it('prevents unauthorized access', function () {
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);
    
    Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->assertStatus(403);
});

it('handles empty deck gracefully', function () {
    $emptyDeck = Deck::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
    
    Livewire::test(DeckStudy::class, ['deck' => $emptyDeck])
        ->assertSet('totalCards', 0)
        ->assertSet('accuracy', 0);
});

it('handles single card deck', function () {
    $singleCardDeck = Deck::factory()->create(['user_id' => $this->user->id]);
    Flashcard::factory()->create(['deck_id' => $singleCardDeck->id]);
    
    $this->actingAs($this->user);
    
    $component = Livewire::test(DeckStudy::class, ['deck' => $singleCardDeck])
        ->call('startSession')
        ->call('markCorrect')
        ->assertSet('sessionCompleted', true)
        ->assertSet('correctAnswers', 1)
        ->assertSet('accuracy', 100);
});

it('renders the component view', function () {
    $this->actingAs($this->user);
    
    Livewire::test(DeckStudy::class, ['deck' => $this->deck])
        ->assertViewIs('livewire.deck-study');
}); 