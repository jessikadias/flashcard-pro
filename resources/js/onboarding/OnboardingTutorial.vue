<script setup>
import { ref, onMounted, watch } from 'vue'
import steps from './data.js'
import OnboardingModal from './OnboardingModal.vue'
import OnboardingStepContent from './OnboardingStepContent.vue'
import OnboardingNavigation from './OnboardingNavigation.vue'

// Emits
const emit = defineEmits(['close'])

// Reactive data
const isVisible = ref(true)
const currentStep = ref(0)

// Methods
const nextStep = () => {
  if (currentStep.value < steps.length - 1) {
    currentStep.value++
  } else {
    closeTutorial()
  }
}

const previousStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--
  }
}

const closeTutorial = () => {
  isVisible.value = false
  
  if (typeof window !== 'undefined' && window.Livewire) {
    window.Livewire.dispatch('onboarding-completed')
  }
  emit('close')
}

// Listen for Livewire events
if (typeof window !== 'undefined' && window.Livewire) {
  window.Livewire.on('onboarding-completed', () => {
    isVisible.value = false
  })
}
</script>
<template>
  <OnboardingModal v-if="isVisible" @close="closeTutorial">
    <!-- Skip Button -->
    <div class="absolute top-4 right-4 z-10">
      <button @click="closeTutorial" class="text-gray-400 hover:text-gray-600 transition-colors text-sm">
        Skip
      </button>
    </div>
    <!-- Content Area -->
    <OnboardingStepContent :step="steps[currentStep]" :isLastStep="currentStep === steps.length - 1" />
    <!-- Navigation -->
    <OnboardingNavigation :currentStep="currentStep" :steps="steps" @next="nextStep" @back="previousStep" />
  </OnboardingModal>
</template>