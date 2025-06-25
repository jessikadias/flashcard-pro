<?php

use App\Livewire\Modals\RemoveDeckSharingModal;
use App\Models\Deck;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->deck = Deck::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
});

it('has correct initial state', function () {
    Livewire::test(RemoveDeckSharingModal::class)
        ->assertSet('showModal', false)
        ->assertSet('deckId', null)
        ->assertSet('deck', null);
});

it('opens modal when openRemoveDeckSharingModal event is dispatched', function () {
    Livewire::test(RemoveDeckSharingModal::class)
        ->dispatch('openRemoveDeckSharingModal', deckId: $this->deck->id)
        ->assertSet('showModal', true)
        ->assertSet('deckId', $this->deck->id)
        ->assertSet('deck.id', $this->deck->id);
});

it('handles non-existent deck when opening modal', function () {
    Livewire::test(RemoveDeckSharingModal::class)
        ->dispatch('openRemoveDeckSharingModal', deckId: 999)
        ->assertSet('showModal', true)
        ->assertSet('deckId', 999)
        ->assertSet('deck', null);
});

it('opens modal when openModal method is called', function () {
    Livewire::test(RemoveDeckSharingModal::class)
        ->call('openModal', $this->deck->id)
        ->assertSet('showModal', true)
        ->assertSet('deckId', $this->deck->id)
        ->assertSet('deck.id', $this->deck->id);
});

it('closes modal and resets properties', function () {
    Livewire::test(RemoveDeckSharingModal::class)
        ->set('showModal', true)
        ->set('deckId', $this->deck->id)
        ->set('deck', $this->deck)
        ->call('closeModal')
        ->assertSet('showModal', false)
        ->assertSet('deckId', null)
        ->assertSet('deck', null);
});

it('dispatches deckSharingRemoved event when confirmRemove is called', function () {
    Livewire::test(RemoveDeckSharingModal::class)
        ->set('deckId', $this->deck->id)
        ->set('showModal', true)
        ->call('confirmRemove')
        ->assertDispatched('deckSharingRemoved', $this->deck->id)
        ->assertSet('showModal', false)
        ->assertSet('deckId', null)
        ->assertSet('deck', null);
});

it('does not dispatch event when no deck ID is set', function () {
    Livewire::test(RemoveDeckSharingModal::class)
        ->set('deckId', null)
        ->set('showModal', true)
        ->call('confirmRemove')
        ->assertNotDispatched('deckSharingRemoved');
});

it('handles zero deck ID correctly', function () {
    Livewire::test(RemoveDeckSharingModal::class)
        ->set('deckId', 0)
        ->set('showModal', true)
        ->call('confirmRemove')
        ->assertNotDispatched('deckSharingRemoved');
});

it('renders the component view', function () {
    Livewire::test(RemoveDeckSharingModal::class)
        ->assertViewIs('livewire.modals.remove-deck-sharing-modal');
}); 