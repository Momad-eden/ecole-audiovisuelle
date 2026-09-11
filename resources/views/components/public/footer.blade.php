<footer class="bg-[#080808] text-white border-t border-white/10 relative overflow-hidden">

    {{-- Lueur d'ambiance subtile en arrière-plan --}}
    <div class="absolute top-0 right-1/4 w-96 h-96 bg-[#320080]/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-1/4 w-80 h-80 bg-[#F5B800]/5 rounded-full blur-3xl pointer-events-none"></div>

    {{-- =====================================================
         FOOTER PRINCIPAL
    ====================================================== --}}
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16 lg:py-20 relative z-10">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 lg:gap-14">

            {{-- 1. IDENTITÉ & RÉSEAUX SOCIAUX (5 cols) --}}
            <div class="lg:col-span-5 space-y-6">

                {{-- Logo EMSI --}}
                <a href="{{ route('public.home') }}" class="inline-flex items-center group">
                    @if(isset($siteSettings) && $siteSettings?->logo)
                        <img
                            src="{{ asset('storage/' . $siteSettings->logo) }}"
                            alt="{{ $siteSettings->school_name ?? 'EMSI — École des Métiers du Son et de l’Image' }}"
                            class="h-14 lg:h-16 w-auto object-contain transition-transform duration-300 group-hover:scale-105 filter drop-shadow-[0_4px_20px_rgba(245,184,0,0.15)]"
                        >
                    @else
                        <img
                            src="{{ asset('images/logo.png') }}"
                            alt="EMSI — École des Métiers du Son et de l’Image"
                            class="h-14 lg:h-16 w-auto object-contain transition-transform duration-300 group-hover:scale-105 filter drop-shadow-[0_4px_20px_rgba(245,184,0,0.15)]"
                        >
                    @endif
                </a>

                <p class="text-sm text-white/60 leading-relaxed max-w-sm font-light">
                    {{ $siteSettings?->description ?? 'École des Métiers du Son et de l’Image au cœur du Grand Théâtre National Doudou Ndiaye Rose à Dakar. Formations pratiques d’excellence aux métiers du cinéma et de l’audiovisuel.' }}
                </p>

                {{-- Badge Statut --}}
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-white/[0.05] border border-white/10 text-[#F5B800]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#F5B800] animate-pulse"></span>
                    Promotion 2026 · Inscriptions Ouvertes
                </div>

                {{-- HUB RÉSEAUX SOCIAUX DYNAMIQUES (Configurés depuis l'Admin) --}}
                <div class="pt-2">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40 mb-3">
                        Rejoignez notre communauté
                    </p>

                    <div class="flex flex-wrap items-center gap-2.5">

                        {{-- Instagram --}}
                        @if($siteSettings?->instagram)
                            <a
                                href="{{ $siteSettings->instagram }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-9 h-9 rounded-full bg-white/[0.06] hover:bg-[#F5B800] text-white/70 hover:text-black border border-white/10 flex items-center justify-center transition-all duration-200"
                                title="Instagram de l'EMSI"
                                aria-label="Instagram"
                            >
                                <x-lucide-instagram class="w-4 h-4" />
                            </a>
                        @endif

                        {{-- Facebook --}}
                        @if($siteSettings?->facebook)
                            <a
                                href="{{ $siteSettings->facebook }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-9 h-9 rounded-full bg-white/[0.06] hover:bg-[#F5B800] text-white/70 hover:text-black border border-white/10 flex items-center justify-center transition-all duration-200"
                                title="Facebook de l'EMSI"
                                aria-label="Facebook"
                            >
                                <x-lucide-facebook class="w-4 h-4" />
                            </a>
                        @endif

                        {{-- YouTube --}}
                        @if($siteSettings?->youtube)
                            <a
                                href="{{ $siteSettings->youtube }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-9 h-9 rounded-full bg-white/[0.06] hover:bg-[#F5B800] text-white/70 hover:text-black border border-white/10 flex items-center justify-center transition-all duration-200"
                                title="Chaîne YouTube de l'EMSI"
                                aria-label="YouTube"
                            >
                                <x-lucide-youtube class="w-4 h-4" />
                            </a>
                        @endif

                        {{-- TikTok --}}
                        @if($siteSettings?->tiktok)
                            <a
                                href="{{ $siteSettings->tiktok }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-9 h-9 rounded-full bg-white/[0.06] hover:bg-[#F5B800] text-white/70 hover:text-black border border-white/10 flex items-center justify-center transition-all duration-200"
                                title="TikTok de l'EMSI"
                                aria-label="TikTok"
                            >
                                <x-lucide-video class="w-4 h-4" />
                            </a>
                        @endif

                        {{-- LinkedIn --}}
                        @if($siteSettings?->linkedin)
                            <a
                                href="{{ $siteSettings->linkedin }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-9 h-9 rounded-full bg-white/[0.06] hover:bg-[#F5B800] text-white/70 hover:text-black border border-white/10 flex items-center justify-center transition-all duration-200"
                                title="LinkedIn de l'EMSI"
                                aria-label="LinkedIn"
                            >
                                <x-lucide-linkedin class="w-4 h-4" />
                            </a>
                        @endif

                        {{-- Twitter / X --}}
                        @if($siteSettings?->twitter)
                            <a
                                href="{{ $siteSettings->twitter }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-9 h-9 rounded-full bg-white/[0.06] hover:bg-[#F5B800] text-white/70 hover:text-black border border-white/10 flex items-center justify-center transition-all duration-200"
                                title="Twitter / X de l'EMSI"
                                aria-label="Twitter"
                            >
                                <x-lucide-twitter class="w-4 h-4" />
                            </a>
                        @endif

                        {{-- WhatsApp --}}
                        @if($siteSettings?->whatsapp)
                            <a
                                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings->whatsapp) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-9 h-9 rounded-full bg-emerald-600/20 hover:bg-emerald-500 text-emerald-400 hover:text-white border border-emerald-500/30 flex items-center justify-center transition-all duration-200"
                                title="Discuter sur WhatsApp"
                                aria-label="WhatsApp"
                            >
                                <x-lucide-message-circle class="w-4 h-4" />
                            </a>
                        @endif

                    </div>
                </div>

            </div>


            {{-- 2. NAVIGATION (3 cols) --}}
            <div class="lg:col-span-3 space-y-4">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#F5B800]">
                    Navigation
                </p>

                <ul class="space-y-3 text-sm text-white/65 font-light">
                    <li>
                        <a href="{{ route('public.home') }}" class="hover:text-white transition-colors">Accueil</a>
                    </li>
                    <li>
                        <a href="{{ route('public.project') }}" class="hover:text-[#F5B800] transition-colors font-medium">Le Projet Officiel</a>
                    </li>
                    <li>
                        <a href="{{ route('public.vae') }}" class="hover:text-[#F5B800] transition-colors font-medium">Dispositif VAE (BTS)</a>
                    </li>
                    <li>
                        <a href="{{ route('public.courses.index') }}" class="hover:text-white transition-colors">5 Filières d'Excellence</a>
                    </li>
                    <li>
                        <a href="{{ route('public.about') }}" class="hover:text-white transition-colors">L'école & Campus</a>
                    </li>
                    <li>
                        <a href="{{ route('public.gallery.index') }}" class="hover:text-white transition-colors">Galerie & Réalisations</a>
                    </li>
                    <li>
                        <a href="{{ route('public.news.index') }}" class="hover:text-white transition-colors">Actualités & Journal</a>
                    </li>
                    <li>
                        <a href="{{ route('public.admissions.create') }}" class="text-[#F5B800] hover:underline font-semibold">
                            Candidater en ligne
                        </a>
                    </li>
                </ul>
            </div>


            {{-- 3. CAMPUS & CONTACT (4 cols) --}}
            <div class="lg:col-span-4 space-y-4">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#F5B800]">
                    Campus & Contact
                </p>

                <div class="space-y-3.5 text-xs sm:text-sm text-white/60">

                    {{-- Adresse --}}
                    <div class="flex items-start gap-3">
                        <x-lucide-map-pin class="w-4 h-4 text-[#F5B800] shrink-0 mt-0.5" />
                        <span class="leading-relaxed">
                            {{ $siteSettings?->address ?? 'Grand Théâtre National Doudou Ndiaye Rose, Dakar, Sénégal' }}
                        </span>
                    </div>

                    {{-- Téléphone --}}
                    @if($siteSettings?->phone)
                        <div class="flex items-center gap-3">
                            <x-lucide-phone class="w-4 h-4 text-[#F5B800] shrink-0" />
                            <a href="tel:{{ $siteSettings->phone }}" class="hover:text-white transition-colors font-medium">
                                {{ $siteSettings->phone }}
                            </a>
                        </div>
                    @endif

                    {{-- Email --}}
                    @if($siteSettings?->email)
                        <div class="flex items-center gap-3">
                            <x-lucide-mail class="w-4 h-4 text-[#F5B800] shrink-0" />
                            <a href="mailto:{{ $siteSettings->email }}" class="hover:text-white transition-colors font-medium">
                                {{ $siteSettings->email }}
                            </a>
                        </div>
                    @endif

                    {{-- Horaires --}}
                    <div class="flex items-start gap-3 pt-2 border-t border-white/10 text-white/50 text-xs">
                        <x-lucide-clock class="w-4 h-4 text-white/40 shrink-0 mt-0.5" />
                        <span>Accueil : Lun – Ven (08h30 – 18h00)</span>
                    </div>

                </div>

                {{-- Bouton Candidature Rapide --}}
                <div class="pt-2">
                    <a
                        href="{{ route('public.admissions.create') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#F5B800] hover:bg-white text-black font-bold text-xs uppercase tracking-wider transition-colors duration-200 shadow-lg"
                    >
                        <span>Déposer ma candidature</span>
                        <x-lucide-arrow-up-right class="w-3.5 h-3.5" />
                    </a>
                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         SOUS-FOOTER / BAS DE PAGE
    ====================================================== --}}
    <div class="border-t border-white/10 bg-black/40">

        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-6">

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-white/40">

                <p>
                    © {{ date('Y') }} EMSI — École des Métiers du Son et de l’Image. Tous droits réservés.
                </p>

                <div class="flex items-center gap-6">
                    <a href="{{ route('public.about') }}#contact" class="hover:text-[#F5B800] transition-colors">
                        Plan d'accès
                    </a>
                    <span class="text-white/20">•</span>
                    <a href="{{ route('login') }}" class="hover:text-white transition-colors">
                        Espace Administration
                    </a>
                </div>

            </div>

        </div>

    </div>

</footer>