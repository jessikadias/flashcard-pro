<?php

use App\Livewire\DeckList;
use App\Models\Deck;
use App\Models\User;
use App\Models\Flashcard;
use Livewire\Livewire;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    
    // Create 15 decks for the user
    Deck::factory(15)->create(['user_id' => $this->user->id]);
});

it('renders with default settings', function () {
    Livewire::test(DeckList::class)
        ->assertSet('search', '')
        ->assertSet('loadPerBatch', 10)
        ->assertSet('currentOffset', 10) // After initial load
        ->assertSet('hasMoreDecks', true)
        ->assertSet('isLoading', false);
});

it('displays user decks with initial load', function () {
    $component = Livewire::test(DeckList::class);
    
    $decks = $component->viewData('decks');
    expect($decks)->toHaveCount(10); // Initial batch size
    expect($decks->first()->user_id)->toBe($this->user->id);
});

it('loads more decks when loadMore is called', function () {
    $component = Livewire::test(DeckList::class)
        ->assertSet('currentOffset', 10)
        ->call('loadMore')
        ->assertSet('currentOffset', 15) // All 15 decks loaded
        ->assertSet('hasMoreDecks', false); // No more decks available
    
    $decks = $component->viewData('decks');
    expect($decks)->toHaveCount(15); // All decks loaded
});

it('filters decks by search term', function () {
    // Create a specific deck to search for
    $searchDeck = Deck::factory()->create([
        'name' => 'Laravel Fundamentals',
        'user_id' => $this->user->id
    ]);
    
    $component = Livewire::test(DeckList::class)
        ->set('search', 'Laravel');
    
    $decks = $component->viewData('decks');
    expect($decks)->toHaveCount(1);
    expect($decks->first()->name)->toBe('Laravel Fundamentals');
});

it('only applies search filter for 3 or more characters', function () {
    // Create a deck with "La" in the name
    Deck::factory()->create([
        'name' => 'Laravel Test',
        'user_id' => $this->user->id
    ]);
    
    $component = Livewire::test(DeckList::class)
        ->set('search', 'La');
    
    $decks = $component->viewData('decks');
    // Should show all decks since search is too short
    expect($decks->count())->toBeGreaterThan(1);
});

it('resets loaded decks when search is updated', function () {
    $component = Livewire::test(DeckList::class)
        ->call('loadMore') // Load more decks
        ->assertSet('currentOffset', 15)
        ->set('search', 'test')
        ->assertSet('currentOffset', 0); // Should reset after search
    
    $decks = $component->viewData('decks');
    expect($decks->count())->toBeLessThanOrEqual(10); // Back to initial batch or filtered results
});

it('dispatches searchUpdated event when search changes', function () {
    Livewire::test(DeckList::class)
        ->set('search', 'test')
        ->assertDispatched('searchUpdated');
});

it('prevents loading more when already loading', function () {
    $component = Livewire::test(DeckList::class)
        ->set('isLoading', true)
        ->call('loadMore')
        ->assertSet('isLoading', true); // Should remain true, no change in offset
});

it('prevents loading more when no more decks available', function () {
    $component = Livewire::test(DeckList::class)
        ->set('hasMoreDecks', false)
        ->call('loadMore');
    
    // Should not change anything when no more decks available
    $decks = $component->viewData('decks');
    expect($decks)->toHaveCount(10); // Still initial batch
});

it('handles deckDeleted event by removing specific deck', function () {
    $component = Livewire::test(DeckList::class);
    
    // Get the first deck from the loaded decks (which are alphabetically sorted)
    $deck = $component->get('loadedDecks')->first();
    $initialCount = $component->viewData('decks')->count();
    
    $component->dispatch('deckDeleted', deckId: $deck->id);
    
    $decks = $component->viewData('decks');
    expect($decks->count())->toBe($initialCount - 1);
    expect($decks->contains('id', $deck->id))->toBeFalse();
});

it('handles deckDeleted event without deckId by refreshing', function () {
    $component = Livewire::test(DeckList::class)
        ->call('loadMore') // Load more decks first
        ->dispatch('deckDeleted') // No deckId provided
        ->assertSet('currentOffset', 10); // Should reset to initial state
});

it('handles deckSharingRemoved event', function () {
    // Create another user who will share a deck with our test user
    $otherUser = User::factory()->create();
    $sharedDeck = Deck::factory()->create(['user_id' => $otherUser->id]);
    
    // Share the deck with our test user using the attach method with user_id pivot
    $sharedDeck->sharedWithUsers()->attach($this->user->id, [
        'user_id' => $otherUser->id,  // Deck owner as pivot data
    ]);
    
    // Verify the deck is initially accessible
    expect($this->user->accessibleDecks()->where('id', $sharedDeck->id)->exists())->toBeTrue();    
   
    $component = Livewire::test(DeckList::class)
        ->set('loadPerBatch', 20)
        ->call('loadMore'); // Load more so all decks are loaded

    $initialDecks = $component->viewData('decks');
    
    // Verify the shared deck is in the list
    expect($initialDecks->contains('id', $sharedDeck->id))->toBeTrue();
    
    // Dispatch the deckSharingRemoved event
    $component->dispatch('deckSharingRemoved', deckId: $sharedDeck->id)
        ->assertDispatched('$refresh'); // Should dispatch refresh event
    
    // Verify the sharing relationship was removed from database
    expect(DB::table('shared_decks')
        ->where('deck_id', $sharedDeck->id)
        ->where('shared_with_user_id', $this->user->id)
        ->exists())->toBeFalse();
    
    // Verify the deck is no longer accessible
    expect($this->user->accessibleDecks()->where('id', $sharedDeck->id)->exists())->toBeFalse();
});

it('handles deckSharingRemoved event with unauthorized deck', function () {
    // Create another user's deck that is NOT shared with our test user
    $otherUser = User::factory()->create();
    $unsharedDeck = Deck::factory()->create(['user_id' => $otherUser->id]);
    
    // Verify the deck is not accessible to our user
    expect($this->user->accessibleDecks()->where('id', $unsharedDeck->id)->exists())->toBeFalse();
    
    // Verify no sharing record exists in database
    expect(DB::table('shared_decks')
        ->where('deck_id', $unsharedDeck->id)
        ->where('shared_with_user_id', $this->user->id)
        ->exists())->toBeFalse();
    
    $component = Livewire::test(DeckList::class);
    
    // Dispatch the deckSharingRemoved event for a deck not shared with the user
    // This should fail silently or show an error, but shouldn't change anything
    $component->dispatch('deckSharingRemoved', deckId: $unsharedDeck->id);
    
    // Verify no sharing record was created or removed (should remain false)
    expect(DB::table('shared_decks')
        ->where('deck_id', $unsharedDeck->id)
        ->where('shared_with_user_id', $this->user->id)
        ->exists())->toBeFalse();
    
    // Verify the deck is still not accessible
    expect($this->user->accessibleDecks()->where('id', $unsharedDeck->id)->exists())->toBeFalse();
});

it('orders decks alphabetically by name', function () {
    // Clear existing decks to ensure predictable ordering
    $this->user->decks()->delete();
    
    // Create specific decks with known names
    Deck::factory()->create(['name' => 'Zebra Deck', 'user_id' => $this->user->id]);
    Deck::factory()->create(['name' => 'Alpha Deck', 'user_id' => $this->user->id]);
    Deck::factory()->create(['name' => 'Beta Deck', 'user_id' => $this->user->id]);
    
    $component = Livewire::test(DeckList::class);
    $decks = $component->viewData('decks');
    
    expect($decks->first()->name)->toBe('Alpha Deck');
    expect($decks->get(1)->name)->toBe('Beta Deck');
    expect($decks->last()->name)->toBe('Zebra Deck');
});

it('includes flashcard count for each deck', function () {
    // Clear existing decks and create a specific one
    $this->user->decks()->delete();
    $deck = Deck::factory()->for($this->user)->create();
    Flashcard::factory()->for($deck)->create();

    $component = Livewire::test(DeckList::class);
    $decks = $component->viewData('decks');
    
    // Find the deck and check if it has flashcard_count
    $deckWithCount = $decks->where('id', $deck->id)->first();
    expect($deckWithCount)->not->toBeNull();
    expect($deckWithCount->flashcard_count)->toBe(1);
});

it('shows only accessible decks for the user', function () {
    $otherUser = User::factory()->create();
    $otherDeck = Deck::factory()->create(['user_id' => $otherUser->id]);
    
    $component = Livewire::test(DeckList::class);
    $decks = $component->viewData('decks');
    
    expect($decks->contains('id', $otherDeck->id))->toBeFalse();
    expect($decks->every(fn($deck) => $deck->user_id === $this->user->id))->toBeTrue();
});

it('maintains loaded decks state across multiple loadMore calls', function () {
    $component = Livewire::test(DeckList::class);
    
    // Initial load should have 10 decks
    $initialDecks = $component->viewData('decks');
    expect($initialDecks)->toHaveCount(10);
    
    // Load more should add to the collection
    $component->call('loadMore');
    $allDecks = $component->viewData('decks');
    expect($allDecks)->toHaveCount(15);
    
    // Should contain all the initial decks plus new ones
    foreach ($initialDecks as $deck) {
        expect($allDecks->contains('id', $deck->id))->toBeTrue();
    }
}); 