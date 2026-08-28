@props([
    'course',
    'index' => 0,
])

@php
    $image = $course->image
        ? (str_starts_with($course->image, 'http') ? $course->image : asset('storage/' . $course->image))
        : asset('images/institution-' . (($index % 3) + 1) . '.jpg');

    $category = $course->category ?? 'Audiovisuel';
    $level = $course->level;
    $duration = $course->duration;
@endphp

<article
    class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-b from-[#141419] to-[#0b0b0e] transition-all duration-500 hover:-translate-y-2 hover:border-[#F5B800]/50 shadow-xl hover:shadow-[0_15px_40px_rgba(245,184,0,0.14)]"
>

    {{-- =====================================================
         1. IMAGE & BADGES
    ====================================================== --}}
    <div class="relative aspect-[16/10] w-full overflow-hidden bg-[#181A1C]">

        <img
            src="{{ $image }}"
            alt="{{ $course->title }}"
            class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-108 filter brightness-95"
            loading="lazy"
        >

        {{-- Voile cinématique sombre --}}
        <div class="absolute inset-0 bg-gradient-to-t from-[#0b0b0e] via-[#0b0b0e]/30 to-black/40"></div>

        {{-- Badge Catégorie (Haut Gauche) --}}
        @if($category)
            <div class="absolute left-4 top-4 z-10">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-black/75 backdrop-blur-md text-[#F5B800] border border-[#F5B800]/30 shadow-lg">
                    <x-lucide-layers class="w-3 h-3 text-[#F5B800]" />
                    {{ $category }}
                </span>
            </div>
        @endif

        {{-- Badge Durée (Haut Droite) --}}
        @if($duration)
            <div class="absolute right-4 top-4 z-10">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-medium text-white/80 bg-black/60 backdrop-blur-md border border-white/10">
                    <x-lucide-clock class="w-3 h-3 text-[#F5B800]" />
                    {{ $duration }}
                </span>
            </div>
        @endif

    </div>


    {{-- =====================================================
         2. CONTENU DE LA FORMATION
    ====================================================== --}}
    <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">

        <div>
            {{-- Titre --}}
            <h3 class="text-xl sm:text-2xl font-bold text-white group-hover:text-[#F5B800] transition-colors leading-snug">
                <a href="{{ route('public.courses.show', $course->slug) }}" class="focus:outline-none">
                    {{ $course->title }}
                </a>
            </h3>

            {{-- Description --}}
            @if($course->description)
                <p class="mt-3 text-xs sm:text-sm text-white/60 leading-relaxed font-light line-clamp-3">
                    {{ $course->description }}
                </p>
            @endif
        </div>


        {{-- =================================================
             3. INFOS CLÉS
        ================================================== --}}
        <div class="mt-6 pt-5 border-t border-white/[0.08]">

            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-white/70">
                @if($level)
                    <div class="flex items-center gap-1.5">
                        <x-lucide-graduation-cap class="w-3.5 h-3.5 text-[#F5B800]" />
                        <span>Niveau : <strong class="text-white">{{ $level }}</strong></span>
                    </div>
                @else
                    <div class="flex items-center gap-1.5 text-white/60">
                        <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-[#F5B800]" />
                        <span>Accessible sans prérequis</span>
                    </div>
                @endif

                <div class="flex items-center gap-1.5 text-[#F5B800]">
                    <x-lucide-check-circle-2 class="w-3.5 h-3.5" />
                    <span class="text-[11px] font-semibold uppercase tracking-wider">80% Pratique</span>
                </div>
            </div>

            {{-- Boutons d'Action --}}
            <div class="mt-5 flex items-center gap-2.5">
                <a
                    href="{{ route('public.courses.show', $course->slug) }}"
                    class="flex-1 inline-flex items-center justify-between px-4 py-2.5 rounded-full bg-white/[0.06] hover:bg-white/[0.12] border border-white/10 hover:border-white/30 text-white font-semibold text-xs transition-all duration-200 group/btn"
                >
                    <span>Voir le programme</span>
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-white/10 group-hover/btn:bg-[#F5B800] group-hover/btn:text-black transition-colors">
                        <x-lucide-arrow-up-right class="w-3 h-3" />
                    </span>
                </a>

                <a
                    href="{{ route('public.admissions.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-full bg-[#F5B800] hover:bg-white text-black font-bold text-xs uppercase tracking-wider transition-all duration-200 shadow-md hover:shadow-lg shrink-0"
                    title="Déposer ma candidature pour cette formation"
                >
                    Admission
                </a>
            </div>

        </div>

    </div>

</article>