@extends('layouts.public')

@section('title', $course->title . ' — EMSI')

@section('description')
    {{ Str::limit(
        $course->description ?? 'Découvrez cette formation proposée par EMSI.',
        160
    ) }}
@endsection

@section('content')

{{-- =========================================================
     HERO
========================================================= --}}

<section class="bg-[#F4F1EA]">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div
            class="
                grid
                lg:grid-cols-[0.9fr_1.1fr]
                gap-10
                lg:gap-16

                items-center

                pt-12
                pb-14
                lg:pt-32
                lg:pb-16
            "
        >

            {{-- =================================================
                 TEXTE
            ================================================== --}}

            <div>

                {{-- Retour --}}

                <a
                    href="{{ url('/') }}#formations"
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


                {{-- Label --}}

                <div
                    class="
                        mt-7

                        inline-flex
                        items-center
                        gap-2

                        rounded-full

                        border
                        border-[#B78900]/20

                        bg-[#B78900]/5

                        px-4
                        py-2

                        text-[10px]
                        font-semibold
                        uppercase
                        tracking-[0.18em]

                        text-[#8A6800]
                    "
                >

                    <span
                        class="
                            w-1.5
                            h-1.5
                            rounded-full
                            bg-[#B78900]
                        "
                    ></span>

                    Formation EMSI

                </div>


                {{-- Titre --}}

                <h1
                    class="
                        mt-5

                        max-w-2xl

                        text-5xl
                        md:text-6xl
                        lg:text-[4.5rem]

                        leading-[0.94]

                        tracking-[-0.045em]

                        font-semibold

                        text-[#111]
                    "
                >
                    {{ $course->title }}
                </h1>


                {{-- Description --}}

                @if($course->description)

                    <p
                        class="
                            mt-6

                            max-w-xl

                            text-base
                            md:text-[17px]

                            leading-7

                            text-black/55
                        "
                    >
                        {{ $course->description }}
                    </p>

                @endif


                {{-- Informations --}}

                <div
                    class="
                        mt-7

                        flex
                        flex-wrap
                        gap-3
                    "
                >

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

                                <x-lucide-clock-3 class="w-4 h-4" />

                            </div>

                            <div>

                                <p
                                    class="
                                        text-[9px]
                                        uppercase
                                        tracking-[0.16em]
                                        text-black/35
                                    "
                                >
                                    Durée
                                </p>

                                <p
                                    class="
                                        mt-0.5
                                        text-sm
                                        font-semibold
                                        text-black
                                    "
                                >
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

                                <p
                                    class="
                                        text-[9px]
                                        uppercase
                                        tracking-[0.16em]
                                        text-black/35
                                    "
                                >
                                    Tarif
                                </p>

                                <p
                                    class="
                                        mt-0.5
                                        text-sm
                                        font-semibold
                                        text-black
                                    "
                                >
                                    {{ number_format($course->price, 0, ',', ' ') }}
                                    FCFA
                                </p>

                            </div>

                        </div>

                    @endif

                </div>


                {{-- CTA --}}

                <div class="mt-7">

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

                        Candidater à cette formation

                        <x-lucide-arrow-up-right class="w-4 h-4" />

                    </a>

                </div>

            </div>


            {{-- =================================================
                 IMAGE
            ================================================== --}}

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


                    {{-- Badge image --}}

                    <div
                        class="
                            absolute
                            bottom-5
                            left-5

                            rounded-full

                            bg-white/95

                            px-4
                            py-2

                            text-[9px]
                            font-semibold
                            uppercase
                            tracking-[0.15em]

                            text-black
                        "
                    >
                        EMSI · Formation professionnelle
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     PRÉSENTATION
========================================================= --}}

<section class="bg-white py-16 lg:py-20">

    <div class="max-w-5xl mx-auto px-6 lg:px-10">

        <div
            class="
                grid
                md:grid-cols-[140px_1fr]

                gap-8
                lg:gap-12
            "
        >

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
                    La formation
                </p>

            </div>


            <div>

                <h2
                    class="
                        max-w-3xl

                        text-3xl
                        md:text-4xl

                        leading-tight

                        tracking-[-0.035em]

                        font-semibold

                        text-black
                    "
                >
                    Développez vos compétences
                    dans l'univers audiovisuel.
                </h2>


                @if($course->description)

                    <p
                        class="
                            mt-6

                            max-w-3xl

                            text-base
                            md:text-lg

                            leading-8

                            text-black/50
                        "
                    >
                        {{ $course->description }}
                    </p>

                @endif

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     INFORMATIONS CLÉS
========================================================= --}}

<section class="bg-[#F4F1EA] py-12 lg:py-14">

    <div class="max-w-5xl mx-auto px-6 lg:px-10">

        <div
            class="
                grid
                md:grid-cols-3

                overflow-hidden

                rounded-2xl

                border
                border-black/10

                divide-y
                md:divide-y-0
                md:divide-x
                divide-black/10
            "
        >

            @if($course->duration)

                <div class="p-6 lg:p-7">

                    <p
                        class="
                            text-[9px]
                            uppercase
                            tracking-[0.18em]
                            text-black/30
                        "
                    >
                        Durée
                    </p>

                    <p
                        class="
                            mt-2
                            text-lg
                            font-semibold
                            text-black
                        "
                    >
                        {{ $course->duration }}
                    </p>

                </div>

            @endif


            <div class="p-6 lg:p-7">

                <p
                    class="
                        text-[9px]
                        uppercase
                        tracking-[0.18em]
                        text-black/30
                    "
                >
                    Investissement
                </p>

                <p
                    class="
                        mt-2
                        text-lg
                        font-semibold
                        text-black
                    "
                >

                    @if(!is_null($course->price))

                        {{ number_format($course->price, 0, ',', ' ') }}
                        FCFA

                    @else

                        Sur demande

                    @endif

                </p>

            </div>


            <div class="p-6 lg:p-7">

                <p
                    class="
                        text-[9px]
                        uppercase
                        tracking-[0.18em]
                        text-black/30
                    "
                >
                    Statut
                </p>

                <p
                    class="
                        mt-2

                        flex
                        items-center
                        gap-2

                        text-lg
                        font-semibold
                        text-black
                    "
                >

                    <span
                        class="
                            h-2
                            w-2
                            rounded-full
                            bg-green-500
                        "
                    ></span>

                    Inscriptions ouvertes

                </p>

            </div>

        </div>

    </div>

</section>


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
            Prêt à rejoindre l'aventure ?
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
            Déposez votre candidature et notre équipe
            étudiera votre dossier.
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