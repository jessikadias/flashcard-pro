<?php

namespace App\Livewire;

use Livewire\Component;

class OnboardingTutorial extends Component
{

    public bool $showOnboarding = false;

    protected $listeners = ['onboarding-completed' => 'completeOnboarding'];

    public function mount()
    {
        /* Check if user is authenticated and hasn't completed onboarding */
        if (auth()->check()) {
            $this->showOnboarding = !auth()->user()->hasCompletedOnboarding();
        }        
    }

    /* Complete onboarding */
    public function completeOnboarding()
    {
        /* Complete onboarding */
        if (auth()->check()) {
            $user = auth()->user();
            $user->onboarding_completed = true;
            $user->save();
        }

        /* Close onboarding */
        $this->showOnboarding = false;

        /* Emit event to Vue component */
        $this->dispatch('onboarding-completed');
    }

    /* Render component */
    public function render()
    {
        return view('livewire.onboarding-tutorial');
    }
} 