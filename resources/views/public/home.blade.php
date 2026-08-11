@extends('layouts.public')


@section('title')
EMSI — École de Formation Audiovisuelle
@endsection


@section('description')
EMSI — École de Formation Audiovisuelle au cœur du Grand Théâtre National Doudou Ndiaye Rose, Dakar.
@endsection


@section('content')


{{-- =========================================================
     HERO
========================================================= --}}

{{-- =========================================================
     HERO — L'IMAGE EST UN LANGAGE
========================================================= --}}

<section
    id="hero"
    class="relative min-h-screen overflow-hidden bg-[#080808] text-white">

    {{-- =====================================================
         ATMOSPHÈRE
    ====================================================== --}}

    <div class="absolute inset-0 pointer-events-none">

        {{-- Halo très discret --}}
        <div
            class="absolute right-[12%] top-[28%] w-[520px] h-[520px]
                   rounded-full bg-violet-900/10 blur-[140px]"></div>

        {{-- Dégradé inférieur --}}
        <div
            class="absolute inset-x-0 bottom-0 h-[35%]
                   bg-gradient-to-t from-[#080808] to-transparent"></div>

        {{-- Dégradé gauche --}}
        <div
            class="absolute inset-y-0 left-0 w-[45%]
                   bg-gradient-to-r from-black/60 to-transparent"></div>

    </div>


    {{-- =====================================================
         IMAGE DE FOND
    ====================================================== --}}

    <div class="absolute inset-0 pointer-events-none">

        <img
            src="{{ asset('images/hero.jpg') }}"
            alt="Grand Théâtre National Doudou Ndiaye Rose"
            class="w-full h-full object-cover opacity-[0.32]">

        {{-- Voile sombre --}}
        <div
            class="absolute inset-0
                   bg-gradient-to-r
                   from-[#080808]/95
                   via-[#080808]/65
                   to-[#080808]/35"></div>

        <div
            class="absolute inset-0
                   bg-gradient-to-t
                   from-[#080808]
                   via-transparent
                   to-[#080808]/50"></div>

    </div>


    {{-- =====================================================
         CONTENU
    ====================================================== --}}

    <div
        class="relative z-10 max-w-[1600px] mx-auto
               min-h-screen px-6 lg:px-12
               pt-32 pb-12
               flex flex-col">


        {{-- =================================================
             INFORMATIONS SUPÉRIEURES
        ================================================== --}}

        <div class="flex items-start justify-between">

            <div>

                <p
                    class="text-[9px] md:text-[10px]
                           uppercase tracking-[0.38em]
                           text-white/45">
                    École de Formation Audiovisuelle
                </p>

            </div>


            <div class="hidden md:block">

                <p
                    class="text-[9px]
                           uppercase tracking-[0.35em]
                           text-white/35">
                    Dakar — Sénégal
                </p>

            </div>

        </div>



        {{-- =================================================
             ZONE PRINCIPALE
        ================================================== --}}

        <div
            class="flex-1 grid
                   lg:grid-cols-[0.9fr_1.1fr]
                   items-center
                   gap-8 lg:gap-16">


            {{-- =============================================
                 TYPOGRAPHIE
            ============================================== --}}

            <div
                class="relative z-20
                       max-w-[720px]
                       pt-12 lg:pt-0">

                <div
                    class="flex items-center gap-4 mb-7">

                    <span
                        class="block w-10 h-px bg-white/30"></span>

                    <span
                        class="text-[9px]
                               uppercase
                               tracking-[0.4em]
                               text-white/45">
                        EMSI
                    </span>

                </div>


                <h1
                    class="font-serif
                           text-[clamp(4.5rem,8vw,9rem)]
                           leading-[0.78]
                           tracking-[-0.055em]">

                    <span
                        class="block text-white">
                        L'image
                    </span>


                    <span
                        class="block
                               ml-[0.12em]
                               text-white/45
                               italic
                               font-light">
                        est un
                    </span>


                    <span
                        class="block
                               text-white/45
                               italic
                               font-light">
                        langage.
                    </span>

                </h1>


                <div
                    class="mt-10
                           max-w-[480px]">

                    <p
                        class="text-sm md:text-base
                               leading-relaxed
                               text-white/55">
                        Former une nouvelle génération
                        de créateurs audiovisuels au cœur
                        du Grand Théâtre National
                        Doudou Ndiaye Rose.
                    </p>

                </div>


                {{-- CTA --}}

                <div
                    class="flex flex-wrap
                           items-center
                           gap-3
                           mt-8">

                    <a
                        href="#formations"
                        class="group
                               inline-flex
                               items-center
                               gap-3
                               rounded-full
                               bg-white
                               text-black
                               px-6 py-3.5
                               text-sm
                               font-medium
                               transition
                               hover:bg-white/90">

                        Découvrir les formations

                        <span
                            class="flex
                                   items-center
                                   justify-center
                                   w-6 h-6
                                   rounded-full
                                   bg-black
                                   text-white
                                   transition
                                   group-hover:translate-x-1">

                            <x-lucide-arrow-up-right
                                class="w-3.5 h-3.5" />

                        </span>

                    </a>


                    <a
                        href="{{ route('public.admissions.create') }}"
                        class="inline-flex
                               items-center
                               rounded-full
                               border border-white/20
                               px-6 py-3.5
                               text-sm
                               text-white/70
                               transition
                               hover:border-white/50
                               hover:text-white">

                        Candidater

                    </a>

                </div>

            </div>



            {{-- =============================================
                 COMPOSITION DES OBJETS
            ============================================== --}}

            <div
                class="relative
                       h-[520px]
                       md:h-[600px]
                       lg:h-[650px]
                       w-full">


                {{-- =========================================
                     CLAP
                ========================================== --}}

                <div
                    class="absolute
                           left-[7%]
                           top-[8%]
                           w-[105px]
                           md:w-[125px]
                           animate-hero-float-slow">

                    <img
                        src="{{ asset('images/objects/clap.png') }}"
                        alt="Clap de cinéma"
                        class="w-full h-auto object-contain">

                </div>



                {{-- =========================================
                     PROJECTEUR
                ========================================== --}}

                <div
                    class="absolute
                           left-[2%]
                           bottom-[18%]
                           w-[135px]
                           md:w-[165px]
                           animate-hero-float-reverse">

                    <img
                        src="{{ asset('images/objects/projecteur.png') }}"
                        alt="Projecteur audiovisuel"
                        class="w-full h-auto object-contain">

                </div>



                {{-- =========================================
                     CAMÉRA — OBJET PRINCIPAL
                ========================================== --}}

                <div
                    class="absolute
                           left-[30%]
                           top-[23%]
                           w-[230px]
                           md:w-[290px]
                           lg:w-[320px]
                           animate-hero-float">

                    <img
                        src="{{ asset('images/objects/camera.png') }}"
                        alt="Caméra professionnelle"
                        class="w-full h-auto object-contain
                               drop-shadow-[0_30px_60px_rgba(0,0,0,0.55)]">

                </div>



                {{-- =========================================
                     MICROPHONE
                ========================================== --}}

                <div
                    class="absolute
                           right-[7%]
                           top-[5%]
                           w-[125px]
                           md:w-[155px]
                           animate-hero-float-reverse">

                    <img
                        src="{{ asset('images/objects/microphone.png') }}"
                        alt="Microphone audiovisuel"
                        class="w-full h-auto object-contain">

                </div>



                {{-- =========================================
                     CONSOLE
                ========================================== --}}

                <div
                    class="absolute
                           right-[15%]
                           bottom-[15%]
                           w-[170px]
                           md:w-[220px]
                           animate-hero-float-slow">

                    <img
                        src="{{ asset('images/objects/console.png') }}"
                        alt="Console audiovisuelle"
                        class="w-full h-auto object-contain
                               drop-shadow-[0_25px_50px_rgba(0,0,0,0.45)]">

                </div>



                {{-- =========================================
                     CASQUE
                ========================================== --}}

                <div
                    class="absolute
                           right-[1%]
                           bottom-[3%]
                           w-[135px]
                           md:w-[175px]
                           animate-hero-float">

                    <img
                        src="{{ asset('images/objects/casque.png') }}"
                        alt="Casque audio professionnel"
                        class="w-full h-auto object-contain">

                </div>



                {{-- =========================================
                     PETITS POINTS DE COMPOSITION
                ========================================== --}}

                <span
                    class="absolute
                           left-[24%]
                           top-[17%]
                           w-1.5 h-1.5
                           rounded-full
                           bg-white/40"></span>

                <span
                    class="absolute
                           right-[28%]
                           top-[32%]
                           w-1 h-1
                           rounded-full
                           bg-white/30"></span>

                <span
                    class="absolute
                           left-[35%]
                           bottom-[12%]
                           w-1 h-1
                           rounded-full
                           bg-white/25"></span>

            </div>

        </div>



        {{-- =================================================
             BAS DU HERO
        ================================================== --}}

        <div
            class="border-t border-white/10
                   pt-4
                   flex items-center
                   justify-between">

            <div
                class="hidden md:flex
                       items-center gap-12">

                <span
                    class="text-[9px]
                           uppercase
                           tracking-[0.35em]
                           text-white/25">
                    Grand Théâtre National
                </span>

                <span
                    class="text-[9px]
                           uppercase
                           tracking-[0.35em]
                           text-white/25">
                    Doudou Ndiaye Rose
                </span>

            </div>


            <div
                class="flex items-center gap-3
                       text-[9px]
                       uppercase
                       tracking-[0.35em]
                       text-white/30">

                <span>
                    Explorer
                </span>

                <span
                    class="flex
                           items-center
                           justify-center
                           w-8 h-8
                           rounded-full
                           border border-white/20">

                    <x-lucide-arrow-down
                        class="w-3.5 h-3.5" />

                </span>

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     02 — L'ÉCOLE
========================================================= --}}

<section
    id="ecole"
    class="relative overflow-hidden bg-[#F1EEE7] text-[#111111]">

    <div
        class="max-w-[1500px] mx-auto
               px-8 lg:px-16
               py-20 lg:py-24">

        {{-- =================================================
             EN-TÊTE
        ================================================== --}}

        <div
            class="flex items-center gap-4 mb-12">

            <span
                class="text-[9px]
                       uppercase
                       tracking-[0.3em]
                       text-black/35">
                02
            </span>

            <span
                class="w-8 h-px bg-black/20"></span>

            <span
                class="text-[9px]
                       uppercase
                       tracking-[0.3em]
                       text-black/40">
                L'école
            </span>

        </div>


        {{-- =================================================
             TITRE + INTRODUCTION
        ================================================== --}}

        <div
            class="grid
                   lg:grid-cols-[1.4fr_0.6fr]
                   gap-10
                   items-end">

            <div>

                <p
                    class="mb-5
                           text-[9px]
                           uppercase
                           tracking-[0.3em]
                           text-black/35">
                    Une école dédiée à l'image,
                    au son et au récit
                </p>


                <h2
                    class="font-serif
                           text-[clamp(3.5rem,6.5vw,7rem)]
                           font-normal
                           leading-[0.86]
                           tracking-[-0.055em]">

                    Former des

                    <span class="italic text-black/40">
                        regards,
                    </span>

                    <br>

                    créer des

                    <span class="italic text-black/40">
                        récits.
                    </span>

                </h2>

            </div>


            <div
                class="max-w-sm pb-1">

                <p
                    class="text-sm
                           leading-[1.7]
                           text-black/55">

                    EMSI forme une nouvelle génération
                    de créateurs audiovisuels en développant
                    à la fois la maîtrise technique,
                    la créativité et le regard.

                </p>

            </div>

        </div>



        {{-- =================================================
             IMAGE + TEXTE
        ================================================== --}}

        <div
            class="grid
                   lg:grid-cols-[1.5fr_0.5fr]
                   gap-5
                   mt-14">

            {{-- IMAGE --}}

            <div
                class="relative
                       h-[360px]
                       lg:h-[430px]
                       overflow-hidden
                       bg-[#E4DFD5]">

                @if(file_exists(public_path('images/ecole.jpg')))

                <img
                    src="{{ asset('images/ecole.jpg') }}"
                    alt="L'école EMSI"
                    class="w-full h-full
                               object-cover
                               transition duration-700
                               hover:scale-[1.02]">

                @else

                <div
                    class="w-full h-full
                               flex items-center
                               justify-center">

                    <div class="text-center">

                        <p
                            class="font-serif
                                       text-4xl
                                       text-black/10">
                            EMSI
                        </p>

                        <p
                            class="mt-2
                                       text-[8px]
                                       uppercase
                                       tracking-[0.25em]
                                       text-black/20">
                            Image à venir
                        </p>

                    </div>

                </div>

                @endif


                {{-- Légende --}}

                <div
                    class="absolute
                           bottom-4
                           left-5
                           right-5
                           flex
                           justify-between">

                    <span
                        class="text-[8px]
                               uppercase
                               tracking-[0.25em]
                               text-white/70">
                        EMSI
                    </span>

                    <span
                        class="text-[8px]
                               uppercase
                               tracking-[0.25em]
                               text-white/70">
                        Dakar
                    </span>

                </div>

            </div>


            {{-- TEXTE LATÉRAL --}}

            <div
                class="flex
                       flex-col
                       justify-between
                       border-t
                       lg:border-t-0
                       lg:border-l
                       border-black/10
                       pt-6
                       lg:pt-0
                       lg:pl-7">

                <div>

                    <p
                        class="text-[8px]
                               uppercase
                               tracking-[0.25em]
                               text-black/30">
                        Notre approche
                    </p>


                    <p
                        class="mt-4
                               font-serif
                               text-2xl
                               leading-tight
                               text-black/80">

                        La technique devient
                        un langage.

                    </p>

                </div>


                <p
                    class="mt-8
                           text-xs
                           leading-[1.8]
                           text-black/40">

                    Apprendre à cadrer,
                    éclairer, enregistrer,
                    monter et raconter.

                </p>


                <div
                    class="mt-8
                           pt-4
                           border-t
                           border-black/10">

                    <p
                        class="text-[8px]
                               uppercase
                               tracking-[0.25em]
                               text-black/30">
                        Grand Théâtre National
                        Doudou Ndiaye Rose
                    </p>

                </div>

            </div>

        </div>



        {{-- =================================================
             PHRASE FINALE
        ================================================== --}}

        <div
            class="mt-12
                   flex
                   items-center
                   justify-between
                   border-t
                   border-black/10
                   pt-5">

            <p
                class="text-[8px]
                       uppercase
                       tracking-[0.25em]
                       text-black/30">
                Apprendre
                ·
                Expérimenter
                ·
                Créer
            </p>


            <span
                class="text-[8px]
                       uppercase
                       tracking-[0.25em]
                       text-black/25">
                02 / 07
            </span>

        </div>

    </div>

</section>



{{-- =========================================================
     03 — LE LIEU
========================================================= --}}

<section
    id="lieu"
    class="relative bg-[#111111] text-white overflow-hidden">

    <div
        class="max-w-[1600px] mx-auto
               px-8 lg:px-16
               py-20 lg:py-24">

        {{-- =================================================
             EN-TÊTE
        ================================================== --}}

        <div
            class="flex items-center justify-between
                   mb-10">

            <div
                class="flex items-center gap-4">

                <span
                    class="text-[9px]
                           uppercase
                           tracking-[0.3em]
                           text-white/30">
                    03
                </span>

                <span
                    class="w-8 h-px bg-white/20"></span>

                <span
                    class="text-[9px]
                           uppercase
                           tracking-[0.3em]
                           text-white/40">
                    Le lieu
                </span>

            </div>


            <span
                class="hidden md:block
                       text-[8px]
                       uppercase
                       tracking-[0.3em]
                       text-white/25">
                Dakar — Sénégal
            </span>

        </div>



        {{-- =================================================
             GRANDE IMAGE
        ================================================== --}}

        <div
            class="relative
                   h-[480px]
                   md:h-[560px]
                   lg:h-[650px]
                   overflow-hidden
                   bg-[#181818]">

            @if(file_exists(public_path('images/grand-theatre.jpg')))

            <img
                src="{{ asset('images/grand-theatre.jpg') }}"
                alt="Grand Théâtre National Doudou Ndiaye Rose"
                class="absolute inset-0
                           w-full h-full
                           object-cover
                           transition duration-[1500ms]
                           hover:scale-[1.025]">

            @elseif(file_exists(public_path('images/theatre.jpg')))

            <img
                src="{{ asset('images/theatre.jpg') }}"
                alt="Grand Théâtre National Doudou Ndiaye Rose"
                class="absolute inset-0
                           w-full h-full
                           object-cover
                           transition duration-[1500ms]
                           hover:scale-[1.025]">

            @else

            <div
                class="absolute inset-0
                           flex items-center justify-center">

                <div class="text-center">

                    <p
                        class="font-serif
                                   text-6xl
                                   text-white/10">
                        Grand Théâtre
                    </p>

                    <p
                        class="mt-3
                                   text-[8px]
                                   uppercase
                                   tracking-[0.3em]
                                   text-white/20">
                        Photographie à venir
                    </p>

                </div>

            </div>

            @endif


            {{-- Voile photographique --}}

            <div
                class="absolute inset-0
                       bg-gradient-to-t
                       from-black/80
                       via-transparent
                       to-black/10"></div>


            {{-- =================================================
                 TITRE SUR L'IMAGE
            ================================================== --}}

            <div
                class="absolute
                       left-6 md:left-10
                       bottom-8 md:bottom-10
                       max-w-4xl">

                <p
                    class="text-[8px]
                           uppercase
                           tracking-[0.35em]
                           text-white/50
                           mb-5">
                    Un lieu de création
                </p>


                <h2
                    class="font-serif
                           text-[clamp(3.5rem,7vw,7.5rem)]
                           leading-[0.82]
                           tracking-[-0.055em]">

                    Grand Théâtre

                    <span
                        class="italic
                               text-white/50">
                        National.
                    </span>

                </h2>

            </div>


            {{-- Numéro --}}

            <div
                class="absolute
                       top-6
                       right-6">

                <span
                    class="flex
                           items-center
                           justify-center
                           w-12 h-12
                           rounded-full
                           border border-white/20
                           text-[8px]
                           tracking-[0.2em]
                           text-white/50">
                    03
                </span>

            </div>

        </div>



        {{-- =================================================
             TEXTE SOUS L'IMAGE
        ================================================== --}}

        <div
            class="grid
                   md:grid-cols-[1fr_1fr]
                   gap-10
                   mt-8
                   pt-7
                   border-t border-white/10">

            <div>

                <p
                    class="max-w-xl
                           text-base
                           leading-[1.8]
                           text-white/55">

                    L'EMSI prend place dans les locaux du
                    Grand Théâtre National Doudou Ndiaye Rose,
                    un lieu emblématique de la création
                    culturelle sénégalaise.

                </p>

            </div>


            <div
                class="md:text-right">

                <p
                    class="text-[9px]
                           uppercase
                           tracking-[0.3em]
                           text-white/30">
                    Grand Théâtre National
                </p>


                <p
                    class="mt-2
                           text-[9px]
                           uppercase
                           tracking-[0.3em]
                           text-white/30">
                    Doudou Ndiaye Rose
                </p>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
     FORMATIONS
========================================================= --}}

<section
    id="formations"
    class="
        relative
        overflow-hidden
        bg-[#080A0C]
        py-20
        text-white
        md:py-24
        lg:py-28
    ">


    {{-- Légère lumière décorative --}}

    <div
        class="
            pointer-events-none
            absolute
            -right-40
            top-20
            h-[500px]
            w-[500px]
            rounded-full
            bg-[#F5B800]/[0.035]
            blur-[120px]
        "></div>


    <div
        class="
            relative
            mx-auto
            max-w-[1500px]
            px-6
            lg:px-12
        ">


        {{-- =================================================
             EN-TÊTE
        ================================================== --}}

        <div
            class="
                grid
                grid-cols-1
                gap-10
                lg:grid-cols-12
                lg:items-end
            ">


            {{-- GAUCHE --}}

            <div class="lg:col-span-6">

                <div
                    class="
                        mb-5
                        flex
                        items-center
                        gap-3
                        text-xs
                        font-medium
                        uppercase
                        tracking-[0.25em]
                        text-[#F5B800]
                    ">

                    <span class="h-px w-8 bg-[#F5B800]"></span>

                    <span>
                        Formations
                    </span>

                </div>


                <h2
                    class="
                        max-w-xl
                        text-5xl
                        font-semibold
                        uppercase
                        leading-[0.9]
                        tracking-[-0.055em]
                        md:text-6xl
                        lg:text-7xl
                    ">

                    Nos
                    <br>

                    <span class="text-white/90">
                        formations
                    </span>

                </h2>


                <div
                    class="
                        mt-7
                        h-[3px]
                        w-16
                        bg-[#F5B800]
                    "></div>


                <p
                    class="
                        mt-6
                        max-w-xl
                        text-base
                        leading-7
                        text-white/55
                        md:text-lg
                    ">

                    Des formations complètes et
                    professionnalisantes pour révéler
                    votre talent et façonner votre avenir.

                </p>

            </div>


            {{-- DROITE --}}

            <div
                class="
                    lg:col-span-5
                    lg:col-start-8
                ">

                <div
                    class="
                        space-y-5
                        border-l
                        border-white/10
                        pl-6
                    ">

                    {{-- Bloc 1 --}}

                    <div
                        class="
                            flex
                            items-start
                            gap-4
                        ">

                        <div
                            class="
                                mt-1
                                flex
                                h-10
                                w-10
                                flex-shrink-0
                                items-center
                                justify-center
                                rounded-full
                                border
                                border-[#F5B800]
                                text-[#F5B800]
                            ">

                            <x-lucide-graduation-cap
                                class="h-5 w-5" />

                        </div>


                        <div>

                            <p
                                class="
                                    font-medium
                                    text-white
                                ">
                                Formations professionnalisantes
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-sm
                                    text-white/45
                                ">
                                Du technicien au créateur
                            </p>

                        </div>

                    </div>


                    {{-- Séparateur --}}

                    <div class="h-px bg-white/10"></div>


                    {{-- Bloc 2 --}}

                    <div
                        class="
                            flex
                            items-start
                            gap-4
                        ">

                        <div
                            class="
                                mt-1
                                flex
                                h-10
                                w-10
                                flex-shrink-0
                                items-center
                                justify-center
                                rounded-full
                                border
                                border-[#F5B800]
                                text-[#F5B800]
                            ">

                            <x-lucide-clock
                                class="h-5 w-5" />

                        </div>


                        <div>

                            <p
                                class="
                                    font-medium
                                    text-white
                                ">
                                Durée
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-sm
                                    text-white/45
                                ">
                                1 à 3 ans selon la formation
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- =================================================
             CARTES
        ================================================== --}}

        <div
            class="
                mt-12
                grid
                grid-cols-1
                gap-4
                sm:grid-cols-2
                xl:grid-cols-4
            ">

            @forelse($courses->take(4) as $index => $course)

            <x-public.formation-item
                :course="$course"
                :index="$index" />

            @empty

            <div
                class="
                        col-span-full
                        border
                        border-white/10
                        bg-white/[0.02]
                        p-10
                        text-center
                    ">

                <x-lucide-graduation-cap
                    class="
                            mx-auto
                            h-10
                            w-10
                            text-[#F5B800]/50
                        " />

                <p
                    class="
                            mt-4
                            text-white/50
                        ">
                    Les formations seront bientôt disponibles.
                </p>

            </div>

            @endforelse

        </div>



        {{-- =================================================
             BOUTON TOUTES LES FORMATIONS
        ================================================== --}}

        @if($courses->count() > 4)

        <div class="mt-10 flex justify-center">

            <a
                href="{{ url('/#formations') }}"
                class="
                        group
                        inline-flex
                        items-center
                        gap-4
                        border
                        border-[#F5B800]/70
                        px-8
                        py-4
                        text-sm
                        font-medium
                        uppercase
                        tracking-[0.12em]
                        text-[#F5B800]
                        transition-all
                        duration-300
                        hover:bg-[#F5B800]
                        hover:text-black
                    ">

                Voir toutes les formations

                <x-lucide-arrow-right
                    class="
                            h-4
                            w-4
                            transition-transform
                            duration-300
                            group-hover:translate-x-1
                        " />

            </a>

        </div>

        @endif

    </div>

</section>



{{-- =========================================================
     GALERIE
========================================================= --}}

<section
    id="galerie"
    class="gallery-section">

    <div class="public-container">

        <p class="public-section-label public-section-label--light">
            04 — Galerie
        </p>


        <div class="gallery-section__header">

            <h2>
                Regards.
            </h2>


            <p>

                Images, créations et fragments
                de la vie de l'école.

            </p>

        </div>


        <div class="gallery-grid">

            @forelse($galleries as $index => $gallery)

            <x-public.gallery-item
                :gallery="$gallery"
                :index="$index" />

            @empty

            <div class="empty-state">

                La galerie sera bientôt disponible.

            </div>

            @endforelse

        </div>

    </div>

</section>



{{-- =========================================================
     ACTUALITÉS
========================================================= --}}

<section
    id="actualites"
    class="news-section">

    <div class="public-container">

        <p class="public-section-label">
            05 — Actualités
        </p>


        <div class="news-section__header">

            <h2>
                Journal EMSI.
            </h2>


            <p>

                La vie de l'école, ses projets,
                ses créations et ses actualités.

            </p>

        </div>


        <div class="news-grid">

            @forelse($news as $article)

            <x-public.news-card
                :article="$article" />

            @empty

            <div class="empty-state">

                Aucune actualité pour le moment.

            </div>

            @endforelse

        </div>

    </div>

</section>



{{-- =========================================================
     ADMISSIONS
========================================================= --}}

<section
    id="admissions"
    class="admissions-section">

    <div class="public-container">

        <p class="public-section-label public-section-label--light">
            06 — Admissions
        </p>


        <div class="admissions-section__content">

            <h2>

                Votre regard

                <span>
                    commence ici.
                </span>

            </h2>


            <p>

                Vous souhaitez apprendre les métiers
                de l'audiovisuel et développer votre
                propre langage créatif ?

            </p>


            <a
                href="{{ route('public.admissions.create') }}"
                class="admissions-button">

                Déposer une candidature

                <span>
                    ↗
                </span>

            </a>

        </div>

    </div>

</section>


@endsection