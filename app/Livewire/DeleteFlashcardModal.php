<?php

namespace App\Livewire;

use App\Models\Flashcard;
use Livewire\Component;
use Livewire\Attributes\On;

class DeleteFlashcardModal extends Component
{
    public bool $show = false;
    public ?int $flashcardId = null;

    #[On('openDeleteFlashcardModal')]
    public function open(int $flashcardId)
    {
        $this->flashcardId = $flashcardId;
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
        $this->flashcardId = null;
    }

    public function delete()
    {
        if ($this->flashcardId) {
            $this->dispatch('flashcardDeleted', $this->flashcardId);
            $this->close();
        }
    }

    public function render()
    {
        return view('livewire.delete-flashcard-modal');
    }
}
