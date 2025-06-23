@props([
    'variant' => 'primary', // 'primary', 'secondary', 'header'
    'type' => 'button',
    'href' => null,
    'size' => 'lg', // 'lg', 'sm'
    'fontWeight' => 'font-normal', // e.g., 'font-normal', 'font-semibold', 'font-bold'
])

@php
$baseClasses = 'rounded-md transition shadow flex items-center justify-center whitespace-nowrap';

$sizeClasses = match ($size) {
    'lg' => 'px-6 py-2 text-xl',
    'md' => 'px-4 py-2 text-md',
    'sm' => 'px-4 py-1 text-md',
    default => 'px-6 py-2 text-xl',
};

$variantClasses = match ($variant) {
    'primary' => 'bg-primary-500 text-white hover:bg-primary-800',
    'secondary' => 'bg-primary-300 text-primary-500 hover:bg-primary-200',
    'header' => 'bg-primary-500 text-white hover:bg-primary-50 hover:text-gray-900',
    default => 'bg-primary-500 text-white hover:bg-primary-800',
};

$classes = $baseClasses . ' ' . $sizeClasses . ' ' . $variantClasses . ' ' . $fontWeight;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif 