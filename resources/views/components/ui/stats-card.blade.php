@props([
    'title',
    'value',
    'icon',
    'color' => 'primary',
])

@php

$colors = [
    'primary' => 'bg-orange-100 text-primary',
    'success' => 'bg-green-100 text-green-600',
    'danger'  => 'bg-red-100 text-red-600',
    'secondary' => 'bg-purple-100 text-secondary',
];

@endphp

<div class="group rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">

    <div class="flex items-start justify-between">

        <div>

            <p class="text-sm text-muted">
                {{ $title }}
            </p>

            <h2 class="mt-3 text-4xl font-bold">
                {{ $value }}
            </h2>

        </div>

        <div class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $colors[$color] }}">

            {{ $icon }}

        </div>

    </div>

</div>