@props(['align' => 'right'])

@php
$alignmentClasses = match ($align) {
    'left' => 'left-0',
    'right' => 'right-0',
    default => 'right-0',
};
@endphp

<div x-data="{ open: false }" @click.outside="open = false" class="relative">
    <!-- Trigger -->
    <button @click="open = !open" class="p-2 rounded-full hover:bg-gray-200 transition">
        <x-icons.kebab-vertical class="text-gray-700" />
    </button>

    <!-- Content -->
    <div x-show="open"
         style="display: none;"
         x-transition
         class="absolute {{ $alignmentClasses }} mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1 z-50">
        {{ $slot }}
    </div>
</div> 