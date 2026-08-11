@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
])

@php

$classes = match($variant){

    'primary' => 'bg-primary hover:bg-orange-700 text-white',

    'secondary' => 'bg-secondary hover:bg-indigo-950 text-white',

    'danger' => 'bg-red-600 hover:bg-red-700 text-white',

    'success' => 'bg-green-600 hover:bg-green-700 text-white',

    'outline' => 'border border-gray-300 hover:bg-gray-100 text-gray-800',

    default => 'bg-primary text-white'

};

$base = "inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-medium transition duration-200 {$classes}";

@endphp

@if($href)

<a href="{{ $href }}" {{ $attributes->merge(['class' => $base]) }}>
    {{ $slot }}
</a>

@else

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $base]) }}>
    {{ $slot }}
</button>

@endif