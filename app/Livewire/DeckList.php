<?php

namespace App\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Collection;

#[Title('My Decks')]
class DeckList extends Component
{
    use AuthorizesRequests;

    /**
     * Search term for filtering decks
     */
    public string $search = '';

    /**
     * Number of decks to load per batch
     */
    public int $loadPerBatch = 10;

    /**
     * Current offset for loading decks
     */
    public int $currentOffset = 0;

    /**
     * Collection of loaded decks
     */
    public Collection $loadedDecks;

    /**
     * Whether there are more decks to load
     */
    public bool $hasMoreDecks = true;

    /**
     * Whether we're currently loading more decks
     */
    public bool $isLoading = false;

    protected $listeners = [
        'deckDeleted' => 'handleDeckDeleted',
        'deckSharingRemoved' => 'handleDeckSharingRemoved',
        'loadMoreDecks' => 'loadMore',
    ];

    /**
     * Initialize the component
     */
    public function mount()
    {
        $this->loadedDecks = collect();
        $this->loadInitialDecks();
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.deck-list', [
            'decks' => $this->loadedDecks,
        ]);
    }

    /**
     * Load initial batch of decks
     */
    private function loadInitialDecks()
    {
        $this->currentOffset = 0;
        $this->hasMoreDecks = true;
        $decks = $this->fetchDecks($this->loadPerBatch, 0);
        
        $this->loadedDecks = $decks;
        $this->currentOffset = $decks->count();
        $this->hasMoreDecks = $decks->count() === $this->loadPerBatch;
    }

    /**
     * Fetch decks from database
     */
    private function fetchDecks(int $limit, int $offset)
    {
        $query = auth()->user()->accessibleDecks()->with(['user', 'flashcards']);

        // Apply search filter only if 3 or more characters
        if (strlen($this->search) >= 3) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        
        return $query->orderBy('name')
                    ->skip($offset)
                    ->take($limit)
                    ->get();
    }

    /**
     * Update search and reset decks
     */
    public function updatedSearch()
    {
        $this->loadInitialDecks();
        $this->dispatch('searchUpdated');
    }

    /**
     * Load more decks
     */
    public function loadMore()
    {
        if (!$this->hasMoreDecks || $this->isLoading) {
            return;
        }

        $this->isLoading = true;

        $newDecks = $this->fetchDecks($this->loadPerBatch, $this->currentOffset);
        
        // Add new decks to the existing collection
        $this->loadedDecks = $this->loadedDecks->concat($newDecks);
        
        // Update offset and check if there are more decks
        $this->currentOffset += $newDecks->count();
        $this->hasMoreDecks = $newDecks->count() === $this->loadPerBatch;
        
        $this->isLoading = false;
    }

    /**
     * Handle deck deletion
     */
    public function handleDeckDeleted($deckId = null)
    {
        if ($deckId) {
            $this->loadedDecks = $this->loadedDecks->reject(function ($deck) use ($deckId) {
                return $deck->id === $deckId;
            });
        } else {
            // Refresh all decks if no specific deck ID
            $this->loadInitialDecks();
        }
    }

    /**
     * Handle deck sharing removal.
     */
    public function handleDeckSharingRemoved($deckId = null)
    {
        if (!$deckId) {
            return;
        }

        try {
            $deck = \App\Models\Deck::find($deckId);
            
            if (!$deck) {
                session()->flash('error', 'Deck not found.');
                return;
            }

            // Check if user has access to this deck (is shared with them)
            $isSharedWith = $deck->sharedWithUsers()
                                ->where('shared_with_user_id', auth()->id())
                                ->exists();

            if (!$isSharedWith) {
                session()->flash('error', 'You are not authorized to perform this action.');
                return;
            }

            // Remove the sharing relationship
            $deck->sharedWithUsers()->where('shared_with_user_id', auth()->id())->detach();
            
            session()->flash('success', 'Deck removed from your shared decks.');
            
            // Refresh the component to update the deck list
            $this->dispatch('$refresh');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to remove deck sharing.');
        }
    }
} 