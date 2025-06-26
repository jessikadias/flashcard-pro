<script setup>
import { SparklesIcon } from '@heroicons/vue/24/outline'
defineProps({ step: Object, isLastStep: Boolean })
</script>
<template>
    <div
        class="flex flex-col items-center justify-start text-center p-8 pt-16 overflow-y-auto flex-1 overflow-y-hidden">
        <!-- Logo -->
        <div class="mx-auto w-20 h-20 flex items-center justify-center">
            <img :src="step.icon" alt="Logo" class="w-18 h-18 max-w-full max-h-full">
        </div>
        <!-- Title -->
        <h3 class="text-xl font-bold text-gray-900 mb-3">
            {{ step.title }}
        </h3>
        <!-- Description -->
        <p class="text-gray-600 leading-relaxed mb-4">
            {{ step.description }}
        </p>
        <div class="flex items-center justify-center w-full mb-6" style="height: 200px;">
            <template v-if="step.image">
                <div
                    class="max-w-xs w-full mx-auto border border-gray-200 rounded-lg overflow-hidden shadow-inner flex items-center justify-center h-full">
                    <img :src="step.image" alt="Onboarding Step Image" class="w-full h-auto max-h-full object-contain">
                </div>
            </template>
            <template v-else-if="isLastStep">
                <SparklesIcon class="w-24 h-24 text-primary-800 opacity-80 mx-auto" />
            </template>
        </div>
    </div>
</template>
