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

    protected $listeners = ['deckDeleted' => '$refresh'];

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
     * Check if user can edit a specific deck
     */
    public function canEdit(Deck $deck): bool
    {
        return $deck->canEdit(auth()->user());
    }

    /**
     * Load more decks
     */
    public function loadMore()
    {
        $this->perPage += 10;
    }
} 