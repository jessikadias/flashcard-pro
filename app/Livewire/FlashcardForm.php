<?php

namespace App\Livewire;

use App\Models\Deck;
use App\Models\Flashcard as FlashcardModel;
use Livewire\Component;
use Livewire\Attributes\On;

class FlashcardForm extends Component
{
    /**
     * The deck.
     *
     * @var Deck
     */
    public Deck $deck;
    
    /**
     * The flashcard.
     *
     * @var FlashcardModel|null
     */
    public ?FlashcardModel $flashcard = null;

    /**
     * The question.
     * @var string
     */
    public string $question = '';

    /**
     * The answer.
     * @var string
     */
    public string $answer = '';

    protected $listeners = ['flashcardDeleted' => 'delete'];

    /**
     * Mount the component.
     *
     * @param Deck $deck
     * @param int|null $flashcardId
     */
    public function mount(Deck $deck, $flashcardId = null)
    {
        $this->deck = $deck;

        if ($flashcardId) {
            $this->flashcard = FlashcardModel::findOrFail($flashcardId);
            $this->question = $this->flashcard->question;
            $this->answer = $this->flashcard->answer;
        }
    }

    /**
     * Save the flashcard.
     */
    public function save()
    {
        if (!auth()->user()->can('edit', $this->deck)) {
            session()->flash('error', 'You are not authorized to perform this action.');
            return;
        }

        $this->validate([
            'question' => 'required|min:3',
            'answer' => 'required|min:1',
        ]);

        if ($this->flashcard) {
            // Update existing flashcard
            $this->flashcard->update([
                'question' => $this->question,
                'answer' => $this->answer,
            ]);
        } else {
            // Create new flashcard
            $this->deck->flashcards()->create([
                'question' => $this->question,
                'answer' => $this->answer,
            ]);
        }

        session()->flash('success', 'Flashcard saved successfully!');
        return redirect()->route('decks.edit', $this->deck);
    }

    #[On('flashcardDeleted')]
    public function delete()
    {
        if (!$this->flashcard || !auth()->user()->can('edit', $this->deck)) {
            session()->flash('error', 'You are not authorized to perform this action.');
            return;
        }

        $this->flashcard->delete();

        session()->flash('success', 'Flashcard deleted successfully!');
        return redirect()->route('decks.edit', $this->deck);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.flashcard-form');
    }
}
