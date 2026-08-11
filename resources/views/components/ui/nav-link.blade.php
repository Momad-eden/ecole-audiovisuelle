@props([
'href',
'active' => false,
])

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' =>
            'flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 font-medium ' .
            ($active
                ? 'bg-primary text-white shadow-lg'
                : 'text-white/80 hover:bg-white/10 hover:text-white')
   ]) }}>

    {{ $slot }}

</a>