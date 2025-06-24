<?php

namespace App\Livewire\Modals;

use App\Models\Deck;
use Livewire\Component;
use Livewire\Attributes\On;

class RemoveDeckSharingModal extends Component
{
    /**
     * Whether the modal is shown.
     *
     * @var bool
     */
    public bool $showModal = false;

    /**
     * The deck ID.
     *
     * @var int|null
     */
    public ?int $deckId = null;

    /**
     * The deck instance.
     *
     * @var Deck|null
     */
    public ?Deck $deck = null;

    /**
     * Listen for the openRemoveDeckSharingModal event.
     */
    #[On('openRemoveDeckSharingModal')]
    public function openModal($deckId)
    {
        $this->deckId = $deckId;
        $this->deck = Deck::find($deckId);
        $this->showModal = true;
    }

    /**
     * Close the modal.
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->deckId = null;
        $this->deck = null;
    }

    /**
     * Confirm the removal of deck sharing.
     */
    public function confirmRemove()
    {
        if (!$this->deckId) {
            return;
        }

        // Dispatch single event for deck sharing removal
        $this->dispatch('deckSharingRemoved', $this->deckId);
        
        $this->closeModal();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.modals.remove-deck-sharing-modal');
    }
}
