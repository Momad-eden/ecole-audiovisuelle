@props([
    'gallery',
    'index' => 0,
])

@php
    $isVideo = $gallery->type === 'video';
    $thumbnailUrl = $gallery->image_url;
@endphp

<article
    x-show="activeFilter === 'all' || activeFilter === '{{ $gallery->type }}'"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    @click="openModal({{ $index }})"
    class="group relative cursor-pointer overflow-hidden rounded-2xl bg-[#141414] border border-white/10 hover:border-[#F5B800]/50 transition-all duration-500 shadow-[0_10px_30px_rgba(0,0,0,0.5)] hover:shadow-[0_20px_40px_rgba(245,184,0,0.12)] hover:-translate-y-1"
>

    {{-- =====================================================
         CONTAINER MÉDIA / THUMBNAIL
    ====================================================== --}}
    <div class="relative aspect-[16/10] overflow-hidden bg-[#0d0d0d]">

        @if($thumbnailUrl)
            <img
                src="{{ $thumbnailUrl }}"
                alt="{{ $gallery->title }}"
                loading="lazy"
                class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
            >
        @else
            <div class="w-full h-full flex items-center justify-center bg-[#151515]">
                <x-lucide-image class="w-12 h-12 text-white/15" />
            </div>
        @endif

        {{-- Voile assombrissant au repos + dégradé accentué au survol --}}
        <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-[#0a0a0a]/30 to-transparent opacity-75 group-hover:opacity-90 transition-opacity duration-300"></div>

        {{-- =====================================================
             BADGE TYPE (Photo / Vidéo)
        ====================================================== --}}
        <div class="absolute top-4 left-4 z-10">
            @if($isVideo)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black/60 backdrop-blur-md text-[#F5B800] border border-[#F5B800]/30 shadow-lg">
                    <x-lucide-video class="w-3 h-3 text-[#F5B800]" />
                    Vidéo
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black/60 backdrop-blur-md text-white/90 border border-white/20 shadow-lg">
                    <x-lucide-camera class="w-3 h-3 text-white/70" />
                    Photo
                </span>
            @endif
        </div>

        {{-- =====================================================
             BOUTON D'ACTION CENTRAL (Play ou Zoom)
        ====================================================== --}}
        <div class="absolute inset-0 flex items-center justify-center z-10">
            @if($isVideo)
                <div class="relative flex items-center justify-center">
                    {{-- Pulse animation ring --}}
                    <span class="absolute w-16 h-16 rounded-full bg-[#F5B800]/20 animate-ping group-hover:bg-[#F5B800]/30"></span>
                    <div class="w-14 h-14 rounded-full bg-[#F5B800] text-black flex items-center justify-center shadow-[0_0_30px_rgba(245,184,0,0.5)] transform group-hover:scale-110 transition-transform duration-300 pl-0.5">
                        <x-lucide-play class="w-6 h-6 fill-current" />
                    </div>
                </div>
            @else
                <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transform translate-y-3 group-hover:translate-y-0 transition-all duration-300 shadow-xl">
                    <x-lucide-maximize-2 class="w-5 h-5" />
                </div>
            @endif
        </div>

    </div>

    {{-- =====================================================
         INFORMATIONS DE LA CARTE
    ====================================================== --}}
    <div class="p-5 bg-gradient-to-b from-[#141414] to-[#0e0e0e]">
        <div class="flex items-center justify-between text-[11px] text-white/40 mb-1">
            <span class="uppercase tracking-widest font-semibold text-[#F5B800]/80">EMSI Studio</span>
            <span class="inline-flex items-center gap-1 group-hover:text-white transition-colors duration-200">
                @if($isVideo)
                    <span>Regarder</span>
                    <x-lucide-arrow-up-right class="w-3 h-3 text-[#F5B800]" />
                @else
                    <span>Agrandir</span>
                    <x-lucide-arrow-up-right class="w-3 h-3" />
                @endif
            </span>
        </div>

        <h3 class="text-base md:text-lg font-bold text-white group-hover:text-[#F5B800] transition-colors duration-300 line-clamp-1">
            {{ $gallery->title }}
        </h3>

        @if($gallery->description)
            <p class="mt-1.5 text-xs leading-relaxed text-white/55 line-clamp-2">
                {{ $gallery->description }}
            </p>
        @endif
    </div>

</article>