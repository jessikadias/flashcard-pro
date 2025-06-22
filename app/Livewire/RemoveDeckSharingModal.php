<?php

namespace App\Livewire;

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
     * Listen for the openRemoveDeckSharingModal event.
     */
    #[On('openRemoveDeckSharingModal')]
    public function openModal($deckId)
    {
        $this->deckId = $deckId;
        $this->showModal = true;
    }

    /**
     * Close the modal.
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->deckId = null;
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
        return view('livewire.remove-deck-sharing-modal');
    }
}
