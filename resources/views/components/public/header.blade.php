{{-- =========================================================
     HEADER PUBLIC — EMSI AUDIOVISUELLE
     Design cinématique luxe, glassmorphism sombre & micro-interactions
========================================================== --}}

<header
    id="public-header"
    x-data="{
        scrolled: false,
        scrollProgress: 0,
        mobileMenuOpen: false,
        formationsDropdownOpen: false,
        mobileFormationsOpen: false,
        init() {
            const updateScroll = () => {
                this.scrolled = window.scrollY > 15;
                const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
                const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                this.scrollProgress = height > 0 ? Math.min(100, Math.max(0, (winScroll / height) * 100)) : 0;
            };
            window.addEventListener('scroll', updateScroll, { passive: true });
            updateScroll();
        }
    }"
    @keydown.escape.window="mobileMenuOpen = false; formationsDropdownOpen = false"
    class="fixed top-0 left-0 right-0 z-50 text-white font-sans select-none"
>

    {{-- =====================================================
         1. JAUGE DE PROGRESSION DE LECTURE (SCROLL BAR)
    ====================================================== --}}
    <div class="absolute top-0 left-0 right-0 h-[2px] bg-transparent overflow-hidden pointer-events-none z-[60]">
        <div
            class="h-full bg-gradient-to-r from-[#F5B800] via-[#E59800] to-[#C15C03] shadow-[0_0_12px_rgba(245,184,0,0.9)] transition-all duration-100 ease-out"
            :style="'width: ' + scrollProgress + '%'"
        ></div>
    </div>


    {{-- =====================================================
         2. MICRO-BARRE D'INFORMATION SUPÉRIEURE (TOP UTILITY BAR)
    ====================================================== --}}
    <div
        class="hidden md:block border-b border-white/[0.06] bg-[#050505]/95 backdrop-blur-md transition-all duration-300"
        :class="scrolled ? 'h-0 opacity-0 overflow-hidden py-0 border-b-0' : 'h-9 opacity-100 py-1.5'"
    >
        <div class="max-w-7xl mx-auto px-6 lg:px-10 flex items-center justify-between text-[11px] font-medium text-white/70">

            {{-- Statut et Localisation --}}
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/25 text-[#F5B800] text-[10.5px] font-semibold tracking-wide">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#F5B800] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-[#F5B800]"></span>
                    </span>
                    École des Métiers du Son et de l'Image
                </span>

                <span class="text-white/20 hidden lg:inline">•</span>

                <span class="hidden lg:flex items-center gap-1.5 text-white/60">
                    <x-lucide-map-pin class="w-3 h-3 text-[#F5B800]/80" />
                    Grand Théâtre National Doudou Ndiaye Rose, Dakar
                </span>
            </div>

            {{-- Contacts rapides & Accès Admin --}}
            <div class="flex items-center gap-5">

                @if($siteSettings?->phone)
                    <a
                        href="tel:{{ $siteSettings->phone }}"
                        class="flex items-center gap-1.5 text-white/60 hover:text-[#F5B800] transition-colors"
                        title="Appelez l'EMSI"
                    >
                        <x-lucide-phone class="w-3 h-3 text-[#F5B800]" />
                        <span>{{ $siteSettings->phone }}</span>
                    </a>
                @endif

                @if($siteSettings?->whatsapp)
                    <a
                        href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings->whatsapp) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-1.5 text-white/60 hover:text-[#25D366] transition-colors"
                        title="Contact WhatsApp"
                    >
                        <x-lucide-message-circle class="w-3 h-3 text-[#25D366]" />
                        <span>WhatsApp</span>
                    </a>
                @endif

                @auth
                    <span class="text-white/20">•</span>
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/10 hover:bg-[#F5B800] text-white hover:text-black font-semibold transition-all duration-200"
                    >
                        <x-lucide-layout-dashboard class="w-3 h-3" />
                        <span>Espace Admin</span>
                    </a>
                @endauth

            </div>

        </div>
    </div>


    {{-- =====================================================
         3. BARRE DE NAVIGATION PRINCIPALE (MAIN HEADER)
    ====================================================== --}}
    <div
        class="border-b transition-all duration-300"
        :class="scrolled
            ? 'bg-[#080808]/92 backdrop-blur-2xl border-white/10 shadow-[0_10px_35px_rgba(0,0,0,0.6)] py-2.5 lg:py-3'
            : 'bg-[#080808]/80 backdrop-blur-xl border-white/[0.08] shadow-[0_4px_25px_rgba(0,0,0,0.35)] py-3.5 lg:py-4'"
    >

        <div class="max-w-7xl mx-auto px-6 lg:px-10 flex items-center justify-between">

            {{-- =================================================
                 LOGO & MARQUE DE L'ÉCOLE
            ================================================== --}}
            <a
                href="{{ route('public.home') }}"
                aria-label="EMSI — École des Métiers du Son et de l'Image"
                class="flex items-center gap-3.5 group shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B800] rounded-xl"
            >

                @if($siteSettings?->logo)

                    <img
                        src="{{ asset('storage/' . $siteSettings->logo) }}"
                        alt="{{ $siteSettings->school_name ?? 'EMSI' }}"
                        class="h-11 md:h-13 w-auto object-contain transition-transform duration-300 group-hover:scale-105 filter drop-shadow-[0_2px_10px_rgba(245,184,0,0.15)]"
                    >

                @elseif(file_exists(public_path('images/logo.png')))

                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="{{ $siteSettings->school_name ?? 'EMSI — École des Métiers du Son et de l\'Image' }}"
                        class="h-11 md:h-13 w-auto object-contain transition-transform duration-300 group-hover:scale-105 filter drop-shadow-[0_2px_10px_rgba(245,184,0,0.15)]"
                    >

                @else

                    {{-- Monogramme Cinéma & Typographie d'excellence (si aucune image de logo) --}}
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 md:h-11 md:w-11 items-center justify-center rounded-xl bg-gradient-to-br from-white/15 to-white/5 border border-white/20 text-[#F5B800] font-serif text-xl font-bold shadow-[0_0_20px_rgba(245,184,0,0.2)] group-hover:border-[#F5B800]/60 transition-colors">
                            E
                        </div>

                        <div>
                            <div class="text-sm md:text-base font-bold tracking-[0.18em] text-white flex items-center gap-1.5">
                                <span>EMSI</span>
                                <span class="h-1.5 w-1.5 rounded-full bg-[#F5B800]"></span>
                            </div>

                            <div class="text-[8px] uppercase tracking-[0.22em] text-white/50 font-medium">
                                Dakar • Cinéma & Audiovisuel
                            </div>
                        </div>
                    </div>

                @endif

            </a>


            {{-- =================================================
                 NAVIGATION DESKTOP
            ================================================== --}}
            <nav
                class="hidden md:flex items-center gap-1 lg:gap-2 ml-auto mr-6 lg:mr-8"
                aria-label="Navigation principale"
            >

                {{-- Accueil --}}
                <a
                    href="{{ route('public.home') }}"
                    class="relative px-3.5 py-2 rounded-full text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('public.home') ? 'text-[#F5B800] font-bold bg-white/[0.05]' : 'text-white/70 hover:text-white hover:bg-white/[0.04]' }}"
                >
                    <span>Accueil</span>
                    @if(request()->routeIs('public.home'))
                        <span class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-[#F5B800]"></span>
                    @endif
                </a>

                {{-- L'École --}}
                <a
                    href="{{ route('public.about') }}"
                    class="relative px-3.5 py-2 rounded-full text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('public.about') ? 'text-[#F5B800] font-bold bg-white/[0.05]' : 'text-white/70 hover:text-white hover:bg-white/[0.04]' }}"
                >
                    <span>L'école</span>
                    @if(request()->routeIs('public.about'))
                        <span class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-[#F5B800]"></span>
                    @endif
                </a>

                {{-- Formations (avec Mega-Dropdown interactif) --}}
                <div
                    class="relative"
                    @mouseenter="formationsDropdownOpen = true"
                    @mouseleave="formationsDropdownOpen = false"
                >
                    <a
                        href="{{ route('public.courses.index') }}"
                        class="relative flex items-center gap-1.5 px-3.5 py-2 rounded-full text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('public.courses.*') ? 'text-[#F5B800] font-bold bg-white/[0.05]' : 'text-white/70 hover:text-white hover:bg-white/[0.04]' }}"
                        aria-expanded="false"
                        :aria-expanded="formationsDropdownOpen.toString()"
                    >
                        <span>Formations</span>
                        <span class="inline-flex transition-transform duration-200" :class="formationsDropdownOpen ? 'rotate-180 text-[#F5B800]' : 'text-white/40'">
                            <x-lucide-chevron-down class="w-3.5 h-3.5" />
                        </span>
                        @if(request()->routeIs('public.courses.*'))
                            <span class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-[#F5B800]"></span>
                        @endif
                    </a>

                    {{-- Panneau Dropdown Grand Format (2 Colonnes) - 100% OPAQUE & HAUT CONTRASTE --}}
                    <div
                        x-show="formationsDropdownOpen"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 translate-y-3 scale-[0.98]"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
                        class="absolute top-full left-1/2 -translate-x-1/2 pt-3 w-[640px] lg:w-[740px] z-50 pointer-events-auto"
                        style="display: none;"
                    >
                        <div class="rounded-3xl bg-[#131316] border border-white/20 shadow-[0_25px_60px_rgba(0,0,0,0.95)] p-6 overflow-hidden relative">

                            <div class="grid grid-cols-12 gap-6 relative z-10">

                                {{-- COLONNE GAUCHE (7/12) : LISTE DES FILIÈRES RÉELLES --}}
                                <div class="col-span-7 flex flex-col justify-between border-r border-white/10 pr-6">

                                    <div>
                                        {{-- En-tête --}}
                                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-white/10">
                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-widest text-[#F5B800] flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-[#F5B800]"></span>
                                                    Cursus & Formations
                                                </p>
                                                <p class="text-xs text-gray-300 mt-0.5">
                                                    Programmes de l'école audiovisuelle
                                                </p>
                                            </div>
                                            <span class="px-2.5 py-1 rounded-full bg-[#F5B800]/15 border border-[#F5B800]/30 text-[11px] text-[#F5B800] font-bold">
                                                80% Pratique
                                            </span>
                                        </div>

                                        {{-- Liste des filières réelles --}}
                                        <div class="grid grid-cols-1 gap-2">
                                            @if(isset($headerCourses) && $headerCourses->count() > 0)
                                                @foreach($headerCourses->take(5) as $course)
                                                    <a
                                                        href="{{ route('public.courses.show', $course->slug) }}"
                                                        class="group/item flex items-center justify-between p-3 rounded-2xl bg-[#1a1a20] hover:bg-[#24242c] border border-white/10 hover:border-[#F5B800]/50 transition-all duration-200"
                                                    >
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-9 h-9 rounded-xl bg-[#22222a] border border-white/15 flex items-center justify-center text-[#F5B800] group-hover/item:bg-[#F5B800] group-hover/item:text-black transition-colors shrink-0">
                                                                @if(str_contains(strtolower($course->title . $course->category), 'son') || str_contains(strtolower($course->title . $course->category), 'audio'))
                                                                    <x-lucide-mic class="w-4 h-4" />
                                                                @elseif(str_contains(strtolower($course->title . $course->category), 'montage') || str_contains(strtolower($course->title . $course->category), 'post'))
                                                                    <x-lucide-sliders class="w-4 h-4" />
                                                                @elseif(str_contains(strtolower($course->title . $course->category), 'photo') || str_contains(strtolower($course->title . $course->category), 'image') || str_contains(strtolower($course->title . $course->category), 'caméra'))
                                                                    <x-lucide-camera class="w-4 h-4" />
                                                                @else
                                                                    <x-lucide-clapperboard class="w-4 h-4" />
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <p class="text-xs font-bold text-white group-hover/item:text-[#F5B800] transition-colors line-clamp-1">
                                                                    {{ $course->title }}
                                                                </p>
                                                                <p class="text-[11px] text-gray-300 flex items-center gap-2 mt-0.5">
                                                                    @if($course->category)
                                                                        <span class="font-medium text-gray-200">{{ $course->category }}</span>
                                                                    @endif
                                                                    @if($course->duration)
                                                                        <span>• {{ $course->duration }}</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <x-lucide-chevron-right class="w-4 h-4 text-gray-400 group-hover/item:text-[#F5B800] group-hover/item:translate-x-1 transition-all shrink-0" />
                                                    </a>
                                                @endforeach
                                            @else
                                                {{-- Si aucune formation en BDD, présentation sobre du catalogue officiel sans inventer de fausses filières --}}
                                                <div class="p-4 rounded-2xl bg-[#1a1a20] border border-white/10 space-y-2">
                                                    <p class="text-xs font-semibold text-white">
                                                        Catalogue officiel des Formations
                                                    </p>
                                                    <p class="text-xs text-gray-300 leading-relaxed">
                                                        Retrouvez l'ensemble des cursus diplômants et certifiants de l'EMSI sur la page dédiée aux formations.
                                                    </p>
                                                    <div class="pt-2">
                                                        <a
                                                            href="{{ route('public.courses.index') }}"
                                                            class="inline-flex items-center gap-2 text-xs font-bold text-[#F5B800] hover:text-white transition-colors"
                                                        >
                                                            <span>Accéder au catalogue complet</span>
                                                            <x-lucide-arrow-right class="w-3.5 h-3.5" />
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Lien catalogue complet --}}
                                    <div class="pt-3 mt-3 border-t border-white/10">
                                        <a
                                            href="{{ route('public.courses.index') }}"
                                            class="inline-flex items-center gap-2 text-xs font-bold text-[#F5B800] hover:text-white transition-colors group/all"
                                        >
                                            <span>Consulter toutes les formations</span>
                                            <x-lucide-arrow-right class="w-3.5 h-3.5 group-hover/all:translate-x-1 transition-transform" />
                                        </a>
                                    </div>

                                </div>

                                {{-- COLONNE DROITE (5/12) : SPOTLIGHT & ADMISSIONS --}}
                                <div class="col-span-5 flex flex-col justify-between rounded-2xl bg-[#18181f] border border-white/15 p-5">

                                    <div>
                                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#F5B800]/20 border border-[#F5B800]/40 text-[#F5B800] text-[10px] font-bold uppercase tracking-wider mb-3">
                                            Rentrée 2024–2025
                                        </div>

                                        <h4 class="font-serif text-sm font-bold text-white leading-snug">
                                            Grand Théâtre National Doudou Ndiaye Rose
                                        </h4>

                                        <p class="text-xs text-gray-300 mt-2 leading-relaxed">
                                            Formation intensive aux métiers du cinéma et de l'audiovisuel au cœur de Dakar.
                                        </p>

                                        {{-- Points forts --}}
                                        <div class="mt-4 space-y-2.5 text-xs text-gray-200 font-medium">
                                            <div class="flex items-center gap-2.5">
                                                <x-lucide-check class="w-4 h-4 text-[#F5B800] shrink-0" />
                                                <span>Plateaux de tournage cinéma 4K/6K</span>
                                            </div>
                                            <div class="flex items-center gap-2.5">
                                                <x-lucide-check class="w-4 h-4 text-[#F5B800] shrink-0" />
                                                <span>Diplômes & Certifications reconnus</span>
                                            </div>
                                            <div class="flex items-center gap-2.5">
                                                <x-lucide-check class="w-4 h-4 text-[#F5B800] shrink-0" />
                                                <span>Intervenants professionnels en activité</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-5 pt-4 border-t border-white/10">
                                        <a
                                            href="{{ route('public.admissions.create') }}"
                                            class="flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-[#F5B800] hover:bg-white text-black font-bold text-xs uppercase tracking-wider transition-all duration-200 shadow-lg"
                                        >
                                            <span>Déposer une candidature</span>
                                            <x-lucide-arrow-up-right class="w-4 h-4 stroke-[2.5]" />
                                        </a>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                {{-- Galerie --}}
                <a
                    href="{{ route('public.gallery.index') }}"
                    class="relative px-3.5 py-2 rounded-full text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('public.gallery.*') ? 'text-[#F5B800] font-bold bg-white/[0.05]' : 'text-white/70 hover:text-white hover:bg-white/[0.04]' }}"
                >
                    <span>Galerie</span>
                    @if(request()->routeIs('public.gallery.*'))
                        <span class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-[#F5B800]"></span>
                    @endif
                </a>

                {{-- Actualités --}}
                <a
                    href="{{ route('public.news.index') }}"
                    class="relative px-3.5 py-2 rounded-full text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('public.news.*') ? 'text-[#F5B800] font-bold bg-white/[0.05]' : 'text-white/70 hover:text-white hover:bg-white/[0.04]' }}"
                >
                    <span>Actualités</span>
                    @if(request()->routeIs('public.news.*'))
                        <span class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-[#F5B800]"></span>
                    @endif
                </a>

            </nav>


            {{-- =================================================
                 ACTIONS : BOUTON ADMISSION & BURGER MOBILE
            ================================================== --}}
            <div class="flex items-center gap-3">

                {{-- Bouton WhatsApp Desktop (Discret & Rapide) --}}
                @if($siteSettings?->whatsapp)
                    <a
                        href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings->whatsapp) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hidden xl:inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/[0.06] hover:bg-[#25D366]/20 border border-white/10 hover:border-[#25D366]/40 text-[#25D366] transition-all duration-200"
                        title="Échanger sur WhatsApp"
                        aria-label="WhatsApp"
                    >
                        <x-lucide-message-circle class="w-4 h-4" />
                    </a>
                @endif

                {{-- CTA Principal : Admission --}}
                <a
                    href="{{ route('public.admissions.create') }}"
                    class="hidden sm:inline-flex items-center gap-2.5 px-5 lg:px-6 py-2.5 rounded-full bg-gradient-to-r from-[#F5B800] via-[#F5B800] to-[#E59800] text-black font-bold text-xs lg:text-sm tracking-wide shadow-[0_0_25px_rgba(245,184,0,0.35)] hover:shadow-[0_0_35px_rgba(245,184,0,0.6)] hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 group"
                >
                    <span>Admission</span>
                    <span class="w-5 h-5 rounded-full bg-black/10 flex items-center justify-center text-black group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform">
                        <x-lucide-arrow-up-right class="w-3.5 h-3.5 stroke-[2.5]" />
                    </span>
                </a>

                {{-- =============================================
                     BOUTON BURGER MOBILE (ANIMÉ EN X)
                ============================================== --}}
                <button
                    type="button"
                    id="public-mobile-menu-button"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="md:hidden relative flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/[0.05] hover:bg-white/10 text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B800] transition"
                    :aria-expanded="mobileMenuOpen.toString()"
                    aria-controls="public-mobile-menu"
                    :aria-label="mobileMenuOpen ? 'Fermer le menu' : 'Ouvrir le menu'"
                >
                    {{-- 3 barres animées --}}
                    <div class="w-5 h-4 flex flex-col justify-between items-center relative">
                        <span
                            class="w-5 h-0.5 bg-white rounded-full transition-all duration-300 transform origin-center"
                            :class="mobileMenuOpen ? 'rotate-45 translate-y-[7px] bg-[#F5B800]' : ''"
                        ></span>
                        <span
                            class="w-5 h-0.5 bg-white rounded-full transition-all duration-200"
                            :class="mobileMenuOpen ? 'opacity-0' : 'opacity-100'"
                        ></span>
                        <span
                            class="w-5 h-0.5 bg-white rounded-full transition-all duration-300 transform origin-center"
                            :class="mobileMenuOpen ? '-rotate-45 -translate-y-[7px] bg-[#F5B800]' : ''"
                        ></span>
                    </div>
                </button>

            </div>

        </div>

    </div>


    {{-- =====================================================
         4. MENU MOBILE PLEIN ÉCRAN (IMMERSIVE MOBILE DRAWER)
    ====================================================== --}}
    <div
        id="public-mobile-menu"
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="md:hidden fixed inset-x-0 top-[60px] bottom-0 bg-[#080808]/98 backdrop-blur-2xl border-t border-white/10 overflow-y-auto z-40 flex flex-col justify-between"
        style="display: none;"
    >

        {{-- Halos d'ambiance --}}
        <div class="absolute top-10 right-0 w-64 h-64 bg-[#320080]/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 left-0 w-64 h-64 bg-[#F5B800]/10 rounded-full blur-3xl pointer-events-none"></div>

        {{-- Contenu Navigation Mobile --}}
        <div class="p-6 relative z-10 flex flex-col space-y-1">

            {{-- 01. Accueil --}}
            <a
                href="{{ route('public.home') }}"
                @click="mobileMenuOpen = false"
                class="flex items-center justify-between py-3.5 border-b border-white/[0.06] text-base font-semibold transition {{ request()->routeIs('public.home') ? 'text-[#F5B800]' : 'text-white/90 hover:text-white' }}"
            >
                <div class="flex items-center gap-3.5">
                    <span class="text-[10px] font-mono tracking-widest text-[#F5B800]/60">01</span>
                    <span>Accueil</span>
                </div>
                <x-lucide-arrow-up-right class="w-4 h-4 text-white/30" />
            </a>

            {{-- 02. L'École --}}
            <a
                href="{{ route('public.about') }}"
                @click="mobileMenuOpen = false"
                class="flex items-center justify-between py-3.5 border-b border-white/[0.06] text-base font-semibold transition {{ request()->routeIs('public.about') ? 'text-[#F5B800]' : 'text-white/90 hover:text-white' }}"
            >
                <div class="flex items-center gap-3.5">
                    <span class="text-[10px] font-mono tracking-widest text-[#F5B800]/60">02</span>
                    <span>L'école</span>
                </div>
                <x-lucide-arrow-up-right class="w-4 h-4 text-white/30" />
            </a>

            {{-- 03. Formations (Accordéon) --}}
            <div class="border-b border-white/[0.06] py-3.5">
                <div class="flex items-center justify-between">
                    <a
                        href="{{ route('public.courses.index') }}"
                        @click="mobileMenuOpen = false"
                        class="flex items-center gap-3.5 text-base font-semibold {{ request()->routeIs('public.courses.*') ? 'text-[#F5B800]' : 'text-white/90 hover:text-white' }}"
                    >
                        <span class="text-[10px] font-mono tracking-widest text-[#F5B800]/60">03</span>
                        <span>Formations</span>
                    </a>

                    <button
                        type="button"
                        @click="mobileFormationsOpen = !mobileFormationsOpen"
                        class="p-1 text-white/50 hover:text-[#F5B800] transition"
                        aria-label="Afficher les filières"
                    >
                        <span class="inline-flex transition-transform duration-200" :class="mobileFormationsOpen ? 'rotate-180 text-[#F5B800]' : ''">
                            <x-lucide-chevron-down class="w-4 h-4" />
                        </span>
                    </button>
                </div>

                {{-- Sous-liste formations --}}
                <div
                    x-show="mobileFormationsOpen"
                    x-transition
                    class="mt-3 pl-6 pr-3 py-3 space-y-2.5 bg-[#15151a] rounded-2xl border border-white/10"
                    style="display: none;"
                >
                    @if(isset($headerCourses) && $headerCourses->count() > 0)
                        @foreach($headerCourses as $course)
                            <a
                                href="{{ route('public.courses.show', $course->slug) }}"
                                @click="mobileMenuOpen = false"
                                class="block py-1.5 text-xs font-semibold text-gray-200 hover:text-[#F5B800] transition"
                            >
                                • {{ $course->title }}
                            </a>
                        @endforeach
                    @endif
                    <a
                        href="{{ route('public.courses.index') }}"
                        @click="mobileMenuOpen = false"
                        class="block pt-1.5 text-xs font-bold text-[#F5B800] hover:underline"
                    >
                        → Accéder au catalogue complet
                    </a>
                </div>
            </div>

            {{-- 04. Galerie --}}
            <a
                href="{{ route('public.gallery.index') }}"
                @click="mobileMenuOpen = false"
                class="flex items-center justify-between py-3.5 border-b border-white/[0.06] text-base font-semibold transition {{ request()->routeIs('public.gallery.*') ? 'text-[#F5B800]' : 'text-white/90 hover:text-white' }}"
            >
                <div class="flex items-center gap-3.5">
                    <span class="text-[10px] font-mono tracking-widest text-[#F5B800]/60">04</span>
                    <span>Galerie & Réalisations</span>
                </div>
                <x-lucide-arrow-up-right class="w-4 h-4 text-white/30" />
            </a>

            {{-- 05. Actualités --}}
            <a
                href="{{ route('public.news.index') }}"
                @click="mobileMenuOpen = false"
                class="flex items-center justify-between py-3.5 border-b border-white/[0.06] text-base font-semibold transition {{ request()->routeIs('public.news.*') ? 'text-[#F5B800]' : 'text-white/90 hover:text-white' }}"
            >
                <div class="flex items-center gap-3.5">
                    <span class="text-[10px] font-mono tracking-widest text-[#F5B800]/60">05</span>
                    <span>Actualités & Journal</span>
                </div>
                <x-lucide-arrow-up-right class="w-4 h-4 text-white/30" />
            </a>

            {{-- Bouton Admission Mobile --}}
            <div class="pt-6">
                <a
                    href="{{ route('public.admissions.create') }}"
                    @click="mobileMenuOpen = false"
                    class="flex items-center justify-center gap-2.5 w-full py-3.5 rounded-full bg-gradient-to-r from-[#F5B800] to-[#E59800] text-black font-bold text-sm tracking-wide shadow-[0_0_25px_rgba(245,184,0,0.4)] transition active:scale-[0.98]"
                >
                    <span>Déposer ma candidature</span>
                    <x-lucide-arrow-up-right class="w-4 h-4 stroke-[2.5]" />
                </a>
            </div>

            @auth
                <div class="pt-3">
                    <a
                        href="{{ route('dashboard') }}"
                        @click="mobileMenuOpen = false"
                        class="flex items-center justify-center gap-2 w-full py-2.5 rounded-full bg-white/10 text-white text-xs font-semibold hover:bg-white/20 transition"
                    >
                        <x-lucide-layout-dashboard class="w-3.5 h-3.5 text-[#F5B800]" />
                        <span>Accéder à l'Administration</span>
                    </a>
                </div>
            @endauth

        </div>

        {{-- Pied du Menu Mobile avec Coordonnées --}}
        <div class="p-6 bg-black/40 border-t border-white/[0.08] relative z-10 space-y-4">
            <div class="text-xs text-white/50 space-y-1">
                <p class="font-semibold text-white/80">EMSI — École des Métiers du Son et de l'Image</p>
                <p>Grand Théâtre National Doudou Ndiaye Rose, Dakar</p>
            </div>

            <div class="flex items-center gap-3 pt-2">
                @if($siteSettings?->phone)
                    <a
                        href="tel:{{ $siteSettings->phone }}"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-white/[0.05] border border-white/10 text-xs font-medium text-white hover:bg-white/10 transition"
                    >
                        <x-lucide-phone class="w-3.5 h-3.5 text-[#F5B800]" />
                        <span>Appeler</span>
                    </a>
                @endif

                @if($siteSettings?->whatsapp)
                    <a
                        href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings->whatsapp) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-[#25D366]/10 border border-[#25D366]/25 text-xs font-medium text-[#25D366] hover:bg-[#25D366]/20 transition"
                    >
                        <x-lucide-message-circle class="w-3.5 h-3.5" />
                        <span>WhatsApp</span>
                    </a>
                @endif
            </div>
        </div>

    </div>

</header>