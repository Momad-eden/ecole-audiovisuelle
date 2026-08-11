@props([
    'course',
    'index' => 0,
])

@php

    /*
    |--------------------------------------------------------------------------
    | Image
    |--------------------------------------------------------------------------
    */

    $image = $course->image
        ? asset('storage/' . $course->image)
        : null;


    /*
    |--------------------------------------------------------------------------
    | Numéro
    |--------------------------------------------------------------------------
    */

    $number = str_pad(
        $index + 1,
        2,
        '0',
        STR_PAD_LEFT
    );


    /*
    |--------------------------------------------------------------------------
    | Catégorie
    |--------------------------------------------------------------------------
    */

    $category = $course->category ?? 'Audiovisuel';


    /*
    |--------------------------------------------------------------------------
    | Niveau
    |--------------------------------------------------------------------------
    */

    $level = $course->level ?? 'BAC';


    /*
    |--------------------------------------------------------------------------
    | Effectif
    |--------------------------------------------------------------------------
    */

    $studentsCount = $course->students_count ?? 0;

@endphp


<article
    class="
        group
        relative
        overflow-hidden
        rounded-[2px]
        border
        border-white/10
        bg-[#111315]
        transition-all
        duration-500
        hover:-translate-y-1
        hover:border-[#F5B800]/50
    "
>


    {{-- =====================================================
         IMAGE
    ====================================================== --}}

    <div
        class="
            relative
            aspect-[16/10]
            overflow-hidden
            bg-[#181A1C]
        "
    >

        @if($image)

            <img
                src="{{ $image }}"
                alt="{{ $course->title }}"
                class="
                    h-full
                    w-full
                    object-cover
                    transition-transform
                    duration-700
                    ease-out
                    group-hover:scale-105
                "
            >

        @else

            {{-- Pas d'image : on garde une composition élégante
                 sans créer de fichier placeholder. --}}

            <div
                class="
                    absolute
                    inset-0
                    flex
                    items-center
                    justify-center
                    bg-gradient-to-br
                    from-[#202326]
                    via-[#151719]
                    to-[#0B0C0D]
                "
            >

                <x-lucide-clapperboard
                    class="
                        h-16
                        w-16
                        text-[#F5B800]/40
                    "
                />

            </div>

        @endif


        {{-- Voile sombre --}}

        <div
            class="
                absolute
                inset-0
                bg-gradient-to-t
                from-black/50
                via-transparent
                to-transparent
                opacity-60
            "
        ></div>


        {{-- Numéro --}}

        <div
            class="
                absolute
                left-4
                top-4
                flex
                h-10
                min-w-10
                items-center
                justify-center
                rounded-sm
                bg-black/75
                px-3
                text-sm
                font-medium
                text-[#F5B800]
                backdrop-blur-sm
            "
        >

            {{ $number }}

        </div>

    </div>


    {{-- =====================================================
         CONTENU
    ====================================================== --}}

    <div class="p-6 md:p-7">


        {{-- Catégorie --}}

        <div
            class="
                mb-5
                flex
                items-center
                gap-2
                text-[11px]
                font-medium
                uppercase
                tracking-[0.18em]
                text-[#F5B800]
            "
        >

            <x-lucide-layers
                class="h-4 w-4"
            />

            <span>
                {{ $category }}
            </span>

        </div>


        {{-- Titre --}}

        <h3
            class="
                min-h-[72px]
                text-2xl
                font-semibold
                uppercase
                leading-[1.05]
                tracking-[-0.03em]
                text-white
                md:text-[26px]
            "
        >

            {{ $course->title }}

        </h3>


        {{-- Description --}}

        <p
            class="
                mt-5
                min-h-[84px]
                text-[15px]
                leading-7
                text-white/55
            "
        >

            {{ $course->description }}

        </p>


        {{-- =================================================
             INFORMATIONS
        ================================================== --}}

        <div
            class="
                mt-6
                flex
                flex-wrap
                items-center
                gap-x-6
                gap-y-3
                border-b
                border-white/10
                pb-6
            "
        >

            {{-- Durée --}}

            @if($course->duration)

                <div
                    class="
                        flex
                        items-center
                        gap-2
                        text-xs
                        uppercase
                        tracking-wide
                        text-white/80
                    "
                >

                    <x-lucide-clock
                        class="h-4 w-4 text-[#F5B800]"
                    />

                    <span>
                        {{ $course->duration }}
                    </span>

                </div>

            @endif


            {{-- Niveau --}}

            <div
                class="
                    flex
                    items-center
                    gap-2
                    text-xs
                    uppercase
                    tracking-wide
                    text-white/80
                "
            >

                <x-lucide-bar-chart-3
                    class="h-4 w-4 text-[#F5B800]"
                />

                <span>
                    {{ $level }}
                </span>

            </div>


            {{-- Effectif --}}

            @if($studentsCount > 0)

                <div
                    class="
                        flex
                        items-center
                        gap-2
                        text-xs
                        uppercase
                        tracking-wide
                        text-white/80
                    "
                >

                    <x-lucide-users
                        class="h-4 w-4 text-[#F5B800]"
                    />

                    <span>
                        {{ $studentsCount }} étudiants
                    </span>

                </div>

            @endif

        </div>


        {{-- =================================================
             DÉTAIL
        ================================================== --}}

        <a
            href="{{ url('/formations/' . $course->slug) }}"
            class="
                mt-6
                flex
                items-center
                justify-between
                text-sm
                font-medium
                uppercase
                tracking-wide
                text-white
                transition-colors
                duration-300
                hover:text-[#F5B800]
            "
        >

            <span>
                Voir le détail
            </span>

            <span
                class="
                    flex
                    h-8
                    w-8
                    items-center
                    justify-center
                    rounded-full
                    border
                    border-white/20
                    transition-all
                    duration-300
                    group-hover:border-[#F5B800]
                    group-hover:bg-[#F5B800]
                    group-hover:text-black
                "
            >

                <x-lucide-arrow-up-right
                    class="h-4 w-4"
                />

            </span>

        </a>

    </div>

</article>