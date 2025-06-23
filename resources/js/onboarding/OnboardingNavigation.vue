<script setup>
defineProps({ currentStep: Number, steps: Array })
</script>
<template>
  <div class="flex-shrink-0 w-full px-8 pb-8 pt-4">
    <div class="flex items-center justify-between">
      <!-- Back Button -->
      <button v-if="currentStep > 0" @click="$emit('back')"
        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors font-medium">
        ← Back
      </button>
      <div v-else class="w-16"></div>
      <!-- Step Indicator -->
      <div class="flex space-x-2">
        <div v-for="(step, index) in steps" :key="index" class="w-2 h-2 rounded-full transition-colors"
          :class="index <= currentStep ? 'bg-primary-800' : 'bg-gray-300'"></div>
      </div>
      <!-- Next/Finish Button -->
      <button @click="$emit('next')" type="button"
        class="px-6 py-2 bg-primary-800 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
        {{ currentStep === steps.length - 1 ? 'Get Started' : 'Next' }}
      </button>
    </div>
  </div>
</template>
