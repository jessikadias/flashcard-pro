<?php

namespace App\Livewire;

use App\Models\Deck;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class DeckDetails extends Component
{
    /**
     * The deck.
     *
     * @var Deck|null
     */
    public ?Deck $deck = null;
    public string $name = '';
    public bool $isPublic = false;

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
        $this->isPublic = $deck->is_public;
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
        if (!auth()->user()->can('edit', $this->deck)) {
            return;
        }

        $this->name = $this->deck->name; // Reset name to current deck name
        $this->isPublic = $this->deck->is_public; // Reset public status to current deck status
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
     * Update the deck details (name and visibility).
     */
    public function updateDeck()
    {      
        if (!$this->deck) {
            return;
        }

        if (!auth()->user()->can('edit', $this->deck)) {
            session()->flash('error', 'You are not authorized to perform this action.');
            return;
        }

        $this->validate();

        try {
            $this->deck->update([
                'name' => $this->name,
                'is_public' => $this->isPublic
            ]);
            $this->deck->refresh();
            $this->closeEditModal();
            session()->flash('success', 'Deck updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update deck.');
        }
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
    #[On('deckSharingRemoved')]
    public function removeSharing($deckId = null)
    {
        // If deckId is provided, check if it matches current deck
        if ($deckId && $deckId !== $this->deck->id) {
            return;
        }

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
     * Handle flashcard deletion.
     */
    #[On('flashcardDeleted')]
    public function handleFlashcardDeleted(int $flashcardId)
    {
        if (!auth()->user()->can('edit', $this->deck)) {
            session()->flash('error', 'You are not authorized to perform this action.');
            return;
        }

        try {
            $flashcard = $this->deck->flashcards()->find($flashcardId);
            
            if (!$flashcard) {
                session()->flash('error', 'Flashcard not found.');
                return;
            }

            $flashcard->delete();
            
            // Reload flashcards to update the view
            $this->loadFlashcards();
            
            session()->flash('success', 'Flashcard deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete flashcard.');
        }
    }

    /**
     * Get the page title.
     */
    #[Computed]
    public function title()
    {
        return $this->deck ? $this->deck->name : 'Deck Details';
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.deck-details')->layoutData(['title' => $this->title]);
    }
}
