<?php

use App\Livewire\FlashcardForm;
use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->deck = Deck::factory()->create(['user_id' => $this->user->id]);
    $this->flashcard = Flashcard::factory()->create(['deck_id' => $this->deck->id]);
    $this->actingAs($this->user);
});

it('mounts with empty form for new flashcard', function () {
    Livewire::test(FlashcardForm::class, ['deck' => $this->deck])
        ->assertSet('deck.id', $this->deck->id)
        ->assertSet('flashcard', null)
        ->assertSet('question', '')
        ->assertSet('answer', '');
});

it('mounts with existing flashcard data for editing', function () {
    Livewire::test(FlashcardForm::class, [
        'deck' => $this->deck,
        'flashcardId' => $this->flashcard->id
    ])
        ->assertSet('deck.id', $this->deck->id)
        ->assertSet('flashcard.id', $this->flashcard->id)
        ->assertSet('question', $this->flashcard->question)
        ->assertSet('answer', $this->flashcard->answer);
});

it('validates required fields when saving', function () {
    Livewire::test(FlashcardForm::class, ['deck' => $this->deck])
        ->set('question', '')
        ->set('answer', '')
        ->call('save')
        ->assertHasErrors(['question', 'answer']);
});

it('validates minimum length for question and answer', function () {
    Livewire::test(FlashcardForm::class, ['deck' => $this->deck])
        ->set('question', 'ab') // Too short
        ->set('answer', '') // Empty
        ->call('save')
        ->assertHasErrors(['question', 'answer']);
});

it('creates new flashcard successfully', function () {
    Livewire::test(FlashcardForm::class, ['deck' => $this->deck])
        ->set('question', 'What is Laravel?')
        ->set('answer', 'A PHP framework')
        ->call('save')
        ->assertRedirect(route('decks.edit', $this->deck));
    
    expect($this->deck->flashcards()->count())->toBe(2); // Original + new one
    
    $newFlashcard = $this->deck->flashcards()->where('question', 'What is Laravel?')->first();
    expect($newFlashcard)->not->toBeNull();
    expect($newFlashcard->question)->toBe('What is Laravel?');
    expect($newFlashcard->answer)->toBe('A PHP framework');
});

it('updates existing flashcard successfully', function () {
    $originalQuestion = $this->flashcard->question;
    $originalAnswer = $this->flashcard->answer;
    
    Livewire::test(FlashcardForm::class, [
        'deck' => $this->deck,
        'flashcardId' => $this->flashcard->id
    ])
        ->set('question', 'Updated question')
        ->set('answer', 'Updated answer')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('decks.edit', $this->deck));
    
    $this->flashcard->refresh();
    expect($this->flashcard->question)->toBe('Updated question');
    expect($this->flashcard->answer)->toBe('Updated answer');
});

it('prevents unauthorized users from saving flashcards', function () {
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);
    
    Livewire::test(FlashcardForm::class, ['deck' => $this->deck])
        ->set('question', 'Unauthorized question')
        ->set('answer', 'Unauthorized answer')
        ->call('save')
        ->assertNoRedirect(); // Should not redirect on error
    
    // Check that the flashcard was not created
    expect($this->deck->flashcards()->where('question', 'Unauthorized question')->exists())->toBe(false);
});

it('deletes flashcard successfully', function () {
    Livewire::test(FlashcardForm::class, [
        'deck' => $this->deck,
        'flashcardId' => $this->flashcard->id
    ])
        ->call('delete')
        ->assertRedirect(route('decks.edit', $this->deck));
    
    expect(Flashcard::find($this->flashcard->id))->toBeNull();
});

it('handles flashcardDeleted event', function () {
    Livewire::test(FlashcardForm::class, [
        'deck' => $this->deck,
        'flashcardId' => $this->flashcard->id
    ])
        ->dispatch('flashcardDeleted')
        ->assertRedirect(route('decks.edit', $this->deck));
    
    expect(Flashcard::find($this->flashcard->id))->toBeNull();
});

it('prevents unauthorized users from deleting flashcards', function () {
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);
    
    Livewire::test(FlashcardForm::class, ['deck' => $this->deck, 'flashcardId' => $this->flashcard->id])
        ->call('delete')
        ->assertNoRedirect(); // Should not redirect on error
    
    // Check that the flashcard still exists
    expect($this->deck->flashcards()->where('id', $this->flashcard->id)->exists())->toBe(true);
});

it('renders the component view', function () {
    Livewire::test(FlashcardForm::class, ['deck' => $this->deck])
        ->assertViewIs('livewire.flashcard-form');
}); 