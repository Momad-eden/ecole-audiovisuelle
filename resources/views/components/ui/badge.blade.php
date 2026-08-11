@props([
    'variant' => 'gray'
])

@php

$classes = match($variant) {

    'success' => 'bg-green-100 text-green-700',

    'danger' => 'bg-red-100 text-red-700',

    'warning' => 'bg-yellow-100 text-yellow-700',

    'primary' => 'bg-orange-100 text-primary',

    default => 'bg-gray-100 text-gray-700'

};

@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {$classes}"
]) }}>

    {{ $slot }}

</span>