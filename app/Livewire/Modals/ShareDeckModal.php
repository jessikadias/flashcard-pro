<?php

namespace App\Livewire\Modals;

use App\Models\Deck;
use App\Models\User;
use Livewire\Component;

class ShareDeckModal extends Component
{
    public bool $show = false;
    public ?Deck $deck = null;
    public string $shareEmail = '';

    protected $listeners = ['openShareDeckModal'];

    protected function rules(): array
    {
        return [
            'shareEmail' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if (!User::where('email', $value)->exists()) {
                        $fail('No user found with this email address.');
                    }
                },
                function ($attribute, $value, $fail) {
                    if ($this->deck && User::where('email', $value)->first()?->id === $this->deck->user_id) {
                        $fail('You cannot share the deck with its owner.');
                    }
                },
                 function ($attribute, $value, $fail) {
                    if ($this->deck && $this->deck->sharedWithUsers()->where('email', $value)->exists()) {
                        $fail('This deck is already shared with this user.');
                    }
                }
            ],
        ];
    }

    public function openShareDeckModal(int $deckId)
    {
        $this->deck = Deck::find($deckId);
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
        $this->deck = null;
        $this->shareEmail = '';
        $this->resetValidation();
    }

    public function shareDeck()
    {
        $this->validate();

        if (!$this->deck) {
            session()->flash('error', 'No deck selected to share.');
            return;
        }

        try {
            $targetUser = User::where('email', $this->shareEmail)->first();
            
            if (!$targetUser) {
                session()->flash('error', 'User not found.');
                return;
            }
            
            $this->deck->sharedWithUsers()->attach($targetUser->id, [
                'user_id' => auth()->id()
            ]);
            
            session()->flash('success', "Deck '{$this->deck->name}' has been shared with {$this->shareEmail}.");            
            $this->close();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to share deck. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.modals.share-deck-modal');
    }
}
