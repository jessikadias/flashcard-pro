<?php

namespace App\Livewire\Modals;

use App\Services\AIFlashcardGeneratorService;
use Illuminate\Support\Facades\Log;
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
     * AI generation options
     */
    public bool $useAI = false;
    public string $aiTheme = '';
    public string $aiDifficulty = 'beginner';
    public array $aiDifficultyOptions = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate', 
        'advanced' => 'Advanced'
    ];

    /**
     * Loading state for deck creation
     */
    public bool $isCreating = false;

    /**
     * Listen for events from parent component
     */
    protected $listeners = ['openCreateDeckModal'];

    protected function rules(): array
    {
        $rules = [
            'deckName' => 'required|string|max:50',
        ];
        
        if ($this->useAI) {
            $rules['aiTheme'] = 'required|string|max:255';
        }
        
        return $rules;
    }

    protected function messages(): array
    {
        return [
            'deckName.required' => 'Deck name is required.',
            'deckName.max' => 'Deck name cannot exceed 50 characters.',
            'aiTheme.required' => 'Please enter a theme when using AI generation.',
            'aiTheme.max' => 'Theme cannot exceed 255 characters.',
        ];
    }

    /**
     * Check if AI is available (API keys configured)
     */
    public function isAIAvailable(): bool
    {
        return AIFlashcardGeneratorService::isAvailable();
    }

    /**
     * Open the modal
     */
    public function openCreateDeckModal()
    {
        $this->show = true;
        $this->resetForm();
    }

    /**
     * Close the modal
     */
    public function close()
    {
        $this->show = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->deckName = '';
        $this->isPublic = false;
        $this->useAI = false;
        $this->aiTheme = '';
        $this->aiDifficulty = 'beginner';
        $this->isCreating = false;
        $this->resetValidation();
    }

    /**
     * Create a new deck
     */
    public function createDeck()
    {
        $this->validate();

        $this->isCreating = true;

        try {
            $deck = auth()->user()->decks()->create([
                'name' => $this->deckName,
                'is_public' => $this->isPublic,
            ]);

            if ($this->useAI && $this->aiTheme) {
                $generator = new AIFlashcardGeneratorService();
                $aiSuccess = $generator->generateFlashcards($deck, $this->aiTheme, $this->aiDifficulty);
                
                if ($aiSuccess) {
                    session()->flash('success', "Deck '{$this->deckName}' created successfully with AI-generated flashcards!");
                } else {
                    session()->flash('success', "Deck '{$this->deckName}' created successfully! AI generation failed, but you can add flashcards manually.");
                }
            } else {
                session()->flash('success', "Deck '{$this->deckName}' created successfully!");
            }
            
            $this->close();
            
            $this->dispatch('deckCreated', ['deckId' => $deck->id]);
            
            return redirect()->route('decks.edit', $deck);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create deck. Please try again.');
            Log::error('Deck creation failed: ' . $e->getMessage());
        } finally {
            $this->isCreating = false;
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
     * Handle AI theme changes
     */
    public function updatedAiTheme()
    {
        $this->resetValidation('aiTheme');
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.modals.create-deck-modal');
    }
}
