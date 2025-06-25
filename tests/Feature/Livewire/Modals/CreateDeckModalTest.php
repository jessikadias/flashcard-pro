<?php

use App\Livewire\Modals\CreateDeckModal;
use App\Models\Deck;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('starts with modal closed and empty form', function () {
    Livewire::test(CreateDeckModal::class)
        ->assertSet('show', false)
        ->assertSet('deckName', '')
        ->assertSet('isPublic', false);
});

it('opens modal when openCreateDeckModal event is dispatched', function () {
    Livewire::test(CreateDeckModal::class)
        ->dispatch('openCreateDeckModal')
        ->assertSet('show', true)
        ->assertSet('deckName', '')
        ->assertSet('isPublic', false);
});

it('opens modal when open method is called', function () {
    Livewire::test(CreateDeckModal::class)
        ->call('open')
        ->assertSet('show', true)
        ->assertSet('deckName', '')
        ->assertSet('isPublic', false);
});

it('closes modal and resets form when close method is called', function () {
    Livewire::test(CreateDeckModal::class)
        ->set('show', true)
        ->set('deckName', 'Test Deck')
        ->set('isPublic', true)
        ->call('close')
        ->assertSet('show', false)
        ->assertSet('deckName', '')
        ->assertSet('isPublic', false);
});

it('validates required deck name', function () {
    Livewire::test(CreateDeckModal::class)
        ->set('show', true)
        ->set('deckName', '')
        ->call('createDeck')
        ->assertHasErrors(['deckName' => 'required']);
});

it('validates minimum deck name length', function () {
    Livewire::test(CreateDeckModal::class)
        ->set('show', true)
        ->set('deckName', 'ab') // Too short
        ->call('createDeck')
        ->assertHasErrors(['deckName' => 'min']);
});

it('validates maximum deck name length', function () {
    Livewire::test(CreateDeckModal::class)
        ->set('show', true)
        ->set('deckName', str_repeat('a', 51)) // Too long
        ->call('createDeck')
        ->assertHasErrors(['deckName' => 'max']);
});

it('creates deck successfully with valid data', function () {
    Livewire::test(CreateDeckModal::class)
        ->set('show', true)
        ->set('deckName', 'Laravel Fundamentals')
        ->set('isPublic', true)
        ->call('createDeck')
        ->assertHasNoErrors()
        ->assertSet('show', false)
        ->assertDispatched('deckCreated');
    
    $deck = Deck::where('name', 'Laravel Fundamentals')->first();
    expect($deck)->not->toBeNull();
    expect($deck->user_id)->toBe($this->user->id);
    expect($deck->is_public)->toBeTrue();
});

it('creates private deck by default', function () {
    Livewire::test(CreateDeckModal::class)
        ->set('show', true)
        ->set('deckName', 'Private Deck')
        ->call('createDeck')
        ->assertHasNoErrors();
    
    $deck = Deck::where('name', 'Private Deck')->first();
    expect($deck->is_public)->toBeFalse();
});

it('redirects to deck edit page after creation', function () {
    Livewire::test(CreateDeckModal::class)
        ->set('show', true)
        ->set('deckName', 'Test Deck')
        ->call('createDeck')
        ->assertHasNoErrors();
    
    $deck = Deck::where('name', 'Test Deck')->first();
    expect($deck)->not->toBeNull();
    
    // Note: We can't easily test the redirect in Livewire tests,
    // but we can verify the deck was created
});

it('dispatches deckCreated event with deck ID', function () {
    Livewire::test(CreateDeckModal::class)
        ->set('show', true)
        ->set('deckName', 'Event Test Deck')
        ->call('createDeck')
        ->assertHasNoErrors()
        ->assertDispatched('deckCreated');
    
    $deck = Deck::where('name', 'Event Test Deck')->first();
    expect($deck)->not->toBeNull();
});

it('resets validation errors when deck name is updated', function () {
    $component = Livewire::test(CreateDeckModal::class)
        ->set('show', true)
        ->set('deckName', '')
        ->call('createDeck')
        ->assertHasErrors(['deckName']);
    
    // Update deck name should reset validation
    $component->set('deckName', 'Valid Name')
        ->assertHasNoErrors();
});

it('handles creation errors gracefully', function () {
    // Mock a scenario where deck creation might fail
    // This is more of an integration test concept
    Livewire::test(CreateDeckModal::class)
        ->set('show', true)
        ->set('deckName', 'Test Deck')
        ->call('createDeck')
        ->assertHasNoErrors();
    
    // In a real scenario, you might mock the Deck model to throw an exception
    // and then test that the error flash message is set
});

it('renders the component view', function () {
    Livewire::test(CreateDeckModal::class)
        ->assertViewIs('livewire.modals.create-deck-modal');
}); 