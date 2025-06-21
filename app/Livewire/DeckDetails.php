<?php

namespace App\Livewire;

use App\Models\Deck;
use Livewire\Component;
use Livewire\Attributes\On;

class DeckDetails extends Component
{
    /**
     * The deck.
     *
     * @var Deck|null
     */
    public ?Deck $deck = null;
    public string $name = '';

    /**
     * Whether the edit modal is shown.
     *
     * @var bool
     */
    public bool $showEditModal = false;

    /**
     * The flashcards.
     *
     * @var Collection
     */
    public $flashcards;

    /**
     * The page number.
     *
     * @var int
     */
    public int $page = 1;

    /**
     * The number of flashcards per page.
     *
     * @var int
     */
    public int $perPage = 10;

    /**
     * Whether there are more pages of flashcards.
     *
     * @var bool
     */
    public bool $hasMorePages;

    /**
     * The rules for the deck name.
     *
     * @var array
     */
    protected $rules = [
        'name' => 'required|min:3|max:50',
    ];

    /**
     * The messages for the deck name.
     *
     * @var array
     */
    protected $messages = [
        'name.required' => 'Please enter a deck name.',
        'name.min' => 'Deck name must be at least 3 characters.',
        'name.max' => 'Deck name cannot exceed 50 characters.',
    ];

    /**
     * Mount the component.
     *
     * @param Deck $deck
     */
    public function mount(Deck $deck)
    {
        $this->deck = $deck;
        $this->name = $deck->name;
        $this->loadFlashcards();
    }

    /**
     * Load the flashcards.
     */
    public function loadFlashcards()
    {
        $paginator = $this->deck->flashcards()
            ->latest()
            ->paginate(perPage: $this->perPage, page: $this->page);

        $newFlashcards = collect($paginator->items());

        $this->flashcards = $this->page === 1
            ? $newFlashcards
            : $this->flashcards->concat($newFlashcards);

        $this->hasMorePages = $paginator->hasMorePages();
    }

    /**
     * Load more flashcards.
     */
    public function loadMore()
    {
        if ($this->hasMorePages) {
            $this->page++;
            $this->loadFlashcards();
        }
    }

    #[On('open-edit-modal')]
    public function openEditModal()
    {
        if (!$this->isOwner()) {
            return;
        }
        $this->name = $this->deck->name; // Reset name to current deck name
        $this->showEditModal = true;
    }

    /**
     * Close the edit modal.
     */
    public function closeEditModal()
    {
        $this->showEditModal = false;
    }

    /**
     * Update the title of the deck.
     */
    public function updateTitle()
    {
        if (!$this->isOwner()) {
            session()->flash('error', 'You are not authorized to perform this action.');
            return;
        }

        if (!$this->deck) {
            return;
        }

        $this->validate();

        try {
            $this->deck->update(['name' => $this->name]);
            $this->deck->refresh();
            $this->closeEditModal();
            session()->flash('success', 'Deck title updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update deck title.');
        }
    }

    /**
     * Check if the user is the owner of the deck.
     *
     * @return bool
     */
    public function isOwner(): bool
    {
        return $this->deck && $this->deck->user_id === auth()->id();
    }

    /**
     * Check if the user has been shared the deck.
     *
     * @return bool
     */
    public function isSharedWith(): bool
    {
        return $this->deck && $this->deck->sharedWithUsers()->where('shared_with_user_id', auth()->id())->exists();
    }

    /**
     * Remove deck sharing for the current user.
     */
    #[On('remove-deck-sharing')]
    public function removeSharing()
    {
        if (!$this->isSharedWith()) {
            session()->flash('error', 'You are not authorized to perform this action.');
            return;
        }

        try {
            $this->deck->sharedWithUsers()->where('shared_with_user_id', auth()->id())->detach();
            session()->flash('success', 'Deck removed from your shared decks.');
            
            // Redirect to decks index since user no longer has access
            return redirect()->route('decks.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to remove deck sharing.');
        }
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.deck-details');
    }
}
