<?php

namespace App\Livewire\Modals;

use Livewire\Component;

class CreateDeckModal extends Component
{
    /**
     * Show/hide the modal
     */
    public bool $show = false;

    /**
     * New deck name
     */
    public string $deckName = '';

    /**
     * Deck visibility (public/private)
     */
    public bool $isPublic = false;

    /**
     * Validation rules
     */
    protected $rules = [
        'deckName' => 'required|min:3|max:50',
    ];

    /**
     * Validation messages
     */
    protected $messages = [
        'deckName.required' => 'Please enter a deck name.',
        'deckName.min' => 'Deck name must be at least 3 characters.',
        'deckName.max' => 'Deck name cannot exceed 50 characters.',
    ];

    /**
     * Listen for events from parent component
     */
    protected $listeners = ['openCreateDeckModal' => 'open'];

    /**
     * Open the modal
     */
    public function open()
    {
        $this->deckName = '';
        $this->isPublic = false;
        $this->show = true;
    }

    /**
     * Close the modal
     */
    public function close()
    {
        $this->show = false;
        $this->deckName = '';
        $this->isPublic = false;
        $this->resetValidation();
    }

    /**
     * Create a new deck
     */
    public function createDeck()
    {
        $this->validate();

        try {
            $deck = auth()->user()->decks()->create([
                'name' => $this->deckName,
                'is_public' => $this->isPublic,
            ]);
            
            // Emit event to parent component
            $this->dispatch('deckCreated', deckId: $deck->id);
            
            // Close modal
            $this->close();
            
            // Redirect to deck edit page
            return redirect()->route('decks.edit', ['deck' => $deck]);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create deck. Please try again.');
        }
    }

    /**
     * Handle Enter key press
     */
    public function updatedDeckName()
    {
        $this->resetValidation('deckName');
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.modals.create-deck-modal');
    }
}
