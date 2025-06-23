<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="$emit('close')" />
    <div
      class="relative bg-white rounded-2xl shadow-2xl w-[400px] h-[600px] mx-auto overflow-hidden z-10 flex flex-col">
      <slot />
    </div>
  </div>
</template>