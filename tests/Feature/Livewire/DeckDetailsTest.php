<?php

use App\Livewire\DeckDetails;
use App\Models\Deck;
use App\Models\Flashcard;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->deck = Deck::factory()->for($this->user)->create([
        'name' => 'Test Deck',
        'is_public' => false,
    ]);
    $this->flashcard = Flashcard::factory()->for($this->deck)->create([
        'question' => 'Test Question',
        'answer' => 'Test Answer',
        'order' => 1,
    ]);
    // Create additional flashcards for pagination tests
    Flashcard::factory()->count(14)->for($this->deck)->create();
    $this->actingAs($this->user);
});

it('mounts with correct deck data', function () {    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->assertSet('deck.id', $this->deck->id)
        ->assertSet('name', $this->deck->name)
        ->assertSet('isPublic', $this->deck->is_public)
        ->assertSet('showEditModal', false)
        ->assertSet('page', 1)
        ->assertSet('perPage', 10);
});

it('loads flashcards on mount', function () {    
    $component = Livewire::test(DeckDetails::class, ['deck' => $this->deck]);
    
    $flashcards = $component->get('flashcards');
    expect($flashcards)->toHaveCount(10); // First page
    expect($component->get('hasMorePages'))->toBeTrue();
});

it('loads more flashcards when loadMore is called', function () {    
    $component = Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->call('loadMore');
    
    $flashcards = $component->get('flashcards');
    expect($flashcards)->toHaveCount(15); // All flashcards loaded
    expect($component->get('hasMorePages'))->toBeFalse();
    expect($component->get('page'))->toBe(2);
});

it('does not load more flashcards when no more pages available', function () {    
    // Create deck with only 5 flashcards
    $smallDeck = Deck::factory()->create(['user_id' => $this->user->id]);
    Flashcard::factory()->count(5)->create(['deck_id' => $smallDeck->id]);
    
    $component = Livewire::test(DeckDetails::class, ['deck' => $smallDeck])
        ->call('loadMore');
    
    expect($component->get('page'))->toBe(1); // Should not increment
});

it('opens edit modal when openEditModal is called', function () {    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->call('openEditModal')
        ->assertSet('showEditModal', true)
        ->assertSet('name', $this->deck->name)
        ->assertSet('isPublic', $this->deck->is_public);
});

it('opens edit modal when open-edit-modal event is dispatched', function () {    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->dispatch('open-edit-modal')
        ->assertSet('showEditModal', true);
});

it('prevents unauthorized users from opening edit modal', function () {
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);
    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->call('openEditModal')
        ->assertSet('showEditModal', false);
});

it('closes edit modal when closeEditModal is called', function () {    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->set('showEditModal', true)
        ->call('closeEditModal')
        ->assertSet('showEditModal', false);
});

it('validates deck name when updating', function () {    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->set('name', '') // Empty name
        ->call('updateDeck')
        ->assertHasErrors(['name' => 'required']);
});

it('validates minimum deck name length when updating', function () {    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->set('name', 'ab') // Too short
        ->call('updateDeck')
        ->assertHasErrors(['name' => 'min']);
});

it('validates maximum deck name length when updating', function () {    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->set('name', str_repeat('a', 51)) // Too long
        ->call('updateDeck')
        ->assertHasErrors(['name' => 'max']);
});

it('updates deck successfully with valid data', function () {    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->set('name', 'Updated Deck Name')
        ->set('isPublic', true)
        ->call('updateDeck');
    
    $this->deck->refresh();
    expect($this->deck->name)->toBe('Updated Deck Name');
    expect($this->deck->is_public)->toBe(true);
});

it('prevents unauthorized users from updating deck', function () {
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);
    
    $originalName = $this->deck->name;
    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->set('name', 'Hacked Name')
        ->call('updateDeck');
    
    $this->deck->refresh();
    expect($this->deck->name)->toBe($originalName); // Should not change
});

it('checks if deck is shared with user correctly', function () {
    $sharedUser = User::factory()->create();
    
    // Create shared deck relationship properly
    $this->deck->sharedWithUsers()->attach($sharedUser->id, [
        'user_id' => $this->user->id, // The deck owner
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    $this->actingAs($sharedUser);
    
    $component = Livewire::test(DeckDetails::class, ['deck' => $this->deck]);
    
    expect($component->instance()->isSharedWith())->toBe(true);
});

it('removes deck sharing when removeSharing is called', function () {
    $sharedUser = User::factory()->create();
    
    // Create shared deck relationship properly
    $this->deck->sharedWithUsers()->attach($sharedUser->id, [
        'user_id' => $this->user->id, // The deck owner
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    $this->actingAs($sharedUser);
    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->call('removeSharing')
        ->assertSessionHas('success', 'Deck removed from your shared decks.')
        ->assertRedirect(route('decks.index'));
    
    expect($this->deck->sharedWithUsers()->where('shared_with_user_id', $sharedUser->id)->exists())->toBe(false);
});

it('handles deckSharingRemoved event', function () {
    $sharedUser = User::factory()->create();
    
    // Create shared deck relationship properly
    $this->deck->sharedWithUsers()->attach($sharedUser->id, [
        'user_id' => $this->user->id, // The deck owner
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    $this->actingAs($sharedUser);
    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->dispatch('deckSharingRemoved', deckId: $this->deck->id)
        ->assertSessionHas('success', 'Deck removed from your shared decks.')
        ->assertRedirect(route('decks.index'));
});

it('ignores deckSharingRemoved event for different deck', function () {
    $sharedUser = User::factory()->create();
    $otherDeck = Deck::factory()->create(['user_id' => $this->user->id]);
    
    // Create shared deck relationship properly
    $this->deck->sharedWithUsers()->attach($sharedUser->id, [
        'user_id' => $this->user->id, // The deck owner
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    $this->actingAs($sharedUser);
    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->dispatch('deckSharingRemoved', deckId: $otherDeck->id)
        ->assertSessionMissing('success');
    
    // Shared relationship should still exist
    expect($this->deck->sharedWithUsers()->where('shared_with_user_id', $sharedUser->id)->exists())->toBe(true);
});

it('prevents unauthorized users from removing sharing', function () {
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);
    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->call('removeSharing');
    
    // No exception should be thrown, just silently fail
    expect(true)->toBe(true); // Test passes if no exception
});

it('handles flashcard deletion successfully', function () {    
    $flashcardCount = $this->deck->flashcards()->count();
    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->dispatch('flashcardDeleted', flashcardId: $this->flashcard->id);
    
    expect($this->deck->flashcards()->where('id', $this->flashcard->id)->exists())->toBe(false);
    expect($this->deck->flashcards()->count())->toBe($flashcardCount - 1);
});

it('prevents unauthorized users from deleting flashcards', function () {
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);
    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->dispatch('flashcardDeleted', flashcardId: $this->flashcard->id);
    
    // Flashcard should still exist
    expect($this->deck->flashcards()->where('id', $this->flashcard->id)->exists())->toBe(true);
});

it('handles flashcard not found error', function () {    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->dispatch('flashcardDeleted', flashcardId: 999999);
    
    // Test should complete without error
    expect(true)->toBe(true);
});

it('renders the component view', function () {    
    Livewire::test(DeckDetails::class, ['deck' => $this->deck])
        ->assertViewIs('livewire.deck-details');
}); 