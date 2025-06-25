<?php

use App\Livewire\Modals\DeleteFlashcardModal;
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

it('has correct initial state', function () {
    Livewire::test(DeleteFlashcardModal::class)
        ->assertSet('show', false)
        ->assertSet('flashcardId', null);
});

it('opens modal when openDeleteFlashcardModal event is dispatched', function () {
    Livewire::test(DeleteFlashcardModal::class)
        ->dispatch('openDeleteFlashcardModal', flashcardId: $this->flashcard->id)
        ->assertSet('show', true)
        ->assertSet('flashcardId', $this->flashcard->id);
});

it('opens modal when open method is called', function () {
    Livewire::test(DeleteFlashcardModal::class)
        ->call('open', $this->flashcard->id)
        ->assertSet('show', true)
        ->assertSet('flashcardId', $this->flashcard->id);
});

it('closes modal and resets flashcard ID', function () {
    Livewire::test(DeleteFlashcardModal::class)
        ->set('show', true)
        ->set('flashcardId', $this->flashcard->id)
        ->call('close')
        ->assertSet('show', false)
        ->assertSet('flashcardId', null);
});

it('dispatches flashcardDeleted event when delete is called', function () {
    Livewire::test(DeleteFlashcardModal::class)
        ->set('flashcardId', $this->flashcard->id)
        ->set('show', true)
        ->call('delete')
        ->assertDispatched('flashcardDeleted', $this->flashcard->id)
        ->assertSet('show', false)
        ->assertSet('flashcardId', null);
});

it('does not dispatch event when no flashcard ID is set', function () {
    Livewire::test(DeleteFlashcardModal::class)
        ->set('flashcardId', null)
        ->set('show', true)
        ->call('delete')
        ->assertNotDispatched('flashcardDeleted');
});

it('handles zero flashcard ID correctly', function () {
    Livewire::test(DeleteFlashcardModal::class)
        ->set('flashcardId', 0)
        ->set('show', true)
        ->call('delete')
        ->assertNotDispatched('flashcardDeleted');
});

it('renders the component view', function () {
    Livewire::test(DeleteFlashcardModal::class)
        ->assertViewIs('livewire.modals.delete-flashcard-modal');
}); 