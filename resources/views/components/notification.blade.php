@props([
    'type' => 'info',
    'message' => '',
    'timeout' => null,
])

@php
    $config = [
        'success' => [
            'icon' => 'icons.check-circle',
            'color' => 'text-green-400',
            'timeout' => $timeout ?? 5000,
            'title' => 'Success'
        ],
        'error' => [
            'icon' => 'icons.x-circle',
            'color' => 'text-red-400',
            'timeout' => $timeout ?? 7000,
            'title' => 'Error'
        ],
        'warning' => [
            'icon' => 'icons.exclamation-triangle',
            'color' => 'text-yellow-400',
            'timeout' => $timeout ?? 6000,
            'title' => 'Warning'
        ],
        'info' => [
            'icon' => 'icons.information-circle',
            'color' => 'text-blue-400',
            'timeout' => $timeout ?? 5000,
            'title' => 'Info'
        ]
    ];
    
    $currentConfig = $config[$type] ?? $config['info'];
@endphp

<div x-data="{ show: true }" 
     x-show="show" 
     x-init="setTimeout(() => show = false, {{ $currentConfig['timeout'] }})"
     x-transition:enter="transform ease-out duration-300 transition"
     x-transition:enter-start="translate-y-2 opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="w-full max-w-md sm:max-w-lg bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <x-dynamic-component :component="$currentConfig['icon']" class="h-6 w-6 {{ $currentConfig['color'] }}" />
            </div>
            <div class="ml-3 flex-1 pt-0.5">
                <p class="text-sm font-medium text-gray-900">{{ $currentConfig['title'] }}</p>
                <p class="mt-1 text-sm text-gray-500 break-words">{{ $message }}</p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button @click="show = false" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="sr-only">Close</span>
                    <x-icons.x-mark class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>
</div> 