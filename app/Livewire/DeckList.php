<?php

namespace App\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class DeckList extends Component
{
    use AuthorizesRequests, WithPagination;

    /**
     * Search term for filtering decks
     */
    public string $search = '';

    /**
     * Number of decks to show per page
     */
    public int $perPage = 10;

    /**
     * Livewire pagination theme
     */
    protected string $paginationTheme = 'tailwind';

    protected $listeners = [
        'deckDeleted' => '$refresh',
        'deckSharingRemoved' => 'handleDeckSharingRemoved',
    ];

    /**
     * Render the component
     */
    public function render()
    {
        $decks = $this->getFilteredDecks();

        return view('livewire.deck-list', [
            'decks' => $decks,
        ]);
    }

    /**
     * Get decks based on current filters and search
     */
    private function getFilteredDecks()
    {
        $query = auth()->user()->accessibleDecks()
            ->with(['user', 'flashcards'])
            ->withCount('flashcards');

        // Apply search filter only if 3 or more characters
        if (strlen($this->search) >= 3) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        
        $decks = $query->orderBy('name')->paginate($this->perPage);
        return $decks;
    }

    /**
     * Update search and reset pagination
     */
    public function updatedSearch()
    {
        $this->resetPage();
        $this->perPage = 10;
        $this->dispatch('searchUpdated');
    }

    /**
     * Load more decks
     */
    public function loadMore()
    {
        $this->perPage += 10;
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