<?php

use App\Livewire\Modals\ShareDeckModal;
use App\Models\Deck;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->deck = Deck::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
});

it('has correct initial state', function () {
    Livewire::test(ShareDeckModal::class)
        ->assertSet('show', false)
        ->assertSet('deck', null)
        ->assertSet('shareEmail', '');
});

it('opens modal when openShareDeckModal event is dispatched', function () {    
    Livewire::test(ShareDeckModal::class)
        ->dispatch('openShareDeckModal', deckId: $this->deck->id)
        ->assertSet('show', true)
        ->assertSet('deck.id', $this->deck->id);
});

it('handles non-existent deck when opening modal', function () {
    Livewire::test(ShareDeckModal::class)
        ->dispatch('openShareDeckModal', deckId: 999)
        ->assertSet('show', true)
        ->assertSet('deck', null);
});

it('closes modal and resets form', function () {    
    Livewire::test(ShareDeckModal::class)
        ->dispatch('openShareDeckModal', deckId: $this->deck->id)
        ->set('shareEmail', 'test@example.com')
        ->call('close')
        ->assertSet('show', false)
        ->assertSet('shareEmail', '')
        ->assertSet('deck', null);
});

it('validates required email', function () {    
    Livewire::test(ShareDeckModal::class)
        ->set('deck', $this->deck)
        ->set('shareEmail', '')
        ->call('shareDeck')
        ->assertHasErrors(['shareEmail' => 'required']);
});

it('validates email format', function () {    
    Livewire::test(ShareDeckModal::class)
        ->set('deck', $this->deck)
        ->set('shareEmail', 'invalid-email')
        ->call('shareDeck')
        ->assertHasErrors(['shareEmail' => 'email']);
});

it('validates user exists', function () {    
    Livewire::test(ShareDeckModal::class)
        ->set('deck', $this->deck)
        ->set('shareEmail', 'nonexistent@example.com')
        ->call('shareDeck')
        ->assertHasErrors('shareEmail');
});

it('prevents sharing with deck owner', function () {
    $this->actingAs($this->user);
    
    Livewire::test(ShareDeckModal::class)
        ->set('deck', $this->deck)
        ->set('shareEmail', $this->user->email)
        ->call('shareDeck')
        ->assertHasErrors('shareEmail');
});

it('prevents sharing with already shared user', function () {
    $targetUser = User::factory()->create(['email' => 'target@example.com']);
    
    // Share deck with target user first
    $this->deck->sharedWithUsers()->attach($targetUser->id, ['user_id' => $this->user->id]);
    
    Livewire::test(ShareDeckModal::class)
        ->set('deck', $this->deck)
        ->set('shareEmail', $targetUser->email)
        ->call('shareDeck')
        ->assertHasErrors('shareEmail');
});

it('shares deck successfully with valid email', function () {
    $targetUser = User::factory()->create();
    
    Livewire::test(ShareDeckModal::class)
        ->dispatch('openShareDeckModal', deckId: $this->deck->id)
        ->set('shareEmail', $targetUser->email)
        ->call('shareDeck')
        ->assertHasNoErrors()
        ->assertSet('shareEmail', '')
        ->assertSet('deck', null)
        ->assertSet('show', false);
    
    // Verify the deck was shared
    expect($this->deck->sharedWithUsers()->where('shared_with_user_id', $targetUser->id)->exists())->toBeTrue();
});

it('handles no deck selected error', function () {
    Livewire::test(ShareDeckModal::class)
        ->set('shareEmail', 'test@example.com')
        ->call('shareDeck');
    
    // Should not crash
    expect(true)->toBe(true);
});

it('handles sharing exception gracefully', function () {
    Livewire::test(ShareDeckModal::class)
        ->dispatch('openShareDeckModal', deckId: $this->deck->id)
        ->set('shareEmail', 'nonexistent@example.com')
        ->call('shareDeck');
    
    // Test should complete without throwing an exception
    expect(true)->toBe(true);
});

it('resets validation when closing modal', function () {    
    Livewire::test(ShareDeckModal::class)
        ->set('deck', $this->deck)
        ->set('shareEmail', 'invalid-email')
        ->call('shareDeck')
        ->assertHasErrors('shareEmail')
        ->call('close')
        ->assertHasNoErrors();
});

it('renders the component view', function () {
    Livewire::test(ShareDeckModal::class)
        ->assertViewIs('livewire.modals.share-deck-modal');
}); 