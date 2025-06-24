<?php

namespace App\Livewire\Modals;

use App\Models\Deck;
use Livewire\Component;

class DeleteDeckModal extends Component
{
    public bool $show = false;
    public ?Deck $deck = null;

    protected $listeners = ['openDeleteDeckModal'];

    public function openDeleteDeckModal(int $deckId)
    {
        $this->deck = Deck::find($deckId);
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
        $this->deck = null;
    }

    public function deleteDeck()
    {
        if (!$this->deck) {
            session()->flash('error', 'No deck selected to delete.');
            return;
        }
        
        if ($this->deck->user_id !== auth()->id()) {
            session()->flash('error', 'You can only delete your own decks.');
            return;
        }

        try {
            $deckName = $this->deck->name;
            $this->deck->delete();

            session()->flash('success', "Deck '{$deckName}' has been deleted successfully.");
            
            // Close modal first
            $this->close();
            
            // Redirect to deck list
            return redirect()->route('decks.index');

        } catch (\Exception $e) {
            \Log::error('Failed to delete deck', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to delete deck. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.modals.delete-deck-modal');
    }
}
