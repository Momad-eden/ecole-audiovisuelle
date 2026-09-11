@extends('layouts.public')

@section('title', $course->title . ' — EMSI')

@section('description')
    {{ Str::limit(
        $course->description ?? 'Découvrez la formation ' . $course->title . ' proposée par l’EMSI à Dakar.',
        160
    ) }}
@endsection

@section('content')

{{-- =========================================================
     HERO : TITRE, INFORMATIONS CLÉS & VISUEL
========================================================= --}}
<section class="bg-[#F4F1EA]">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div
            class="
                grid
                lg:grid-cols-[0.95fr_1.05fr]
                gap-10
                lg:gap-16
                items-center
                pt-12
                pb-14
                lg:pt-28
                lg:pb-16
            "
        >

            {{-- Colonne Texte & Informations --}}
            <div>

                {{-- Retour aux formations --}}
                <a
                    href="{{ route('public.courses.index') }}"
                    class="
                        inline-flex
                        items-center
                        gap-2
                        text-xs
                        font-medium
                        uppercase
                        tracking-[0.16em]
                        text-black/45
                        hover:text-black
                        transition
                    "
                >
                    <x-lucide-arrow-left class="w-4 h-4" />
                    Toutes les formations
                </a>

                {{-- Badges Catégorie / Niveau --}}
                <div class="mt-6 flex flex-wrap items-center gap-2">

                    <span
                        class="
                            inline-flex
                            items-center
                            gap-2
                            rounded-full
                            border
                            border-[#B78900]/20
                            bg-[#B78900]/5
                            px-3.5
                            py-1.5
                            text-[10px]
                            font-semibold
                            uppercase
                            tracking-[0.18em]
                            text-[#8A6800]
                        "
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-[#B78900]"></span>
                        Formation EMSI
                    </span>

                    @if($course->category)
                        <span
                            class="
                                inline-flex
                                items-center
                                gap-1.5
                                rounded-full
                                border
                                border-black/10
                                bg-white
                                px-3.5
                                py-1.5
                                text-[10px]
                                font-semibold
                                uppercase
                                tracking-[0.15em]
                                text-black/70
                            "
                        >
                            {{ $course->category }}
                        </span>
                    @endif

                    @if($course->level)
                        <span
                            class="
                                inline-flex
                                items-center
                                gap-1.5
                                rounded-full
                                border
                                border-black/10
                                bg-white
                                px-3.5
                                py-1.5
                                text-[10px]
                                font-semibold
                                uppercase
                                tracking-[0.15em]
                                text-black/70
                            "
                        >
                            {{ $course->level }}
                        </span>
                    @endif

                </div>

                {{-- Titre --}}
                <h1
                    class="
                        mt-5
                        max-w-2xl
                        text-4xl
                        sm:text-5xl
                        lg:text-[4rem]
                        leading-[0.98]
                        tracking-[-0.04em]
                        font-semibold
                        text-[#111]
                    "
                >
                    {{ $course->title }}
                </h1>

                {{-- Informations clés dynamiques --}}
                <div class="mt-7 flex flex-wrap gap-3">

                    @if($course->duration)
                        <div
                            class="
                                flex
                                items-center
                                gap-3
                                rounded-xl
                                border
                                border-black/10
                                bg-white
                                px-4
                                py-3
                            "
                        >
                            <div
                                class="
                                    flex
                                    h-8
                                    w-8
                                    items-center
                                    justify-center
                                    rounded-lg
                                    bg-[#F5B800]/10
                                    text-[#A77800]
                                "
                            >
                                <x-lucide-clock class="w-4 h-4" />
                            </div>

                            <div>
                                <p class="text-[9px] uppercase tracking-[0.16em] text-black/35">
                                    Durée
                                </p>
                                <p class="mt-0.5 text-sm font-semibold text-black">
                                    {{ $course->duration }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if(!is_null($course->price))
                        <div
                            class="
                                flex
                                items-center
                                gap-3
                                rounded-xl
                                border
                                border-black/10
                                bg-white
                                px-4
                                py-3
                            "
                        >
                            <div
                                class="
                                    flex
                                    h-8
                                    w-8
                                    items-center
                                    justify-center
                                    rounded-lg
                                    bg-[#F5B800]/10
                                    text-[#A77800]
                                "
                            >
                                <x-lucide-banknote class="w-4 h-4" />
                            </div>

                            <div>
                                <p class="text-[9px] uppercase tracking-[0.16em] text-black/35">
                                    Tarif
                                </p>
                                <p class="mt-0.5 text-sm font-semibold text-black">
                                    @if($course->price > 0)
                                        {{ number_format($course->price, 0, ',', ' ') }} FCFA
                                    @else
                                        Sur demande
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($course->students_count)
                        <div
                            class="
                                flex
                                items-center
                                gap-3
                                rounded-xl
                                border
                                border-black/10
                                bg-white
                                px-4
                                py-3
                            "
                        >
                            <div
                                class="
                                    flex
                                    h-8
                                    w-8
                                    items-center
                                    justify-center
                                    rounded-lg
                                    bg-[#F5B800]/10
                                    text-[#A77800]
                                "
                            >
                                <x-lucide-users class="w-4 h-4" />
                            </div>

                            <div>
                                <p class="text-[9px] uppercase tracking-[0.16em] text-black/35">
                                    Effectif
                                </p>
                                <p class="mt-0.5 text-sm font-semibold text-black">
                                    {{ $course->students_count }} places max
                                </p>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- CTA Admission --}}
                <div class="mt-8 flex flex-wrap items-center gap-4">

                    <a
                        href="{{ route('public.admissions.create') }}?course={{ $course->id }}"
                        class="
                            inline-flex
                            items-center
                            gap-3
                            rounded-full
                            bg-[#111]
                            px-6
                            py-3.5
                            text-sm
                            font-semibold
                            text-white
                            hover:bg-[#B78900]
                            transition
                            duration-300
                        "
                    >
                        Admission à cette formation
                        <x-lucide-arrow-up-right class="w-4 h-4" />
                    </a>

                </div>

            </div>

            {{-- Colonne Image --}}
            <div>

                <div
                    class="
                        relative
                        aspect-[4/3]
                        w-full
                        overflow-hidden
                        rounded-[1.75rem]
                        bg-[#E4DED2]
                        shadow-sm
                    "
                >

                    @if($course->image)
                        <img
                            src="{{ asset('storage/' . ltrim($course->image, '/')) }}"
                            alt="{{ $course->title }}"
                            class="
                                absolute
                                inset-0
                                h-full
                                w-full
                                object-cover
                            "
                        >
                        <div
                            class="
                                absolute
                                inset-0
                                bg-gradient-to-t
                                from-black/30
                                via-transparent
                                to-transparent
                            "
                        ></div>
                    @else
                        <div
                            class="
                                absolute
                                inset-0
                                flex
                                items-center
                                justify-center
                            "
                        >
                            <x-lucide-clapperboard
                                class="
                                    h-20
                                    w-20
                                    text-black/10
                                "
                            />
                        </div>
                    @endif

                    {{-- Badge sur image --}}
                    <div
                        class="
                            absolute
                            bottom-5
                            left-5
                            rounded-full
                            bg-white/95
                            backdrop-blur-sm
                            px-4
                            py-2
                            text-[9px]
                            font-semibold
                            uppercase
                            tracking-[0.15em]
                            text-black
                        "
                    >
                        EMSI · Grand Théâtre National
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     PRÉSENTATION UNIQUE DE LA FORMATION
========================================================= --}}
@if($course->description)
<section class="bg-white py-16 lg:py-20 border-b border-black/5">

    <div class="max-w-5xl mx-auto px-6 lg:px-10">

        <div
            class="
                grid
                md:grid-cols-[160px_1fr]
                gap-8
                lg:gap-12
            "
        >

            {{-- Label latéral --}}
            <div>
                <p
                    class="
                        text-[10px]
                        font-semibold
                        uppercase
                        tracking-[0.2em]
                        text-black/25
                    "
                >
                    01
                </p>

                <p
                    class="
                        mt-2
                        text-sm
                        font-semibold
                        text-black
                    "
                >
                    Présentation
                </p>
            </div>

            {{-- Texte de présentation (affiché une seule fois ici) --}}
            <div>
                <h2
                    class="
                        max-w-3xl
                        text-2xl
                        sm:text-3xl
                        md:text-4xl
                        leading-tight
                        tracking-[-0.035em]
                        font-semibold
                        text-black
                    "
                >
                    À propos de la formation
                </h2>

                <div
                    class="
                        mt-6
                        max-w-3xl
                        text-base
                        md:text-lg
                        leading-8
                        text-black/70
                        whitespace-pre-line
                    "
                >
                    {{ $course->description }}
                </div>
            </div>

        </div>

    </div>

</section>
@endif


{{-- =========================================================
     FORMATIONS CONNEXES
========================================================= --}}
@if(isset($relatedCourses) && $relatedCourses->isNotEmpty())
<section class="bg-[#F8F7F4] py-16 lg:py-20 border-b border-black/5">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-[#8A6800] mb-2">
                    Catalogue
                </p>
                <h2 class="text-2xl sm:text-3xl font-semibold text-black">
                    Formations similaires
                </h2>
            </div>

            <a
                href="{{ route('public.courses.index') }}"
                class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-black/60 hover:text-black transition"
            >
                <span>Toutes les formations</span>
                <x-lucide-arrow-right class="w-4 h-4" />
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($relatedCourses as $relCourse)
                <x-public.formation-item :course="$relCourse" :index="$loop->index" />
            @endforeach
        </div>

    </div>

</section>
@endif


{{-- =========================================================
     CTA FINAL
========================================================= --}}
<section class="bg-[#111111]">

    <div
        class="
            max-w-4xl
            mx-auto
            px-6
            py-16
            lg:py-20
            text-center
        "
    >

        <p
            class="
                text-[9px]
                uppercase
                tracking-[0.2em]
                text-[#F5B800]
            "
        >
            Votre parcours commence ici
        </p>

        <h2
            class="
                mt-4
                text-3xl
                md:text-4xl
                tracking-[-0.035em]
                font-semibold
                text-white
            "
        >
            Prêt à rejoindre {{ $course->title }} ?
        </h2>

        <p
            class="
                mt-4
                text-sm
                md:text-base
                leading-7
                text-white/45
            "
        >
            Déposez votre candidature en ligne. Notre équipe étudiera votre dossier dans les meilleurs délais.
        </p>

        <div class="mt-7">

            <a
                href="{{ route('public.admissions.create') }}?course={{ $course->id }}"
                class="
                    inline-flex
                    items-center
                    gap-3
                    rounded-full
                    bg-[#F5B800]
                    text-black
                    px-6
                    py-3.5
                    text-sm
                    font-semibold
                    hover:bg-white
                    transition
                "
            >
                Commencer ma candidature
                <x-lucide-arrow-up-right class="w-4 h-4" />
            </a>

        </div>

    </div>

</section>

@endsection