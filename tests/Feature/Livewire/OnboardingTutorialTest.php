<?php

use App\Livewire\OnboardingTutorial;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create([
        'onboarding_completed' => false,
    ]);
    $this->actingAs($this->user);
});

it('shows onboarding for users who have not completed it', function () {
    Livewire::test(OnboardingTutorial::class)
        ->assertSet('showOnboarding', true);
});

it('does not show onboarding for users who have completed it', function () {
    $this->user->update(['onboarding_completed' => true]);
    
    Livewire::test(OnboardingTutorial::class)
        ->assertSet('showOnboarding', false);
});

it('does not show onboarding for guests', function () {    // Override the beforeEach authentication for this specific test
    \Illuminate\Support\Facades\Auth::logout();
    
    Livewire::test(OnboardingTutorial::class)
        ->assertSet('showOnboarding', false);
});

it('completes onboarding when completeOnboarding is called', function () {
    Livewire::test(OnboardingTutorial::class)
        ->call('completeOnboarding')
        ->assertSet('showOnboarding', false)
        ->assertDispatched('onboarding-completed');
    
    expect($this->user->fresh()->onboarding_completed)->toBeTrue();
});

it('handles onboarding completion event', function () {
    Livewire::test(OnboardingTutorial::class)
        ->dispatch('onboarding-completed')
        ->assertSet('showOnboarding', false);
    
    expect($this->user->fresh()->onboarding_completed)->toBeTrue();
});

it('renders the component view', function () {
    Livewire::test(OnboardingTutorial::class)
        ->assertViewIs('livewire.onboarding-tutorial');
}); 