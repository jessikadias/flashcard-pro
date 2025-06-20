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
    'primary' => 'bg-[#315A92] text-white hover:bg-[#223464]',
    'secondary' => 'bg-[#EBF0F6] text-[#315A92] hover:bg-[#DCE4ED]',
    'header' => 'bg-[#315A92] text-white hover:bg-[#f3f6fa] hover:text-gray-900',
    default => 'bg-[#315A92] text-white hover:bg-[#223464]',
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