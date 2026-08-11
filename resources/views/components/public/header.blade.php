<header
    id="public-header"
    class="
        fixed
        top-0
        left-0
        right-0
        z-50

        border-b
        border-black/5

        bg-white/95
        backdrop-blur-xl

        shadow-[0_1px_20px_rgba(0,0,0,0.04)]
    "
>

    <div
        class="
            max-w-7xl
            mx-auto

            h-[76px]

            px-6
            lg:px-10

            flex
            items-center
            justify-between
        "
    >

        {{-- =====================================================
             LOGO
        ====================================================== --}}

        <a
            href="{{ url('/') }}"
            aria-label="EMSI — Accueil"
            class="
                flex
                items-center

                shrink-0
            "
        >

            @if(file_exists(public_path('images/logo.png')))

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="EMSI — École de Formation Audiovisuelle"
                    class="
                        h-11
                        md:h-12
                        w-auto

                        object-contain
                    "
                >

            @else

                {{-- Fallback si le logo n'existe pas --}}

                <div
                    class="
                        flex
                        items-center
                        gap-3
                    "
                >

                    <div
                        class="
                            flex
                            h-10
                            w-10

                            items-center
                            justify-center

                            rounded-xl

                            bg-[#111]

                            text-sm
                            font-bold

                            text-white
                        "
                    >
                        E
                    </div>

                    <div>

                        <div
                            class="
                                text-sm
                                font-bold
                                tracking-[0.16em]

                                text-[#111]
                            "
                        >
                            EMSI
                        </div>

                        <div
                            class="
                                text-[8px]
                                uppercase
                                tracking-[0.15em]

                                text-black/40
                            "
                        >
                            Audiovisuel
                        </div>

                    </div>

                </div>

            @endif

        </a>


        {{-- =====================================================
             NAVIGATION DESKTOP
        ====================================================== --}}

        <nav
            class="
                hidden
                md:flex

                items-center
                gap-8
                lg:gap-10

                ml-auto
                mr-8
            "
            aria-label="Navigation principale"
        >

            <a
                href="{{ url('/') }}"
                class="
                    text-[13px]
                    font-medium

                    text-black/60

                    hover:text-black

                    transition-colors
                    duration-200
                "
            >
                Accueil
            </a>


            <a
                href="{{ url('/#ecole') }}"
                class="
                    text-[13px]
                    font-medium

                    text-black/60

                    hover:text-black

                    transition-colors
                    duration-200
                "
            >
                L'école
            </a>


            <a
                href="{{ url('/#formations') }}"
                class="
                    text-[13px]
                    font-medium

                    text-black/60

                    hover:text-black

                    transition-colors
                    duration-200
                "
            >
                Formations
            </a>


            <a
                href="{{ url('/#galerie') }}"
                class="
                    text-[13px]
                    font-medium

                    text-black/60

                    hover:text-black

                    transition-colors
                    duration-200
                "
            >
                Galerie
            </a>


            <a
                href="{{ url('/#actualites') }}"
                class="
                    text-[13px]
                    font-medium

                    text-black/60

                    hover:text-black

                    transition-colors
                    duration-200
                "
            >
                Actualités
            </a>

        </nav>


        {{-- =====================================================
             ACTIONS
        ====================================================== --}}

        <div class="flex items-center gap-3">

            {{-- CTA desktop --}}

            <a
                href="{{ route('public.admissions.create') }}"
                class="
                    hidden
                    md:inline-flex

                    items-center
                    gap-2

                    rounded-full

                    bg-[#111111]
                    px-5
                    py-2.5

                    text-[12px]
                    font-semibold

                    text-white

                    hover:bg-[#F5B800]
                    hover:text-black

                    transition-all
                    duration-200
                "
            >

                <span>
                    Candidater
                </span>

                <span
                    aria-hidden="true"
                    class="text-sm"
                >
                    ↗
                </span>

            </a>


            {{-- =================================================
                 MENU BURGER MOBILE
            ================================================== --}}

            <button
                type="button"
                id="public-mobile-menu-button"

                class="
                    md:hidden

                    relative

                    flex
                    h-10
                    w-10

                    items-center
                    justify-center

                    rounded-full

                    border
                    border-black/10

                    bg-white

                    text-black

                    hover:bg-black/5

                    transition
                "

                aria-label="Ouvrir le menu"
                aria-expanded="false"
                aria-controls="public-mobile-menu"
            >

                <span
                    id="menu-icon-open"
                    class="
                        absolute

                        flex
                        flex-col
                        gap-1.5
                    "
                >

                    <span
                        class="
                            block
                            h-[1.5px]
                            w-5

                            bg-black
                        "
                    ></span>

                    <span
                        class="
                            block
                            h-[1.5px]
                            w-5

                            bg-black
                        "
                    ></span>

                </span>


                {{-- X --}}

                <span
                    id="menu-icon-close"
                    class="
                        absolute

                        hidden

                        text-xl
                        leading-none
                    "
                >
                    ×
                </span>

            </button>

        </div>

    </div>


    {{-- =========================================================
         MENU MOBILE
    ========================================================== --}}

    <div
        id="public-mobile-menu"
        class="
            hidden

            border-t
            border-black/5

            bg-white
        "
    >

        <nav
            class="
                max-w-7xl
                mx-auto

                px-6
                py-5

                flex
                flex-col
            "
            aria-label="Navigation mobile"
        >

            {{-- Accueil --}}

            <a
                href="{{ url('/') }}"
                class="
                    mobile-nav-link

                    flex
                    items-center
                    justify-between

                    py-4

                    border-b
                    border-black/5

                    text-sm
                    font-medium

                    text-black
                "
            >

                <span class="flex items-center gap-4">

                    <span
                        class="
                            text-[10px]
                            tracking-[0.15em]
                            text-black/30
                        "
                    >
                        01
                    </span>

                    Accueil

                </span>

                <span class="text-black/30">
                    ↗
                </span>

            </a>


            {{-- École --}}

            <a
                href="{{ url('/#ecole') }}"
                class="
                    mobile-nav-link

                    flex
                    items-center
                    justify-between

                    py-4

                    border-b
                    border-black/5

                    text-sm
                    font-medium

                    text-black
                "
            >

                <span class="flex items-center gap-4">

                    <span
                        class="
                            text-[10px]
                            tracking-[0.15em]
                            text-black/30
                        "
                    >
                        02
                    </span>

                    L'école

                </span>

                <span class="text-black/30">
                    ↗
                </span>

            </a>


            {{-- Formations --}}

            <a
                href="{{ url('/#formations') }}"
                class="
                    mobile-nav-link

                    flex
                    items-center
                    justify-between

                    py-4

                    border-b
                    border-black/5

                    text-sm
                    font-medium

                    text-black
                "
            >

                <span class="flex items-center gap-4">

                    <span
                        class="
                            text-[10px]
                            tracking-[0.15em]
                            text-black/30
                        "
                    >
                        03
                    </span>

                    Formations

                </span>

                <span class="text-black/30">
                    ↗
                </span>

            </a>


            {{-- Galerie --}}

            <a
                href="{{ url('/#galerie') }}"
                class="
                    mobile-nav-link

                    flex
                    items-center
                    justify-between

                    py-4

                    border-b
                    border-black/5

                    text-sm
                    font-medium

                    text-black
                "
            >

                <span class="flex items-center gap-4">

                    <span
                        class="
                            text-[10px]
                            tracking-[0.15em]
                            text-black/30
                        "
                    >
                        04
                    </span>

                    Galerie

                </span>

                <span class="text-black/30">
                    ↗
                </span>

            </a>


            {{-- Actualités --}}

            <a
                href="{{ url('/#actualites') }}"
                class="
                    mobile-nav-link

                    flex
                    items-center
                    justify-between

                    py-4

                    border-b
                    border-black/5

                    text-sm
                    font-medium

                    text-black
                "
            >

                <span class="flex items-center gap-4">

                    <span
                        class="
                            text-[10px]
                            tracking-[0.15em]
                            text-black/30
                        "
                    >
                        05
                    </span>

                    Actualités

                </span>

                <span class="text-black/30">
                    ↗
                </span>

            </a>


            {{-- CTA --}}

            <a
                href="{{ route('public.admissions.create') }}"
                class="
                    mt-5

                    flex
                    items-center
                    justify-center
                    gap-3

                    rounded-full

                    bg-[#111111]

                    px-5
                    py-3.5

                    text-sm
                    font-semibold

                    text-white

                    hover:bg-[#F5B800]
                    hover:text-black

                    transition
                "
            >

                Candidater

                <span>
                    ↗
                </span>

            </a>

        </nav>

    </div>

</header>


{{-- =========================================================
     JAVASCRIPT MENU MOBILE
========================================================== --}}

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const button = document.getElementById(
        'public-mobile-menu-button'
    );

    const menu = document.getElementById(
        'public-mobile-menu'
    );

    const openIcon = document.getElementById(
        'menu-icon-open'
    );

    const closeIcon = document.getElementById(
        'menu-icon-close'
    );

    if (!button || !menu) {
        return;
    }


    button.addEventListener('click', function () {

        const isOpen =
            button.getAttribute('aria-expanded') === 'true';


        if (isOpen) {

            // Fermer

            menu.classList.add('hidden');

            button.setAttribute(
                'aria-expanded',
                'false'
            );

            button.setAttribute(
                'aria-label',
                'Ouvrir le menu'
            );

            if (openIcon) {
                openIcon.classList.remove('hidden');
            }

            if (closeIcon) {
                closeIcon.classList.add('hidden');
            }

        } else {

            // Ouvrir

            menu.classList.remove('hidden');

            button.setAttribute(
                'aria-expanded',
                'true'
            );

            button.setAttribute(
                'aria-label',
                'Fermer le menu'
            );

            if (openIcon) {
                openIcon.classList.add('hidden');
            }

            if (closeIcon) {
                closeIcon.classList.remove('hidden');
            }

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Fermer le menu après avoir cliqué sur un lien
    |--------------------------------------------------------------------------
    */

    const mobileLinks = document.querySelectorAll(
        '#public-mobile-menu .mobile-nav-link'
    );


    mobileLinks.forEach(function (link) {

        link.addEventListener('click', function () {

            menu.classList.add('hidden');

            button.setAttribute(
                'aria-expanded',
                'false'
            );

            button.setAttribute(
                'aria-label',
                'Ouvrir le menu'
            );

            if (openIcon) {
                openIcon.classList.remove('hidden');
            }

            if (closeIcon) {
                closeIcon.classList.add('hidden');
            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Fermer avec Escape
    |--------------------------------------------------------------------------
    */

    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {

            menu.classList.add('hidden');

            button.setAttribute(
                'aria-expanded',
                'false'
            );

            button.setAttribute(
                'aria-label',
                'Ouvrir le menu'
            );

            if (openIcon) {
                openIcon.classList.remove('hidden');
            }

            if (closeIcon) {
                closeIcon.classList.add('hidden');
            }

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Fermer automatiquement si on repasse en desktop
    |--------------------------------------------------------------------------
    */

    window.addEventListener('resize', function () {

        if (window.innerWidth >= 768) {

            menu.classList.add('hidden');

            button.setAttribute(
                'aria-expanded',
                'false'
            );

            if (openIcon) {
                openIcon.classList.remove('hidden');
            }

            if (closeIcon) {
                closeIcon.classList.add('hidden');
            }

        }

    });

});
</script>

@endpush