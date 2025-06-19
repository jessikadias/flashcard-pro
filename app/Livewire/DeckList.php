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
     * Selected deck for actions (delete/share)
     */
    public ?Deck $selectedDeck = null;

    /**
     * Show delete modal
     */
    public bool $showDeleteModal = false;

    /**
     * Show share modal
     */
    public bool $showShareModal = false;

    /**
     * Email to share deck with
     */
    public string $shareEmail = '';

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
     * Show share modal for a deck
     */
    public function openShareModal(Deck $deck)
    {
        $this->selectedDeck = $deck;
        $this->shareEmail = '';
        $this->showShareModal = true;
    }

    /**
     * Cancel sharing
     */
    public function cancelShare()
    {
        $this->selectedDeck = null;
        $this->shareEmail = '';
        $this->showShareModal = false;
    }

    /**
     * Share deck with another user
     */
    public function shareDeck()
    {
        if (!$this->selectedDeck) {
            return;
        }

        $this->validate([
            'shareEmail' => 'required|email|exists:users,email'
        ], [
            'shareEmail.exists' => 'No user found with this email address.'
        ]);

        try {
            $targetUser = \App\Models\User::where('email', $this->shareEmail)->first();
            
            // Check if deck is already shared with this user
            if ($this->selectedDeck->sharedWithUsers()->where('users.id', $targetUser->id)->exists()) {
                session()->flash('info', "This deck is already shared with {$this->shareEmail}.");
                return;
            }

            // Share the deck
            $this->selectedDeck->sharedWithUsers()->attach($targetUser->id, [
                'user_id' => $this->selectedDeck->user_id
            ]);            
            session()->flash('success', "Deck '{$this->selectedDeck->name}' has been shared with {$this->shareEmail}.");
        } catch (\Exception $e) {
            \Log::error('Failed to share deck', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Failed to share deck. Please try again.');
        }

        // Hide share modal after sharing and clearing inputs
        $this->cancelShare();
    }

    /**
     * Confirm deck deletion with user
     */
    public function confirmDeletion(Deck $deck)
    {
        $this->selectedDeck = $deck;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel deck deletion
     */
    public function cancelDelete()
    {
        $this->selectedDeck = null;
        $this->showDeleteModal = false;
    }

    /**
     * Delete a deck (only if user owns it)
     */
    public function deleteDeck()
    {
        if (!$this->selectedDeck) {
            return;
        }

        // Check if user can delete this deck
        if (!$this->selectedDeck->canEdit(auth()->user())) {
            session()->flash('error', 'You can only delete your own decks.');
            $this->cancelDelete();
            return;
        }

        try {
            $deckName = $this->selectedDeck->name;
            $this->selectedDeck->delete();

            session()->flash('success', "Deck '{$deckName}' has been deleted successfully.");
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete deck. Please try again.');
        }

        // Clean the modal and the selectedDeck
        $this->cancelDelete();
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
     * Load more decks
     */
    public function loadMore()
    {
        $this->perPage += 10;
    }
} 