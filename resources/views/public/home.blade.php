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
======================================================={{-- =========================================================
     HERO — L'IMAGE EST UN LANGAGE
========================================================= --}}

<section
    id="hero"
    class="relative min-h-screen overflow-hidden bg-[#070709] text-white flex flex-col justify-between"
>

    {{-- =====================================================
         1. ATMOSPHÈRE CINÉMATIQUE & LUEURS STUDIO
    ====================================================== --}}
    <div class="absolute inset-0 pointer-events-none z-0">

        {{-- Lueur ambrée principale --}}
        <div class="absolute right-[20%] top-[15%] w-[600px] h-[600px] rounded-full bg-[#F5B800]/12 blur-[150px]"></div>

        {{-- Lueur violette studio profonde --}}
        <div class="absolute left-[10%] top-[35%] w-[650px] h-[650px] rounded-full bg-[#320080]/30 blur-[160px]"></div>

        {{-- Dégradés de profondeur et vignettage --}}
        <div class="absolute inset-x-0 bottom-0 h-[45%] bg-gradient-to-t from-[#070709] via-[#070709]/80 to-transparent"></div>
        <div class="absolute inset-y-0 left-0 w-[50%] bg-gradient-to-r from-[#070709]/95 via-[#070709]/60 to-transparent"></div>
        <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-[#070709]/90 to-transparent"></div>

    </div>


    {{-- =====================================================
         2. IMAGE DE FOND DU CAMPUS (GRAND THÉÂTRE)
    ====================================================== --}}
    <div class="absolute inset-0 pointer-events-none z-0">
        <img
            src="{{ asset('images/hero.jpg') }}"
            alt="Grand Théâtre National Doudou Ndiaye Rose - Dakar"
            class="w-full h-full object-cover opacity-[0.28] filter brightness-90 contrast-110"
        >
        <div class="absolute inset-0 bg-gradient-to-r from-[#070709]/98 via-[#070709]/75 to-[#070709]/45"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#070709] via-transparent to-[#070709]/60"></div>
    </div>


    {{-- =====================================================
         3. CONTENU PRINCIPAL
    ====================================================== --}}
    <div class="relative z-10 max-w-[1600px] mx-auto w-full px-6 lg:px-12 pt-20 sm:pt-28 lg:pt-36 pb-12 flex-1 flex flex-col justify-between">

        {{-- Barre supérieure d'informations --}}
        <div class="flex flex-wrap items-center justify-between gap-4">

            {{-- Badge Inscription Ouverte --}}
            <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-white/[0.05] border border-white/15 backdrop-blur-xl shadow-lg">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#F5B800] opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-[#F5B800]"></span>
                </span>
                <span class="text-xs uppercase tracking-[0.2em] font-bold text-[#F5B800]">
                    Inscriptions Session {{ date('Y') }}-{{ date('Y') + 1 }}
                </span>
            </div>

            {{-- Localisation d'exception --}}
            <div class="hidden sm:flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-white/[0.03] border border-white/10 backdrop-blur-md text-xs uppercase tracking-[0.2em] text-white/60 font-medium">
                <x-lucide-map-pin class="w-3.5 h-3.5 text-[#F5B800]" />
                <span>Grand Théâtre National</span>
                <span class="text-white/20">•</span>
                <span class="text-white/80 font-semibold">Dakar, Sénégal</span>
            </div>

        </div>


        {{-- Zone Principale : Typographie & Composition 3D --}}
        <div class="my-auto py-6 sm:py-8 lg:py-12 grid lg:grid-cols-[1fr_1.1fr] items-center gap-8 lg:gap-16">

            {{-- GAUCHE : Typographie & Valeur Ajoutée --}}
            <div class="relative z-20 max-w-[700px]">

                {{-- Tagline de l'école --}}
                <div class="inline-flex items-center gap-3 mb-6">
                    <span class="w-8 h-0.5 bg-gradient-to-r from-[#F5B800] to-transparent rounded-full"></span>
                    <span class="text-xs uppercase tracking-[0.28em] font-bold text-[#F5B800]">
                        École des Métiers du Son et de l'Image
                    </span>
                </div>

                {{-- Grand Titre Manifeste --}}
                <h1 class="font-sans font-extrabold text-[clamp(3.2rem,6.4vw,6.6rem)] leading-[1.02] tracking-tight text-white">
                    <span class="block">L'image</span>
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[#F5B800] via-[#FFD043] to-[#E59800]">
                        est un
                    </span>
                    <span class="block">langage.</span>
                </h1>

                {{-- Paragraphe d'impact --}}
                <p class="mt-8 text-base sm:text-lg leading-relaxed text-white/70 font-light max-w-xl">
                    Former la nouvelle génération de <span class="text-white font-medium">cinéastes, directeurs photo, monteurs et ingénieurs du son</span> au cœur du prestigieux Grand Théâtre National Doudou Ndiaye Rose à Dakar.
                </p>

                {{-- Groupe CTA Principal --}}
                <div class="flex flex-wrap items-center gap-4 mt-10">

                    <a
                        href="#formations"
                        class="group inline-flex items-center gap-3.5 px-8 py-4 rounded-full bg-[#F5B800] hover:bg-white text-black font-bold text-sm uppercase tracking-wider transition-all duration-300 shadow-[0_10px_30px_rgba(245,184,0,0.35)] hover:shadow-[0_15px_40px_rgba(255,255,255,0.4)]"
                    >
                        <span>Découvrir les formations</span>
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-black text-white group-hover:bg-[#F5B800] group-hover:text-black transition">
                            <x-lucide-arrow-up-right class="w-3.5 h-3.5" />
                        </span>
                    </a>

                    <a
                        href="{{ route('public.admissions.create') }}"
                        class="inline-flex items-center gap-2.5 px-7 py-4 rounded-full bg-white/[0.05] hover:bg-white/[0.12] border border-white/20 hover:border-white/40 text-white font-semibold text-sm transition-all duration-300 backdrop-blur-md"
                    >
                        <span>Déposer ma candidature</span>
                        <x-lucide-arrow-right class="w-4 h-4 text-white/60" />
                    </a>

                </div>

                {{-- Points de réassurance rapides --}}
                <div class="mt-8 flex flex-wrap items-center gap-5 text-xs text-white/50 font-medium">
                    <span class="flex items-center gap-1.5">
                        <x-lucide-check class="w-3.5 h-3.5 text-[#F5B800]" />
                        80% Pratique Studio
                    </span>
                    <span class="text-white/20">•</span>
                    <span class="flex items-center gap-1.5">
                        <x-lucide-check class="w-3.5 h-3.5 text-[#F5B800]" />
                        Matériel Cinéma 4K / 6K
                    </span>
                    <span class="text-white/20">•</span>
                    <span class="flex items-center gap-1.5">
                        <x-lucide-check class="w-3.5 h-3.5 text-[#F5B800]" />
                        Intervenants Reconnus
                    </span>
                </div>

            </div>


            {{-- DROITE : Composition des Objets 3D avec Parallaxe Interactive --}}
            <div
                class="relative h-[480px] sm:h-[540px] md:h-[600px] lg:h-[640px] w-full select-none"
                x-data="{
                    mouseX: 0,
                    mouseY: 0,
                    onMouseMove(e) {
                        const rect = $el.getBoundingClientRect();
                        const x = (e.clientX - (rect.left + rect.width / 2)) / (rect.width / 2);
                        const y = (e.clientY - (rect.top + rect.height / 2)) / (rect.height / 2);
                        this.mouseX = Math.max(-1, Math.min(1, x));
                        this.mouseY = Math.max(-1, Math.min(1, y));
                    },
                    onMouseLeave() {
                        this.mouseX = 0;
                        this.mouseY = 0;
                    }
                }"
                @mousemove="onMouseMove($event)"
                @mouseleave="onMouseLeave()"
            >

                {{-- Lueur centrale studio --}}
                <div
                    class="absolute left-[30%] top-[20%] w-80 h-80 rounded-full bg-[#F5B800]/20 blur-3xl pointer-events-none transition-transform duration-700 ease-out"
                    :style="'transform: translate3d(' + (mouseX * 18) + 'px, ' + (mouseY * 18) + 'px, 0)'"
                ></div>

                {{-- Faisceau lumineux du projecteur --}}
                <div
                    class="absolute -left-10 bottom-[20%] w-[420px] h-[200px] bg-gradient-to-tr from-[#F5B800]/25 via-[#F5B800]/8 to-transparent rounded-full blur-2xl pointer-events-none animate-hero-lightbeam"
                ></div>


                {{-- 01 • CLAP DE TOURNAGE --}}
                <div
                    class="absolute left-[4%] top-[6%] w-[115px] sm:w-[130px] md:w-[145px] z-20 cursor-pointer group transition-transform duration-500 ease-out"
                    :style="'transform: translate3d(' + (mouseX * -28) + 'px, ' + (mouseY * -24) + 'px, 0)'"
                >
                    <div class="animate-hero-clap transform group-hover:scale-110 group-hover:rotate-0 transition-transform duration-300">
                        <img
                            src="{{ asset('images/objects/clap.png') }}"
                            alt="Clap de cinéma EMSI"
                            class="w-full h-auto object-contain drop-shadow-[0_15px_30px_rgba(0,0,0,0.6)]"
                        >
                        {{-- Badge Scène au survol --}}
                        <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-mono uppercase tracking-wider bg-black/90 text-[#F5B800] border border-[#F5B800]/30 whitespace-nowrap shadow-xl">
                                Scène 01 • Prise 01
                            </span>
                        </div>
                    </div>
                </div>


                {{-- 02 • PROJECTEUR AUDIOVISUEL --}}
                <div
                    class="absolute left-[0%] bottom-[12%] w-[145px] sm:w-[165px] md:w-[185px] z-10 cursor-pointer group transition-transform duration-500 ease-out"
                    :style="'transform: translate3d(' + (mouseX * 22) + 'px, ' + (mouseY * 18) + 'px, 0)'"
                >
                    <div class="animate-hero-projector transform group-hover:scale-110 transition-transform duration-300">
                        <img
                            src="{{ asset('images/objects/projecteur.png') }}"
                            alt="Projecteur de studio audiovisuel"
                            class="w-full h-auto object-contain drop-shadow-[0_20px_40px_rgba(0,0,0,0.7)]"
                        >
                    </div>
                </div>


                {{-- 03 • CAMÉRA CINÉMA — OBJET PHARE --}}
                <div
                    class="absolute left-[24%] top-[16%] w-[250px] sm:w-[300px] md:w-[340px] lg:w-[370px] z-30 cursor-pointer group transition-transform duration-300 ease-out"
                    :style="'transform: translate3d(' + (mouseX * 32) + 'px, ' + (mouseY * 32) + 'px, 0) rotateX(' + (-mouseY * 10) + 'deg) rotateY(' + (mouseX * 10) + 'deg)'"
                >
                    {{-- Widget Écran Caméra (REC & Timecode) --}}
                    <div class="absolute -top-3 right-4 z-40 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-black/85 backdrop-blur-md border border-white/20 shadow-2xl">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                        <span class="text-[9px] font-mono font-bold uppercase tracking-widest text-red-400">REC</span>
                        <span class="text-white/30 text-[9px]">|</span>
                        <span class="text-[9px] font-mono text-white/90 font-semibold tracking-wider">4K 60FPS</span>
                    </div>

                    <div class="animate-hero-camera transform group-hover:scale-105 transition-transform duration-300">
                        <img
                            src="{{ asset('images/objects/camera.png') }}"
                            alt="Caméra professionnelle cinéma"
                            class="w-full h-auto object-contain filter drop-shadow-[0_25px_50px_rgba(0,0,0,0.8)]"
                        >
                    </div>
                </div>


                {{-- 04 • MICROPHONE STUDIO & ONDES ACOUSTIQUES --}}
                <div
                    class="absolute right-[4%] top-[4%] w-[130px] sm:w-[150px] md:w-[170px] z-20 cursor-pointer group transition-transform duration-500 ease-out"
                    :style="'transform: translate3d(' + (mouseX * -32) + 'px, ' + (mouseY * 26) + 'px, 0)'"
                >
                    {{-- Cercles acoustiques animés --}}
                    <div class="absolute top-6 left-6 w-8 h-8 rounded-full border border-[#F5B800]/40 animate-hero-sonic-ring pointer-events-none"></div>

                    <div class="animate-hero-mic transform group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-300">
                        <img
                            src="{{ asset('images/objects/microphone.png') }}"
                            alt="Microphone de studio HF"
                            class="w-full h-auto object-contain drop-shadow-[0_15px_35px_rgba(0,0,0,0.6)]"
                        >
                        {{-- Tooltip au survol --}}
                        <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-mono uppercase tracking-wider bg-black/90 text-white border border-white/20 whitespace-nowrap shadow-lg">
                                Prise de Son HF
                            </span>
                        </div>
                    </div>
                </div>


                {{-- 05 • CONSOLE DE MIXAGE AUDIO --}}
                <div
                    class="absolute right-[10%] bottom-[12%] w-[185px] sm:w-[215px] md:w-[250px] z-20 cursor-pointer group transition-transform duration-500 ease-out"
                    :style="'transform: translate3d(' + (mouseX * 28) + 'px, ' + (mouseY * -22) + 'px, 0)'"
                >
                    <div class="animate-hero-console transform group-hover:scale-105 transition-transform duration-300">
                        <img
                            src="{{ asset('images/objects/console.png') }}"
                            alt="Console de mixage et post-production"
                            class="w-full h-auto object-contain drop-shadow-[0_30px_60px_rgba(0,0,0,0.75)]"
                        >
                        {{-- Barres de VU-mètres LED animées --}}
                        <div class="absolute top-5 right-6 flex items-end gap-1 h-3 pointer-events-none opacity-85 group-hover:opacity-100">
                            <span class="w-1 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                            <span class="w-1 h-3 bg-emerald-400 rounded-full animate-pulse" style="animation-delay: 150ms"></span>
                            <span class="w-1 h-2.5 bg-amber-400 rounded-full animate-pulse" style="animation-delay: 300ms"></span>
                            <span class="w-1 h-1.5 bg-red-400 rounded-full animate-pulse" style="animation-delay: 450ms"></span>
                        </div>
                    </div>
                </div>


                {{-- 06 • CASQUE DE MONITORING AUDIO --}}
                <div
                    class="absolute right-[0%] bottom-[0%] w-[145px] sm:w-[165px] md:w-[195px] z-30 cursor-pointer group transition-transform duration-500 ease-out"
                    :style="'transform: translate3d(' + (mouseX * -38) + 'px, ' + (mouseY * -30) + 'px, 0)'"
                >
                    <div class="animate-hero-casque transform group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                        <img
                            src="{{ asset('images/objects/casque.png') }}"
                            alt="Casque audio de monitoring studio"
                            class="w-full h-auto object-contain drop-shadow-[0_20px_40px_rgba(0,0,0,0.7)]"
                        >
                    </div>
                </div>


                {{-- Points lumineux de plateau studio --}}
                <span class="absolute left-[24%] top-[15%] w-2 h-2 rounded-full bg-[#F5B800]/60 animate-ping"></span>
                <span class="absolute right-[28%] top-[30%] w-1.5 h-1.5 rounded-full bg-white/50 animate-pulse"></span>
                <span class="absolute left-[38%] bottom-[10%] w-1.5 h-1.5 rounded-full bg-[#F5B800]/50 animate-pulse"></span>

            </div>

        </div>


        {{-- =================================================
             4. BAS DU HERO / RÉASSURANCE & SCROLL
        ================================================== --}}
        <div class="border-t border-white/[0.08] pt-6 flex flex-wrap items-center justify-between gap-6">

            <a
                href="#ecole"
                class="inline-flex items-center gap-2.5 text-[10px] uppercase tracking-[0.25em] text-[#F5B800] hover:text-white font-semibold transition group"
            >
                <span>Découvrir l'école</span>
                <span class="flex items-center justify-center w-7 h-7 rounded-full border border-[#F5B800]/40 group-hover:border-white group-hover:bg-white group-hover:text-black transition">
                    <x-lucide-arrow-down class="w-3.5 h-3.5 group-hover:translate-y-0.5 transition-transform" />
                </span>
            </a>

        </div>

    </div>

</section>


{{-- =========================================================
     L'ÉCOLE & LE CAMPUS (INSTITUTION & VISION)
========================================================= --}}

<section
    id="ecole"
    class="relative py-28 lg:py-36 bg-[#08080a] text-white overflow-hidden border-b border-white/10"
>

    {{-- Lueurs d'ambiance cinématiques & mesh --}}
    <div class="absolute -top-40 left-1/4 w-[650px] h-[650px] bg-[#320080]/25 rounded-full blur-[150px] pointer-events-none"></div>
    <div class="absolute -bottom-40 right-10 w-[550px] h-[550px] bg-[#F5B800]/12 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[radial-gradient(circle_at_center,rgba(245,184,0,0.03)_0%,transparent_70%)] pointer-events-none"></div>

    <div class="max-w-[1500px] mx-auto px-6 sm:px-8 lg:px-16 relative z-10">

        {{-- En-tête de section --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-10 pb-16 border-b border-white/[0.08]">

            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/25 text-[#F5B800] text-xs font-bold uppercase tracking-[0.25em] backdrop-blur-md mb-5">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#F5B800] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#F5B800]"></span>
                    </span>
                    <span>L'Institution & La Vision</span>
                </div>

                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-normal tracking-tight leading-[1.08] text-white">
                    Former des regards, <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#F5B800] via-[#FCE38A] to-white italic font-light">maîtriser le geste créatif.</span>
                </h2>
            </div>

            <div class="max-w-lg">
                <p class="text-sm sm:text-base leading-relaxed text-white/70 font-light">
                    Implantée au cœur du <span class="text-white font-medium">Grand Théâtre National Doudou Ndiaye Rose</span>, l'EMSI est l'institution d'excellence dédiée à l'apprentissage intensif et professionnalisant des métiers du cinéma, de la télévision, du son et de la création digitale à Dakar.
                </p>
                <div class="mt-6 flex flex-wrap items-center gap-4">
                    <a
                        href="{{ route('public.about') }}"
                        class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#F5B800] hover:text-white transition group"
                    >
                        <span>Découvrir l'histoire & le campus</span>
                        <x-lucide-arrow-right class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                    </a>
                </div>
            </div>

        </div>


        {{-- Grille Bento : Image du Campus + Piliers Fondamentaux --}}
        <div class="mt-16 grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">

            {{-- Grande Carte Campus / Grand Théâtre --}}
            <div class="lg:col-span-7 relative rounded-3xl overflow-hidden bg-gradient-to-b from-[#141418] to-[#0A0A0D] border border-white/10 hover:border-[#F5B800]/40 transition-all duration-500 group min-h-[480px] lg:min-h-[520px] flex flex-col justify-between p-8 sm:p-10 shadow-2xl">

                {{-- Image de fond avec effet cinématique --}}
                <div class="absolute inset-0 z-0">
                    <img
                        src="{{ asset('images/grand-theatre.jpg') }}"
                        alt="Grand Théâtre National Doudou Ndiaye Rose - Dakar"
                        class="w-full h-full object-cover transition-transform duration-1000 ease-out group-hover:scale-105 opacity-40 group-hover:opacity-55 filter brightness-95"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-[#08080a] via-[#08080a]/60 to-black/30"></div>
                </div>

                {{-- Haut de la carte : Badges d'infrastructure --}}
                <div class="relative z-10 flex flex-wrap items-center justify-between gap-3">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-black/70 backdrop-blur-md text-[#F5B800] border border-[#F5B800]/30 shadow-lg">
                        <x-lucide-map-pin class="w-3.5 h-3.5 text-[#F5B800]" />
                        Campus d'Exception • Dakar
                    </span>

                    <span class="px-3 py-1 rounded-full text-[10px] font-mono font-semibold uppercase tracking-widest bg-white/10 backdrop-blur-md text-white/80 border border-white/15">
                        Monument National
                    </span>
                </div>

                {{-- Badges flottants d'équipements --}}
                <div class="relative z-10 hidden sm:flex flex-wrap gap-2 my-auto pt-10">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-medium bg-black/60 backdrop-blur-md text-white/80 border border-white/10">
                        <x-lucide-video class="w-3 h-3 text-[#F5B800]" />
                        Plateaux 4K/6K
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-medium bg-black/60 backdrop-blur-md text-white/80 border border-white/10">
                        <x-lucide-sliders class="w-3 h-3 text-[#F5B800]" />
                        Régies DaVinci
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-medium bg-black/60 backdrop-blur-md text-white/80 border border-white/10">
                        <x-lucide-mic class="w-3 h-3 text-[#F5B800]" />
                        Cabines Prise de Son
                    </span>
                </div>

                {{-- Bas de la carte --}}
                <div class="relative z-10 max-w-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="h-px w-6 bg-[#F5B800]"></span>
                        <p class="text-xs uppercase tracking-[0.2em] text-[#F5B800] font-bold">Un Monument Artistique & Culturel</p>
                    </div>
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-serif text-white leading-tight">
                        Grand Théâtre National Doudou Ndiaye Rose
                    </h3>
                    <p class="mt-3 text-xs sm:text-sm text-white/75 leading-relaxed font-light">
                        Un écrin architectural majestueux offrant à nos étudiants des plateaux de tournage grandeur nature, des régies de captation multicaméras et des espaces de post-production haut de gamme pour une immersion totale dans les conditions réelles de l'industrie.
                    </p>
                    <div class="mt-5 pt-4 border-t border-white/10 flex items-center justify-between">
                        <a
                            href="{{ route('public.about') }}"
                            class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-white hover:text-[#F5B800] transition"
                        >
                            <span>Visiter le campus & les installations</span>
                            <x-lucide-arrow-up-right class="w-4 h-4 text-[#F5B800]" />
                        </a>
                    </div>
                </div>

            </div>


            {{-- Piliers Pédagogiques avec vraies images illustratives --}}
            <div class="lg:col-span-5 flex flex-col gap-5 justify-between">

                {{-- Pilier 1 : Caméras & Tournage --}}
                <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-[#16161a] to-[#0f0f13] border border-white/[0.08] hover:border-[#F5B800]/50 transition-all duration-300 group hover:-translate-y-1 shadow-lg hover:shadow-[0_10px_30px_rgba(245,184,0,0.12)] flex flex-col sm:flex-row gap-5 items-center">
                    {{-- Photo réelle --}}
                    <div class="w-full sm:w-44 md:w-48 h-40 sm:h-32 rounded-2xl overflow-hidden shrink-0 relative border border-white/10 shadow-md">
                        <img
                            src="{{ asset('images/institution-1.jpg') }}"
                            alt="Plateaux et formation cinéma EMSI"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-full text-[9px] font-mono font-bold uppercase tracking-wider bg-black/80 backdrop-blur-md text-[#F5B800] border border-[#F5B800]/30">
                            Plateaux 4K/6K
                        </span>
                    </div>

                    {{-- Contenu texte --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-[10px] font-mono uppercase tracking-widest text-[#F5B800] font-bold">Plateaux Réels</span>
                            <span class="text-[10px] text-white/40 font-mono">Pratique</span>
                        </div>
                        <h4 class="text-base sm:text-lg font-bold text-white group-hover:text-[#F5B800] transition-colors leading-snug">
                            Plateaux & Caméras Cinéma
                        </h4>
                        <p class="mt-1.5 text-xs text-white/60 leading-relaxed font-light line-clamp-2 sm:line-clamp-3">
                            Manipulation quotidienne de caméras professionnelles, objectifs cinéma, éclairages studio LED et machineries de tournage.
                        </p>
                    </div>
                </div>

                {{-- Pilier 2 : Post-Production & Son --}}
                <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-[#16161a] to-[#0f0f13] border border-white/[0.08] hover:border-[#F5B800]/50 transition-all duration-300 group hover:-translate-y-1 shadow-lg hover:shadow-[0_10px_30px_rgba(245,184,0,0.12)] flex flex-col sm:flex-row gap-5 items-center">
                    {{-- Photo réelle --}}
                    <div class="w-full sm:w-44 md:w-48 h-40 sm:h-32 rounded-2xl overflow-hidden shrink-0 relative border border-white/10 shadow-md">
                        <img
                            src="{{ asset('images/institution-2.jpg') }}"
                            alt="Régie de post-production et étalonnage"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-full text-[9px] font-mono font-bold uppercase tracking-wider bg-black/80 backdrop-blur-md text-[#F5B800] border border-[#F5B800]/30">
                            Montage & Son
                        </span>
                    </div>

                    {{-- Contenu texte --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-[10px] font-mono uppercase tracking-widest text-[#F5B800] font-bold">Post-Production</span>
                            <span class="text-[10px] text-white/40 font-mono">DaVinci • Avid</span>
                        </div>
                        <h4 class="text-base sm:text-lg font-bold text-white group-hover:text-[#F5B800] transition-colors leading-snug">
                            Montage, Étalonnage & Son
                        </h4>
                        <p class="mt-1.5 text-xs text-white/60 leading-relaxed font-light line-clamp-2 sm:line-clamp-3">
                            Stations de montage professionnelles hautes performances (DaVinci Resolve, Premiere Pro) et cabines acoustiques de mixage.
                        </p>
                    </div>
                </div>

                {{-- Pilier 3 : Mentorat & Insertion --}}
                <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-[#16161a] to-[#0f0f13] border border-white/[0.08] hover:border-[#F5B800]/50 transition-all duration-300 group hover:-translate-y-1 shadow-lg hover:shadow-[0_10px_30px_rgba(245,184,0,0.12)] flex flex-col sm:flex-row gap-5 items-center">
                    {{-- Photo réelle --}}
                    <div class="w-full sm:w-44 md:w-48 h-40 sm:h-32 rounded-2xl overflow-hidden shrink-0 relative border border-white/10 shadow-md">
                        <img
                            src="{{ asset('images/institution-3.jpg') }}"
                            alt="Mentorat et masterclass cinéma au Grand Théâtre"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-full text-[9px] font-mono font-bold uppercase tracking-wider bg-black/80 backdrop-blur-md text-[#F5B800] border border-[#F5B800]/30">
                            Mentorat Pro
                        </span>
                    </div>

                    {{-- Contenu texte --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-[10px] font-mono uppercase tracking-widest text-[#F5B800] font-bold">Mentorat Pro</span>
                            <span class="text-[10px] text-white/40 font-mono">Dakar • Réseau</span>
                        </div>
                        <h4 class="text-base sm:text-lg font-bold text-white group-hover:text-[#F5B800] transition-colors leading-snug">
                            Mentorat & Insertion Métier
                        </h4>
                        <p class="mt-1.5 text-xs text-white/60 leading-relaxed font-light line-clamp-2 sm:line-clamp-3">
                            Formateurs et intervenants 100% actifs sur les plateaux de cinéma et séries pour une insertion directe dans l'industrie.
                        </p>
                    </div>
                </div>

            </div>

        </div>


        {{-- Bandeau Métriques Clés --}}
        <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-5 sm:gap-6 pt-12 border-t border-white/[0.08]">

            <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-b from-white/[0.04] to-white/[0.01] border border-white/[0.08] hover:border-[#F5B800]/40 backdrop-blur-md text-center group transition-all duration-300 hover:-translate-y-1">
                <div class="w-10 h-10 mx-auto rounded-xl bg-[#F5B800]/10 flex items-center justify-center text-[#F5B800] mb-3 group-hover:scale-110 transition-transform">
                    <x-lucide-clapperboard class="w-5 h-5" />
                </div>
                <p class="text-3xl sm:text-4xl lg:text-5xl font-serif font-bold text-[#F5B800]">80%</p>
                <p class="text-xs uppercase tracking-wider text-white/70 mt-2 font-semibold">Pratique & Plateaux</p>
                <p class="text-[11px] text-white/40 mt-1">Apprentissage par le geste</p>
            </div>

            <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-b from-white/[0.04] to-white/[0.01] border border-white/[0.08] hover:border-[#F5B800]/40 backdrop-blur-md text-center group transition-all duration-300 hover:-translate-y-1">
                <div class="w-10 h-10 mx-auto rounded-xl bg-[#F5B800]/10 flex items-center justify-center text-[#F5B800] mb-3 group-hover:scale-110 transition-transform">
                    <x-lucide-video class="w-5 h-5" />
                </div>
                <p class="text-3xl sm:text-4xl lg:text-5xl font-serif font-bold text-white">4K / 6K</p>
                <p class="text-xs uppercase tracking-wider text-white/70 mt-2 font-semibold">Caméras & Régies</p>
                <p class="text-[11px] text-white/40 mt-1">Équipements cinéma de pointe</p>
            </div>

            <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-b from-white/[0.04] to-white/[0.01] border border-white/[0.08] hover:border-[#F5B800]/40 backdrop-blur-md text-center group transition-all duration-300 hover:-translate-y-1">
                <div class="w-10 h-10 mx-auto rounded-xl bg-[#F5B800]/10 flex items-center justify-center text-[#F5B800] mb-3 group-hover:scale-110 transition-transform">
                    <x-lucide-users class="w-5 h-5" />
                </div>
                <p class="text-3xl sm:text-4xl lg:text-5xl font-serif font-bold text-[#F5B800]">100%</p>
                <p class="text-xs uppercase tracking-wider text-white/70 mt-2 font-semibold">Intervenants Pros</p>
                <p class="text-[11px] text-white/40 mt-1">Praticiens & réalisateurs actifs</p>
            </div>

            <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-b from-white/[0.04] to-white/[0.01] border border-white/[0.08] hover:border-[#F5B800]/40 backdrop-blur-md text-center group transition-all duration-300 hover:-translate-y-1">
                <div class="w-10 h-10 mx-auto rounded-xl bg-[#F5B800]/10 flex items-center justify-center text-[#F5B800] mb-3 group-hover:scale-110 transition-transform">
                    <x-lucide-landmark class="w-5 h-5" />
                </div>
                <p class="text-3xl sm:text-4xl lg:text-5xl font-serif font-bold text-white">Dakar</p>
                <p class="text-xs uppercase tracking-wider text-white/70 mt-2 font-semibold">Grand Théâtre</p>
                <p class="text-[11px] text-white/40 mt-1">Campus artistique prestigieux</p>
            </div>

        </div>

    </div>

</section>



{{-- =========================================================
     FORMATIONS — CATALOGUE & CURSUS MÉTIERS
========================================================= --}}

<section
    id="formations"
    class="relative py-24 sm:py-28 lg:py-36 bg-[#08080a] text-white overflow-hidden border-b border-white/10"
>

    {{-- Lueurs d'ambiance cinématiques & mesh --}}
    <div class="absolute -top-40 right-1/4 w-[600px] h-[600px] bg-[#F5B800]/10 rounded-full blur-[160px] pointer-events-none"></div>
    <div class="absolute -bottom-40 left-10 w-[600px] h-[600px] bg-[#320080]/20 rounded-full blur-[150px] pointer-events-none"></div>

    <div class="max-w-[1500px] mx-auto px-6 sm:px-8 lg:px-16 relative z-10">

        {{-- =================================================
             EN-TÊTE DE SECTION
        ================================================== --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-10 pb-14 border-b border-white/[0.08]">

            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/25 text-[#F5B800] text-xs font-bold uppercase tracking-[0.25em] backdrop-blur-md mb-5">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#F5B800] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#F5B800]"></span>
                    </span>
                    <span>Catalogue & Cursus Métiers</span>
                </div>

                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-normal tracking-tight leading-[1.08] text-white">
                    Nos filières d'excellence, <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#F5B800] via-[#FCE38A] to-white italic font-light">du plateau de tournage à la régie.</span>
                </h2>
            </div>

            <div class="max-w-md">
                <p class="text-sm sm:text-base leading-relaxed text-white/70 font-light">
                    Des formations complètes et professionnalisantes combinant <span class="text-white font-medium">80% de pratique studio</span>, manipulation quotidienne d'équipements cinéma 4K/6K et encadrement par des experts actifs du secteur.
                </p>
                <div class="mt-5 flex items-center gap-6 text-xs uppercase tracking-wider text-[#F5B800] font-semibold">
                    <span class="flex items-center gap-1.5">
                        <x-lucide-check-circle-2 class="w-4 h-4" />
                        Diplômes & Certifications
                    </span>
                    <span class="text-white/20">•</span>
                    <span class="flex items-center gap-1.5 text-white/70">
                        <x-lucide-check-circle-2 class="w-4 h-4 text-[#F5B800]" />
                        Dakar — Grand Théâtre
                    </span>
                </div>
            </div>

        </div>


        {{-- =================================================
             GRILLE DES FORMATIONS (CRÉÉES DANS L'ADMIN)
        ================================================== --}}
        @if(isset($courses) && $courses->isNotEmpty())

            <div class="mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-7 items-stretch">
                @foreach($courses->take(4) as $index => $course)
                    <x-public.formation-item
                        :course="$course"
                        :index="$index"
                    />
                @endforeach
            </div>

        @else

            {{-- État vide lorsque aucune formation n'a encore été créée dans l'admin --}}
            <div class="mt-14 p-12 sm:p-16 rounded-3xl bg-white/[0.02] border border-white/10 text-center flex flex-col items-center justify-center max-w-2xl mx-auto shadow-2xl">
                <div class="w-16 h-16 rounded-2xl bg-[#F5B800]/10 border border-[#F5B800]/20 flex items-center justify-center text-[#F5B800] mb-5">
                    <x-lucide-graduation-cap class="w-8 h-8" />
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-white">Formations en cours de publication</h3>
                <p class="mt-3 text-sm text-white/60 leading-relaxed max-w-lg font-light">
                    Le catalogue des formations est actuellement en cours de mise à jour pour la prochaine rentrée académique. Vous pouvez dès à présent déposer votre dossier d'admission ou contacter l'administration.
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                    <a
                        href="{{ route('public.admissions.create') }}"
                        class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-full bg-[#F5B800] hover:bg-white text-black font-bold text-xs uppercase tracking-wider transition shadow-[0_4px_20px_rgba(245,184,0,0.3)]"
                    >
                        <span>Déposer ma candidature</span>
                        <x-lucide-arrow-up-right class="w-4 h-4" />
                    </a>
                    <a
                        href="{{ route('public.about') }}"
                        class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-white/5 hover:bg-white/10 text-white text-xs font-semibold uppercase tracking-wider transition border border-white/10"
                    >
                        <span>En savoir plus sur l'école</span>
                    </a>
                </div>
            </div>

        @endif


        {{-- =================================================
             PIED DE SECTION / CALL TO ACTION
        ================================================== --}}
        <div class="mt-14 pt-10 border-t border-white/[0.08] flex flex-col sm:flex-row items-center justify-between gap-6">

            <div class="flex items-center gap-3 text-xs uppercase tracking-wider text-white/50 font-medium">
                <span class="w-2 h-2 rounded-full bg-[#F5B800] animate-ping"></span>
                <span>Inscriptions ouvertes pour la prochaine rentrée académique</span>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <a
                    href="{{ route('public.courses.index') }}"
                    class="group inline-flex items-center gap-3 px-6 py-3.5 rounded-full bg-white/10 hover:bg-white hover:text-black text-white text-xs font-bold uppercase tracking-wider transition-all duration-300 border border-white/15"
                >
                    <span>Voir toutes les formations</span>
                    <x-lucide-arrow-right class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                </a>

                <a
                    href="{{ route('public.admissions.create') }}"
                    class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-full bg-[#F5B800] hover:bg-white text-black font-bold text-xs uppercase tracking-wider transition-all duration-300 shadow-[0_4px_20px_rgba(245,184,0,0.3)]"
                >
                    <span>Déposer ma candidature</span>
                    <x-lucide-arrow-up-right class="w-4 h-4" />
                </a>
            </div>

        </div>

    </div>

</section>



{{-- =========================================================
     GALERIE
========================================================= --}}

@php
    $galleryItemsJson = $galleries->map(function ($item) {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'description' => $item->description,
            'type' => $item->type,
            'file_path' => $item->file_path ? asset('storage/' . $item->file_path) : null,
            'youtube_url' => $item->youtube_url,
            'youtube_embed' => $item->youtube_embed_url,
            'image_url' => $item->image_url,
        ];
    })->values();

    $photoCount = $galleries->where('type', 'image')->count();
    $videoCount = $galleries->where('type', 'video')->count();
@endphp

<section
    id="galerie"
    class="relative py-24 lg:py-32 bg-[#0a0a0a] text-white border-y border-white/10 overflow-hidden"
    x-data="{
        activeFilter: 'all',
        activeModal: null,
        items: {{ Js::from($galleryItemsJson) }},
        openModal(index) {
            this.activeModal = index;
            document.body.style.overflow = 'hidden';
        },
        closeModal() {
            this.activeModal = null;
            document.body.style.overflow = '';
        },
        nextModal() {
            if (this.activeModal !== null && this.items.length > 0) {
                this.activeModal = (this.activeModal + 1) % this.items.length;
            }
        },
        prevModal() {
            if (this.activeModal !== null && this.items.length > 0) {
                this.activeModal = (this.activeModal - 1 + this.items.length) % this.items.length;
            }
        }
    }"
    @keydown.escape.window="closeModal()"
    @keydown.arrow-right.window="if(activeModal !== null) nextModal()"
    @keydown.arrow-left.window="if(activeModal !== null) prevModal()"
>

    {{-- Arrière-plan cinématique avec lueurs d'ambiance --}}
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-[#320080]/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-[#F5B800]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="public-container relative z-10">

        {{-- En-tête de section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 pb-12 border-b border-white/10">

            <div>
                <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-[#F5B800] mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#F5B800] animate-pulse"></span>
                    Galerie & Réalisations
                </p>

                <h2 class="text-4xl md:text-5xl lg:text-6xl font-serif font-normal tracking-tight text-white">
                    Regards & Créations.
                </h2>
            </div>

            <p class="text-sm md:text-base text-white/50 max-w-md leading-relaxed">
                Plongez au cœur de l'école : tournages sur plateau, captations, ateliers de montage et vie des étudiants au Grand Théâtre National.
            </p>

        </div>


        {{-- Barre de Filtres Interactifs --}}
        @if($galleries->count())
        <div class="mt-10 flex flex-wrap items-center justify-between gap-4">

            <div class="inline-flex p-1.5 rounded-2xl bg-[#141414] border border-white/10">

                {{-- Tous --}}
                <button
                    type="button"
                    @click="activeFilter = 'all'"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold tracking-wider transition-all duration-300"
                    :class="activeFilter === 'all'
                        ? 'bg-[#F5B800] text-black shadow-[0_2px_12px_rgba(245,184,0,0.3)] font-bold'
                        : 'text-white/60 hover:text-white hover:bg-white/5'"
                >
                    <x-lucide-layout-grid class="w-3.5 h-3.5" />
                    <span>Tous</span>
                    <span
                        class="px-1.5 py-0.5 rounded-full text-[10px]"
                        :class="activeFilter === 'all' ? 'bg-black/20 text-black' : 'bg-white/10 text-white/60'"
                    >
                        {{ $galleries->count() }}
                    </span>
                </button>

                {{-- Photos --}}
                @if($photoCount > 0)
                <button
                    type="button"
                    @click="activeFilter = 'image'"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold tracking-wider transition-all duration-300"
                    :class="activeFilter === 'image'
                        ? 'bg-[#F5B800] text-black shadow-[0_2px_12px_rgba(245,184,0,0.3)] font-bold'
                        : 'text-white/60 hover:text-white hover:bg-white/5'"
                >
                    <x-lucide-camera class="w-3.5 h-3.5" />
                    <span>Photos</span>
                    <span
                        class="px-1.5 py-0.5 rounded-full text-[10px]"
                        :class="activeFilter === 'image' ? 'bg-black/20 text-black' : 'bg-white/10 text-white/60'"
                    >
                        {{ $photoCount }}
                    </span>
                </button>
                @endif

                {{-- Vidéos --}}
                @if($videoCount > 0)
                <button
                    type="button"
                    @click="activeFilter = 'video'"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold tracking-wider transition-all duration-300"
                    :class="activeFilter === 'video'
                        ? 'bg-[#F5B800] text-black shadow-[0_2px_12px_rgba(245,184,0,0.3)] font-bold'
                        : 'text-white/60 hover:text-white hover:bg-white/5'"
                >
                    <x-lucide-video class="w-3.5 h-3.5" />
                    <span>Vidéos</span>
                    <span
                        class="px-1.5 py-0.5 rounded-full text-[10px]"
                        :class="activeFilter === 'video' ? 'bg-black/20 text-black' : 'bg-white/10 text-white/60'"
                    >
                        {{ $videoCount }}
                    </span>
                </button>
                @endif

            </div>

            <div class="text-xs text-white/40 font-medium hidden sm:flex items-center gap-2">
                <x-lucide-sparkles class="w-3.5 h-3.5 text-[#F5B800]" />
                <span>Cliquez sur un média pour ouvrir le visualiseur</span>
            </div>

        </div>
        @endif


        {{-- Grille des Médias --}}
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

            @forelse($galleries as $index => $gallery)

                <x-public.gallery-item
                    :gallery="$gallery"
                    :index="$index"
                />

            @empty

                <div class="col-span-full py-16 text-center rounded-3xl border border-white/10 bg-[#121212] p-8">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-white/5 flex items-center justify-center text-white/30">
                        <x-lucide-image class="w-8 h-8" />
                    </div>
                    <h3 class="text-lg font-bold text-white mb-1">Galerie en préparation</h3>
                    <p class="text-sm text-white/40 max-w-sm mx-auto">
                        Les clichés des projets et reportages de nos étudiants seront publiés très prochainement.
                    </p>
                </div>

            @endforelse

        </div>

        {{-- Lien vers la page galerie complète --}}
        <div class="mt-12 text-center">
            <a
                href="{{ route('public.gallery.index') }}"
                class="inline-flex items-center gap-3 px-8 py-4 rounded-full bg-white/5 hover:bg-[#F5B800] text-white hover:text-black border border-white/15 hover:border-[#F5B800] text-xs font-bold uppercase tracking-wider transition-all duration-300 shadow-xl"
            >
                <span>Explorer toute la galerie</span>
                <x-lucide-arrow-right class="w-4 h-4" />
            </a>
        </div>

    </div>


    {{-- =========================================================
         MODAL LIGHTBOX FULLSCREEN (Alpine.js)
    ========================================================= --}}
    <div
        x-show="activeModal !== null"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 md:p-8 bg-black/95 backdrop-blur-2xl"
        @click.self="closeModal()"
    >

        {{-- Barre supérieure de contrôle (Compteur + Bouton fermer) --}}
        <div class="absolute top-6 left-6 right-6 flex items-center justify-between z-30 pointer-events-none">

            <div class="pointer-events-auto inline-flex items-center gap-3 px-4 py-2 rounded-full bg-black/60 backdrop-blur-md border border-white/15 text-xs text-white/80">
                <span class="w-2 h-2 rounded-full bg-[#F5B800]"></span>
                <span x-text="items[activeModal]?.type === 'video' ? 'Vidéo' : 'Photographie'" class="font-semibold uppercase tracking-wider text-[10px]"></span>
                <span class="text-white/30">|</span>
                <span x-text="(activeModal !== null ? activeModal + 1 : 0) + ' / ' + items.length" class="font-mono text-white/60"></span>
            </div>

            <button
                type="button"
                @click="closeModal()"
                class="pointer-events-auto p-3 rounded-full bg-white/10 hover:bg-[#F5B800] text-white hover:text-black border border-white/20 hover:border-[#F5B800] transition-all duration-200 shadow-2xl focus:outline-none"
                aria-label="Fermer"
            >
                <x-lucide-x class="w-5 h-5" />
            </button>

        </div>


        {{-- Flèche Précédent --}}
        <button
            type="button"
            x-show="items.length > 1"
            @click.stop="prevModal()"
            class="absolute left-4 md:left-8 z-20 p-3.5 rounded-full bg-black/50 hover:bg-[#F5B800] text-white hover:text-black border border-white/20 hover:border-[#F5B800] backdrop-blur-md transition-all duration-200 shadow-2xl focus:outline-none"
            aria-label="Média précédent"
        >
            <x-lucide-chevron-left class="w-6 h-6" />
        </button>


        {{-- Contenu du Média Actif --}}
        <div
            x-show="activeModal !== null"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="relative max-w-5xl w-full max-h-[85vh] flex flex-col items-center justify-center z-10"
        >

            {{-- Si Vidéo --}}
            <template x-if="items[activeModal]?.type === 'video' && items[activeModal]?.youtube_embed">
                <div class="w-full aspect-video rounded-2xl overflow-hidden shadow-2xl bg-black border border-white/10">
                    <iframe
                        :src="items[activeModal]?.youtube_embed"
                        class="w-full h-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                    ></iframe>
                </div>
            </template>

            {{-- Si Image --}}
            <template x-if="items[activeModal]?.type === 'image' && items[activeModal]?.file_path">
                <div class="relative max-h-[70vh] rounded-2xl overflow-hidden shadow-2xl border border-white/10 bg-black flex items-center justify-center">
                    <img
                        :src="items[activeModal]?.file_path"
                        :alt="items[activeModal]?.title"
                        class="max-h-[70vh] w-auto object-contain rounded-2xl"
                    >
                </div>
            </template>

            {{-- Légende & Description sous le média --}}
            <div class="mt-4 w-full text-center px-4">
                <h3
                    x-text="items[activeModal]?.title"
                    class="text-lg md:text-xl font-bold text-white text-shadow"
                ></h3>
                <p
                    x-show="items[activeModal]?.description"
                    x-text="items[activeModal]?.description"
                    class="mt-1 text-xs md:text-sm text-white/60 max-w-2xl mx-auto leading-relaxed"
                ></p>
            </div>

        </div>


        {{-- Flèche Suivant --}}
        <button
            type="button"
            x-show="items.length > 1"
            @click.stop="nextModal()"
            class="absolute right-4 md:right-8 z-20 p-3.5 rounded-full bg-black/50 hover:bg-[#F5B800] text-white hover:text-black border border-white/20 hover:border-[#F5B800] backdrop-blur-md transition-all duration-200 shadow-2xl focus:outline-none"
            aria-label="Média suivant"
        >
            <x-lucide-chevron-right class="w-6 h-6" />
        </button>

    </div>

</section>




{{-- =========================================================
     ACTUALITÉS — JOURNAL DE L'ÉCOLE
========================================================= --}}

<section
    id="actualites"
    class="relative py-24 lg:py-32 bg-[#0c0c0e] text-white overflow-hidden border-t border-white/10"
>

    {{-- Halos d'ambiance --}}
    <div class="absolute top-1/4 right-1/4 w-[500px] h-[500px] bg-[#320080]/20 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="absolute bottom-10 left-10 w-[450px] h-[450px] bg-[#F5B800]/8 rounded-full blur-[130px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10">

        {{-- En-tête de section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-12 border-b border-white/10">
            <div>
                <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.25em] text-[#F5B800] mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#F5B800] animate-pulse"></span>
                    Le Journal de l'École
                </p>

                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal tracking-tight text-white leading-tight">
                    Tournages, masterclasses & créations.
                </h2>

                <p class="mt-4 text-sm sm:text-base text-white/60 max-w-2xl font-light leading-relaxed">
                    Découvrez les coulisses des productions étudiantes, les rencontres professionnelles exclusives et la vie académique au Grand Théâtre National de Dakar.
                </p>
            </div>

            <div class="shrink-0">
                <a
                    href="{{ route('public.news.index') }}"
                    class="inline-flex items-center gap-2.5 px-6 py-3 rounded-full bg-white/5 hover:bg-[#F5B800] text-white hover:text-black border border-white/15 hover:border-[#F5B800] text-xs font-bold uppercase tracking-wider transition-all duration-300 shadow-lg group"
                >
                    <span>Tout le journal</span>
                    <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </a>
            </div>
        </div>


        {{-- Grille éditoriale des articles --}}
        <div class="mt-12">

            @if($news->count() > 0)

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    {{-- ARTICLE PRINCIPAL (À LA UNE) - 7 colonnes --}}
                    @php
                        $featuredArticle = $news->first();
                        $secondaryArticles = $news->slice(1);
                    @endphp

                    <div class="{{ $secondaryArticles->count() > 0 ? 'lg:col-span-7' : 'lg:col-span-12' }}">
                        <article class="h-full flex flex-col justify-between rounded-3xl overflow-hidden bg-gradient-to-b from-white/[0.06] to-white/[0.02] border border-white/10 hover:border-[#F5B800]/50 shadow-[0_20px_50px_rgba(0,0,0,0.5)] transition-all duration-300 group">

                            {{-- Image de l'article à la une --}}
                            <div class="relative aspect-[16/10] overflow-hidden bg-black/40">
                                @if($featuredArticle->image)
                                    <img
                                        src="{{ asset('storage/' . $featuredArticle->image) }}"
                                        alt="{{ $featuredArticle->title }}"
                                        loading="lazy"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                    >
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-[#1a0045] to-[#0c0c0e] flex items-center justify-center text-white/20">
                                        <x-lucide-newspaper class="w-20 h-20 text-[#F5B800]/40" />
                                    </div>
                                @endif

                                {{-- Badges flottants --}}
                                <div class="absolute top-4 left-4 flex items-center gap-2">
                                    <span class="px-3.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-[#F5B800] text-black shadow-lg">
                                        À la Une
                                    </span>
                                </div>

                                {{-- Date flottante --}}
                                <div class="absolute bottom-4 left-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/70 backdrop-blur-md border border-white/15 text-xs text-white/90">
                                        <x-lucide-calendar class="w-3.5 h-3.5 text-[#F5B800]" />
                                        <time datetime="{{ $featuredArticle->published_at?->toDateString() ?? $featuredArticle->created_at->toDateString() }}">
                                            {{ $featuredArticle->published_at ? $featuredArticle->published_at->translatedFormat('d F Y') : $featuredArticle->created_at->translatedFormat('d F Y') }}
                                        </time>
                                    </span>
                                </div>
                            </div>

                            {{-- Contenu de l'article à la une --}}
                            <div class="p-7 sm:p-9 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-2xl sm:text-3xl font-serif font-normal text-white group-hover:text-[#F5B800] transition-colors leading-snug">
                                        <a href="{{ route('public.news.show', $featuredArticle->slug) }}">
                                            {{ $featuredArticle->title }}
                                        </a>
                                    </h3>

                                    @if($featuredArticle->excerpt)
                                        <p class="mt-4 text-sm sm:text-base text-white/60 line-clamp-3 font-light leading-relaxed">
                                            {{ $featuredArticle->excerpt }}
                                        </p>
                                    @elseif($featuredArticle->content)
                                        <p class="mt-4 text-sm sm:text-base text-white/60 line-clamp-3 font-light leading-relaxed">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($featuredArticle->content), 180) }}
                                        </p>
                                    @endif
                                </div>

                                <div class="mt-8 pt-6 border-t border-white/10 flex items-center justify-between">
                                    <a
                                        href="{{ route('public.news.show', $featuredArticle->slug) }}"
                                        class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#F5B800] group-hover:text-white transition-colors"
                                    >
                                        <span>Lire l'article complet</span>
                                        <x-lucide-arrow-up-right class="w-4 h-4 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
                                    </a>

                                    <span class="text-xs text-white/40">
                                        Grand Théâtre National
                                    </span>
                                </div>
                            </div>

                        </article>
                    </div>


                    {{-- ARTICLES SECONDAIRES - 5 colonnes --}}
                    @if($secondaryArticles->count() > 0)
                        <div class="lg:col-span-5 flex flex-col gap-6">

                            @foreach($secondaryArticles as $article)
                                <article class="flex-1 flex flex-col sm:flex-row lg:flex-col justify-between rounded-3xl overflow-hidden bg-gradient-to-b from-white/[0.05] to-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 shadow-lg transition-all duration-300 group">

                                    {{-- Image --}}
                                    @if($article->image)
                                        <div class="relative sm:w-2/5 lg:w-full aspect-[16/9] overflow-hidden bg-black/40 shrink-0">
                                            <img
                                                src="{{ asset('storage/' . $article->image) }}"
                                                alt="{{ $article->title }}"
                                                loading="lazy"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                            >
                                            <div class="absolute bottom-3 left-3">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-black/70 backdrop-blur-md border border-white/15 text-[11px] text-white/90">
                                                    <x-lucide-calendar class="w-3 h-3 text-[#F5B800]" />
                                                    {{ $article->published_at ? $article->published_at->translatedFormat('d M Y') : $article->created_at->translatedFormat('d M Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Contenu --}}
                                    <div class="p-6 flex-1 flex flex-col justify-between">
                                        <div>
                                            @if(!$article->image)
                                                <div class="flex items-center gap-2 text-xs text-white/50 mb-2 font-mono">
                                                    <x-lucide-calendar class="w-3 h-3 text-[#F5B800]" />
                                                    <span>{{ $article->published_at ? $article->published_at->translatedFormat('d F Y') : $article->created_at->translatedFormat('d F Y') }}</span>
                                                </div>
                                            @endif

                                            <h4 class="text-lg sm:text-xl font-serif font-medium text-white group-hover:text-[#F5B800] transition-colors leading-snug line-clamp-2">
                                                <a href="{{ route('public.news.show', $article->slug) }}">
                                                    {{ $article->title }}
                                                </a>
                                            </h4>

                                            @if($article->excerpt)
                                                <p class="mt-2 text-xs sm:text-sm text-white/55 line-clamp-2 font-light leading-relaxed">
                                                    {{ $article->excerpt }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between">
                                            <a
                                                href="{{ route('public.news.show', $article->slug) }}"
                                                class="inline-flex items-center gap-1.5 text-xs font-bold text-[#F5B800] group-hover:text-white transition-colors"
                                            >
                                                <span>Découvrir</span>
                                                <x-lucide-arrow-right class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                                            </a>
                                            <span class="text-[10px] uppercase tracking-wider text-white/30">
                                                Article
                                            </span>
                                        </div>
                                    </div>

                                </article>
                            @endforeach

                        </div>
                    @endif

                </div>

            @else

                {{-- État vide --}}
                <div class="rounded-3xl bg-white/[0.03] border border-white/10 p-12 text-center max-w-xl mx-auto">
                    <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-[#F5B800] mx-auto mb-4">
                        <x-lucide-newspaper class="w-8 h-8" />
                    </div>
                    <h3 class="text-xl font-serif font-semibold text-white">Le Journal est en préparation</h3>
                    <p class="text-sm text-white/50 mt-2 leading-relaxed">
                        Les prochains comptes-rendus de tournages, masterclasses et interviews d'intervenants seront bientôt publiés ici.
                    </p>
                </div>

            @endif

        </div>

    </div>

</section>




{{-- =========================================================
     ADMISSIONS
========================================================= --}}

<section
    id="admissions"
    class="relative py-28 lg:py-36 bg-[#080808] text-white overflow-hidden border-t border-white/10"
>

    {{-- Lueur d'ambiance immersive --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[650px] h-[650px] bg-gradient-to-tr from-[#320080]/35 to-[#F5B800]/15 rounded-full blur-[140px] pointer-events-none"></div>

    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">

        <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.25em] text-[#F5B800] mb-5">
            <span class="w-2 h-2 rounded-full bg-[#F5B800] animate-pulse"></span>
            Admissions & Candidature
        </p>

        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-normal tracking-tight text-white leading-tight">
            Votre regard commence ici.
        </h2>

        <p class="mt-6 text-base sm:text-lg text-white/65 max-w-2xl mx-auto leading-relaxed font-light">
            Vous souhaitez apprendre les métiers du cinéma et de l'audiovisuel, manipuler du matériel professionnel de pointe et développer votre propre vision créative ?
        </p>

        {{-- Points forts de la candidature --}}
        <div class="mt-10 flex flex-wrap justify-center gap-6 text-xs text-white/70 font-medium">
            <span class="flex items-center gap-2">
                <x-lucide-check-circle-2 class="w-4 h-4 text-[#F5B800]" />
                Dépôt de candidature 100% en ligne
            </span>
            <span class="flex items-center gap-2">
                <x-lucide-check-circle-2 class="w-4 h-4 text-[#F5B800]" />
                Entretien individuel de motivation
            </span>
            <span class="flex items-center gap-2">
                <x-lucide-check-circle-2 class="w-4 h-4 text-[#F5B800]" />
                Accompagnement personnalisé
            </span>
        </div>

        <div class="mt-12 flex flex-wrap items-center justify-center gap-4">
            <a
                href="{{ route('public.admissions.create') }}"
                class="group inline-flex items-center gap-3 px-9 py-4 rounded-full bg-[#F5B800] hover:bg-white text-black font-bold text-sm uppercase tracking-wider transition-all duration-300 shadow-[0_10px_30px_rgba(245,184,0,0.3)] hover:shadow-[0_15px_40px_rgba(255,255,255,0.4)]"
            >
                <span>Déposer ma candidature</span>
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-black text-white group-hover:bg-[#F5B800] group-hover:text-black transition">
                    <x-lucide-arrow-up-right class="w-3.5 h-3.5" />
                </span>
            </a>

            <a
                href="{{ route('public.courses.index') }}"
                class="inline-flex items-center gap-2 px-8 py-4 rounded-full bg-white/10 hover:bg-white/20 text-white font-semibold text-sm border border-white/20 transition-all duration-200"
            >
                <span>Consulter les formations</span>
                <x-lucide-arrow-right class="w-4 h-4" />
            </a>
        </div>

    </div>

</section>


{{-- =========================================================
     07 — PARTENAIRES & RÉSEAU PROFESSIONNEL
========================================================= --}}

<section
    id="partenaires"
    class="relative py-24 lg:py-32 bg-[#050507] text-white overflow-hidden border-t border-white/[0.08]"
>

    {{-- Lueur d'ambiance en arrière-plan --}}
    <div class="absolute -top-24 right-1/4 w-[500px] h-[500px] bg-[#F5B800]/10 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="absolute -bottom-24 left-1/4 w-[500px] h-[500px] bg-[#320080]/20 rounded-full blur-[140px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10">

        {{-- En-tête de section --}}
        <div class="text-center max-w-3xl mx-auto mb-16">
            <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.25em] text-[#F5B800] mb-4">
                <span class="w-2 h-2 rounded-full bg-[#F5B800] animate-pulse"></span>
                Écosystème & Partenaires
            </p>

            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal tracking-tight text-white leading-tight">
                Ils accompagnent l'émergence des nouveaux talents.
            </h2>

            <p class="mt-5 text-sm sm:text-base text-white/65 leading-relaxed font-light">
                L'EMSI collabore étroitement avec les institutions culturelles, les diffuseurs majeurs et les professionnels de l'industrie cinématographique et audiovisuelle pour garantir à nos étudiants une immersion professionnelle dès leur formation.
            </p>
        </div>

        {{-- Grille des partenaires --}}
        @if(isset($partners) && $partners->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-5 items-stretch">
                @foreach($partners as $partner)
                    @if($partner->website)
                        <a
                            href="{{ $partner->website }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group p-6 rounded-2xl bg-white/[0.03] hover:bg-white/[0.07] border border-white/[0.08] hover:border-[#F5B800]/40 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-[0_10px_30px_rgba(245,184,0,0.1)]"
                            title="{{ $partner->name }}"
                        >
                            <div class="w-20 h-14 rounded-xl bg-white/[0.04] border border-white/10 p-2.5 flex items-center justify-center mb-3 group-hover:border-[#F5B800]/50 group-hover:bg-white/[0.08] transition-all shadow-md">
                                @if($partner->logo)
                                    <img
                                        src="{{ asset('storage/' . $partner->logo) }}"
                                        alt="{{ $partner->name }}"
                                        class="max-h-full max-w-full object-contain filter brightness-90 group-hover:brightness-100 group-hover:scale-105 transition-all duration-300"
                                    >
                                @else
                                    <img
                                        src="{{ asset('images/partenaires/partenaire-' . (($loop->index % 6) + 1) . '.png') }}"
                                        alt="{{ $partner->name }}"
                                        class="max-h-full max-w-full object-contain filter brightness-90 group-hover:brightness-100 group-hover:scale-105 transition-all duration-300"
                                    >
                                @endif
                            </div>
                            <span class="mt-2 text-xs font-semibold text-white/80 group-hover:text-[#F5B800] transition-colors">
                                {{ $partner->name }}
                            </span>
                            @if($partner->description)
                                <span class="mt-1 text-[10px] text-white/45 line-clamp-1">
                                    {{ $partner->description }}
                                </span>
                            @endif
                        </a>
                    @else
                        <div class="group p-6 rounded-2xl bg-white/[0.03] hover:bg-white/[0.07] border border-white/[0.08] hover:border-[#F5B800]/40 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-[0_10px_30px_rgba(245,184,0,0.1)]">
                            <div class="w-20 h-14 rounded-xl bg-white/[0.04] border border-white/10 p-2.5 flex items-center justify-center mb-3 group-hover:border-[#F5B800]/50 group-hover:bg-white/[0.08] transition-all shadow-md">
                                @if($partner->logo)
                                    <img
                                        src="{{ asset('storage/' . $partner->logo) }}"
                                        alt="{{ $partner->name }}"
                                        class="max-h-full max-w-full object-contain filter brightness-90 group-hover:brightness-100 group-hover:scale-105 transition-all duration-300"
                                    >
                                @else
                                    <img
                                        src="{{ asset('images/partenaires/partenaire-' . (($loop->index % 6) + 1) . '.png') }}"
                                        alt="{{ $partner->name }}"
                                        class="max-h-full max-w-full object-contain filter brightness-90 group-hover:brightness-100 group-hover:scale-105 transition-all duration-300"
                                    >
                                @endif
                            </div>
                            <span class="mt-2 text-xs font-semibold text-white/80 group-hover:text-[#F5B800] transition-colors">
                                {{ $partner->name }}
                            </span>
                            @if($partner->description)
                                <span class="mt-1 text-[10px] text-white/45 line-clamp-1">
                                    {{ $partner->description }}
                                </span>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            {{-- Partenaires institutionnels et industriels avec vrais logos --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-6 items-stretch">

                {{-- 01. Grand Théâtre National --}}
                <div class="group p-6 rounded-2xl bg-white/[0.03] hover:bg-white/[0.07] border border-white/[0.08] hover:border-[#F5B800]/40 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-[0_10px_30px_rgba(245,184,0,0.1)]">
                    <div class="w-20 h-14 rounded-xl bg-white/[0.04] border border-white/10 p-2.5 flex items-center justify-center mb-3 group-hover:border-[#F5B800]/50 group-hover:bg-white/[0.08] transition-all shadow-md">
                        <img
                            src="{{ asset('images/partenaires/partenaire-1.png') }}"
                            alt="Grand Théâtre National Doudou Ndiaye Rose"
                            class="max-h-full max-w-full object-contain filter brightness-90 contrast-110 group-hover:brightness-110 group-hover:scale-105 transition-all duration-300"
                        >
                    </div>
                    <span class="text-xs font-bold text-white group-hover:text-[#F5B800] transition-colors">
                        Grand Théâtre National
                    </span>
                    <span class="mt-1 text-[10px] text-white/45 uppercase tracking-wider font-medium">
                        Résidence & Campus
                    </span>
                </div>

                {{-- 02. Ministère de la Culture --}}
                <div class="group p-6 rounded-2xl bg-white/[0.03] hover:bg-white/[0.07] border border-white/[0.08] hover:border-[#F5B800]/40 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-[0_10px_30px_rgba(245,184,0,0.1)]">
                    <div class="w-20 h-14 rounded-xl bg-white/[0.04] border border-white/10 p-2.5 flex items-center justify-center mb-3 group-hover:border-[#F5B800]/50 group-hover:bg-white/[0.08] transition-all shadow-md">
                        <img
                            src="{{ asset('images/partenaires/partenaire-2.png') }}"
                            alt="Ministère de la Culture"
                            class="max-h-full max-w-full object-contain filter brightness-90 contrast-110 group-hover:brightness-110 group-hover:scale-105 transition-all duration-300"
                        >
                    </div>
                    <span class="text-xs font-bold text-white group-hover:text-[#F5B800] transition-colors">
                        Ministère de la Culture
                    </span>
                    <span class="mt-1 text-[10px] text-white/45 uppercase tracking-wider font-medium">
                        Soutien Institutionnel
                    </span>
                </div>

                {{-- 03. FOPICA --}}
                <div class="group p-6 rounded-2xl bg-white/[0.03] hover:bg-white/[0.07] border border-white/[0.08] hover:border-[#F5B800]/40 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-[0_10px_30px_rgba(245,184,0,0.1)]">
                    <div class="w-20 h-14 rounded-xl bg-white/[0.04] border border-white/10 p-2.5 flex items-center justify-center mb-3 group-hover:border-[#F5B800]/50 group-hover:bg-white/[0.08] transition-all shadow-md">
                        <img
                            src="{{ asset('images/partenaires/partenaire-3.png') }}"
                            alt="FOPICA"
                            class="max-h-full max-w-full object-contain filter brightness-90 contrast-110 group-hover:brightness-110 group-hover:scale-105 transition-all duration-300"
                        >
                    </div>
                    <span class="text-xs font-bold text-white group-hover:text-[#F5B800] transition-colors">
                        FOPICA
                    </span>
                    <span class="mt-1 text-[10px] text-white/45 uppercase tracking-wider font-medium">
                        Fonds Cinéma Sénégal
                    </span>
                </div>

                {{-- 04. RTS --}}
                <div class="group p-6 rounded-2xl bg-white/[0.03] hover:bg-white/[0.07] border border-white/[0.08] hover:border-[#F5B800]/40 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-[0_10px_30px_rgba(245,184,0,0.1)]">
                    <div class="w-20 h-14 rounded-xl bg-white/[0.04] border border-white/10 p-2.5 flex items-center justify-center mb-3 group-hover:border-[#F5B800]/50 group-hover:bg-white/[0.08] transition-all shadow-md">
                        <img
                            src="{{ asset('images/partenaires/partenaire-4.png') }}"
                            alt="RTS Sénégal"
                            class="max-h-full max-w-full object-contain filter brightness-90 contrast-110 group-hover:brightness-110 group-hover:scale-105 transition-all duration-300"
                        >
                    </div>
                    <span class="text-xs font-bold text-white group-hover:text-[#F5B800] transition-colors">
                        RTS Sénégal
                    </span>
                    <span class="mt-1 text-[10px] text-white/45 uppercase tracking-wider font-medium">
                        Audiovisuel Public
                    </span>
                </div>

                {{-- 05. Chaînes & Diffuseurs --}}
                <div class="group p-6 rounded-2xl bg-white/[0.03] hover:bg-white/[0.07] border border-white/[0.08] hover:border-[#F5B800]/40 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-[0_10px_30px_rgba(245,184,0,0.1)]">
                    <div class="w-20 h-14 rounded-xl bg-white/[0.04] border border-white/10 p-2.5 flex items-center justify-center mb-3 group-hover:border-[#F5B800]/50 group-hover:bg-white/[0.08] transition-all shadow-md">
                        <img
                            src="{{ asset('images/partenaires/partenaire-5.png') }}"
                            alt="Diffuseurs et Médias"
                            class="max-h-full max-w-full object-contain filter brightness-90 contrast-110 group-hover:brightness-110 group-hover:scale-105 transition-all duration-300"
                        >
                    </div>
                    <span class="text-xs font-bold text-white group-hover:text-[#F5B800] transition-colors">
                        Diffuseurs & Médias
                    </span>
                    <span class="mt-1 text-[10px] text-white/45 uppercase tracking-wider font-medium">
                        Diffusion & Stages
                    </span>
                </div>

                {{-- 06. Studios de Post-Production --}}
                <div class="group p-6 rounded-2xl bg-white/[0.03] hover:bg-white/[0.07] border border-white/[0.08] hover:border-[#F5B800]/40 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-[0_10px_30px_rgba(245,184,0,0.1)]">
                    <div class="w-20 h-14 rounded-xl bg-white/[0.04] border border-white/10 p-2.5 flex items-center justify-center mb-3 group-hover:border-[#F5B800]/50 group-hover:bg-white/[0.08] transition-all shadow-md">
                        <img
                            src="{{ asset('images/partenaires/partenaire-6.png') }}"
                            alt="Studios de Production"
                            class="max-h-full max-w-full object-contain filter brightness-90 contrast-110 group-hover:brightness-110 group-hover:scale-105 transition-all duration-300"
                        >
                    </div>
                    <span class="text-xs font-bold text-white group-hover:text-[#F5B800] transition-colors">
                        Studios de Production
                    </span>
                    <span class="mt-1 text-[10px] text-white/45 uppercase tracking-wider font-medium">
                        Plateaux & Tournages
                    </span>
                </div>

            </div>
        @endif

        {{-- Bannière Devenir Partenaire --}}
        <div class="mt-16 p-8 rounded-3xl bg-gradient-to-r from-white/[0.04] via-white/[0.02] to-white/[0.04] border border-white/10 flex flex-col md:flex-row items-center justify-between gap-6 backdrop-blur-xl">
            <div class="flex items-center gap-4 text-center md:text-left">
                <div class="w-12 h-12 rounded-2xl bg-[#F5B800]/15 border border-[#F5B800]/30 flex items-center justify-center text-[#F5B800] shrink-0">
                    <x-lucide-handshake class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-white">
                        Vous souhaitez collaborer avec l'EMSI ?
                    </h3>
                    <p class="text-xs sm:text-sm text-white/60 mt-0.5">
                        Proposez des stages, co-produisez des projets audiovisuels ou intervenez lors de nos masterclasses.
                    </p>
                </div>
            </div>

            <a
                href="{{ route('public.about') }}"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white/10 hover:bg-[#F5B800] text-white hover:text-black text-xs font-bold uppercase tracking-wider transition-all duration-300 shrink-0 border border-white/15 hover:border-[#F5B800]"
            >
                <span>En savoir plus</span>
                <x-lucide-arrow-up-right class="w-4 h-4" />
            </a>
        </div>

    </div>

</section>


@endsection