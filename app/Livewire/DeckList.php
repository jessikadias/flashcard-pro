<?php

namespace App\Livewire;

use App\Models\Deck;
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
        
        return $query->orderBy('name')
                    ->paginate($this->perPage);
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
     * Delete a deck (only if user owns it)
     */
    public function deleteDeck(Deck $deck)
    {
        // Check if user can delete this deck
        if (!$deck->canEdit(auth()->user())) {
            session()->flash('error', 'You can only delete your own decks.');
            return;
        }

        // Confirm deletion
        if (!$this->confirmDeletion($deck)) {
            return;
        }

        try {
            $deckName = $deck->name;
            $deck->delete();

            session()->flash('success', "Deck '{$deckName}' has been deleted successfully.");
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete deck. Please try again.');
        }
    }

    /**
     * Toggle public/private status of a deck
     */
    public function togglePublicStatus(Deck $deck)
    {
        // Check if user can edit this deck
        if (!$deck->canEdit(auth()->user())) {
            session()->flash('error', 'You can only modify your own decks.');
            return;
        }

        try {
            $deck->update(['is_public' => !$deck->is_public]);
            
            $status = $deck->is_public ? 'public' : 'private';
            session()->flash('success', "Deck '{$deck->name}' is now {$status}.");
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update deck status. Please try again.');
        }
    }

    /**
     * Check if user can edit a specific deck
     */
    public function canEdit(Deck $deck): bool
    {
        return $deck->canEdit(auth()->user());
    }

    /**
     * Check if user can delete a specific deck
     */
    public function canDelete(Deck $deck): bool
    {
        return $deck->canEdit(auth()->user());
    }

    /**
     * Check if deck is shared with current user
     */
    public function isShared(Deck $deck): bool
    {
        return $deck->user_id !== auth()->id() && 
               auth()->user()->sharedDecks()->where('decks.id', $deck->id)->exists();
    }

    /**
     * Confirm deck deletion with user
     */
    private function confirmDeletion(Deck $deck): bool
    {
        return true;
    }

    /**
     * Load more decks
     */
    public function loadMore()
    {
        $this->perPage += 10;
    }
} 