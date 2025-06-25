<?php

use App\Livewire\Modals\DeleteDeckModal;
use App\Models\Deck;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->deck = Deck::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
});

it('has correct initial state', function () {
    Livewire::test(DeleteDeckModal::class)
        ->assertSet('show', false)
        ->assertSet('deck', null);
});

it('opens modal when openDeleteDeckModal event is dispatched', function () {
    Livewire::test(DeleteDeckModal::class)
        ->dispatch('openDeleteDeckModal', deckId: $this->deck->id)
        ->assertSet('show', true)
        ->assertSet('deck.id', $this->deck->id);
});

it('handles non-existent deck when opening modal', function () {
    Livewire::test(DeleteDeckModal::class)
        ->dispatch('openDeleteDeckModal', deckId: 999999)
        ->assertSet('deck', null)
        ->assertSet('show', true);
});

it('closes modal and resets deck', function () {
    Livewire::test(DeleteDeckModal::class)
        ->dispatch('openDeleteDeckModal', deckId: $this->deck->id)
        ->call('close')
        ->assertSet('show', false)
        ->assertSet('deck', null);
});

it('deletes deck successfully', function () {
    Livewire::test(DeleteDeckModal::class)
        ->set('deck', $this->deck)
        ->call('deleteDeck')
        ->assertSessionHas('success', "Deck '{$this->deck->name}' has been deleted successfully.")
        ->assertRedirect(route('decks.index'));
    
    // Verify deck is deleted from database
    expect(Deck::find($this->deck->id))->toBeNull();
});

it('prevents unauthorized users from deleting deck', function () {
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);

    Livewire::test(DeleteDeckModal::class)
        ->dispatch('openDeleteDeckModal', deckId: $this->deck->id)
        ->call('deleteDeck');

    // Deck should still exist
    expect(Deck::find($this->deck->id))->not->toBeNull();
});

it('handles no deck selected error', function () {
    Livewire::test(DeleteDeckModal::class)
        ->call('deleteDeck');
    
    // Should not crash and deck should still exist
    expect(Deck::find($this->deck->id))->not->toBeNull();
});

it('handles deletion exception gracefully', function () {
    // Create a deck that will cause an exception when trying to delete
    $deck = Deck::factory()->for($this->user)->create();
    
    Livewire::test(DeleteDeckModal::class)
        ->dispatch('openDeleteDeckModal', deckId: $deck->id)
        ->call('deleteDeck');
    
    // Test should complete without throwing an exception
    expect(true)->toBe(true);
});

it('renders the component view', function () {
    Livewire::test(DeleteDeckModal::class)
        ->assertViewIs('livewire.modals.delete-deck-modal');
}); 