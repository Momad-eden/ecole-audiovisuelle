@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge([
    'class' => 'bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden'
]) }}>

    @if($title)

        <div class="px-6 py-5 border-b border-gray-100">

            <h2 class="text-lg font-semibold text-gray-900">
                {{ $title }}
            </h2>

            @if($subtitle)

                <p class="text-sm text-muted mt-1">
                    {{ $subtitle }}
                </p>

            @endif

        </div>

    @endif

    <div class="p-6">

        {{ $slot }}

    </div>

</div>