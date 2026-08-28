<footer class="bg-[#0D0D0D] text-white">

    {{-- =====================================================
         FOOTER PRINCIPAL
    ====================================================== --}}

    <div
        class="
            max-w-7xl
            mx-auto

            px-6
            lg:px-10

            py-14
            lg:py-16
        ">

        <div
            class="
                grid

                md:grid-cols-[1.4fr_0.8fr_0.8fr]

                gap-12
                lg:gap-20
            ">

            {{-- =================================================
     IDENTITÉ
================================================== --}}

            <div>

                {{-- Logo réel EMSI --}}

                <a
                    href="{{ url('/') }}"
                    class="inline-flex items-center group">

                    @if(isset($siteSettings) && $siteSettings?->logo)
                        <img
                            src="{{ asset('storage/' . $siteSettings->logo) }}"
                            alt="{{ $siteSettings->school_name ?? 'EMSI — École des Métiers du Son et de l\'Image' }}"
                            class="h-16 md:h-20 w-auto object-contain transition-transform duration-300 group-hover:scale-105 filter drop-shadow-[0_4px_20px_rgba(245,184,0,0.15)]">
                    @else
                        <img
                            src="{{ asset('images/logo.png') }}"
                            alt="EMSI — École des Métiers du Son et de l'Image"
                            class="h-16 md:h-20 w-auto object-contain transition-transform duration-300 group-hover:scale-105 filter drop-shadow-[0_4px_20px_rgba(245,184,0,0.15)]">
                    @endif

                </a>


                {{-- Description --}}

                <p
                    class="
            mt-6

            max-w-sm

            text-sm
            leading-7

            text-white/45
        ">
                    École des Métiers du Son et de l'Image au cœur du Grand Théâtre National Doudou Ndiaye Rose.
                </p>


                {{-- Localisation --}}

                <div
                    class="
            mt-6

            flex
            items-center
            gap-2

            text-xs

            text-white/35
        ">

                    <x-lucide-map-pin
                        class="w-4 h-4 text-[#F5B800]" />

                    Dakar, Sénégal

                </div>

            </div>


            {{-- =================================================
                 NAVIGATION
            ================================================== --}}

            <div>

                <p
                    class="
                        text-[9px]
                        font-semibold
                        uppercase
                        tracking-[0.2em]

                        text-[#F5B800]
                    ">
                    Navigation
                </p>


                <nav
                    class="
                        mt-5

                        flex
                        flex-col
                        items-start

                        gap-3
                    ">

                    <a
                        href="{{ route('public.home') }}"
                        class="text-sm text-white/55 hover:text-[#F5B800] transition"
                    >
                        Accueil
                    </a>

                    <a
                        href="{{ route('public.about') }}"
                        class="text-sm text-white/55 hover:text-[#F5B800] transition"
                    >
                        L'école
                    </a>

                    <a
                        href="{{ route('public.courses.index') }}"
                        class="text-sm text-white/55 hover:text-[#F5B800] transition"
                    >
                        Formations
                    </a>

                    <a
                        href="{{ route('public.gallery.index') }}"
                        class="text-sm text-white/55 hover:text-[#F5B800] transition"
                    >
                        Galerie
                    </a>

                    <a
                        href="{{ route('public.news.index') }}"
                        class="text-sm text-white/55 hover:text-[#F5B800] transition"
                    >
                        Actualités
                    </a>

                </nav>

            </div>


            {{-- =================================================
                 CONTACT
            ================================================== --}}

            <div>

                <p
                    class="
                        text-[9px]
                        font-semibold
                        uppercase
                        tracking-[0.2em]

                        text-[#F5B800]
                    ">
                    Contact
                </p>


                <div
                    class="
                        mt-5

                        flex
                        flex-col
                        gap-3

                        text-sm
                        leading-6

                        text-white/50
                    ">

                    <div class="flex items-start gap-3">

                        <x-lucide-map-pin
                            class="
                                w-4
                                h-4

                                mt-1

                                flex-shrink-0

                                text-white/25
                            " />

                        <span>
                            {{ $siteSettings?->address ?? 'Grand Théâtre National Doudou Ndiaye Rose, Dakar, Sénégal' }}
                        </span>

                    </div>

                    @if($siteSettings?->phone)
                        <div class="flex items-center gap-3">
                            <x-lucide-phone class="w-4 h-4 flex-shrink-0 text-white/25" />
                            <a href="tel:{{ $siteSettings->phone }}" class="hover:text-white transition">
                                {{ $siteSettings->phone }}
                            </a>
                        </div>
                    @endif

                    @if($siteSettings?->email)
                        <div class="flex items-center gap-3">
                            <x-lucide-mail class="w-4 h-4 flex-shrink-0 text-white/25" />
                            <a href="mailto:{{ $siteSettings->email }}" class="hover:text-white transition">
                                {{ $siteSettings->email }}
                            </a>
                        </div>
                    @endif

                    @if($siteSettings?->facebook || $siteSettings?->instagram || $siteSettings?->youtube || $siteSettings?->whatsapp)
                        <div class="mt-2 pt-3 border-t border-white/10 flex items-center gap-3">
                            @if($siteSettings?->facebook)
                                <a href="{{ $siteSettings->facebook }}" target="_blank" rel="noopener noreferrer" class="text-white/40 hover:text-[#F5B800] transition" aria-label="Facebook">
                                    <x-lucide-facebook class="w-4 h-4" />
                                </a>
                            @endif
                            @if($siteSettings?->instagram)
                                <a href="{{ $siteSettings->instagram }}" target="_blank" rel="noopener noreferrer" class="text-white/40 hover:text-[#F5B800] transition" aria-label="Instagram">
                                    <x-lucide-instagram class="w-4 h-4" />
                                </a>
                            @endif
                            @if($siteSettings?->youtube)
                                <a href="{{ $siteSettings->youtube }}" target="_blank" rel="noopener noreferrer" class="text-white/40 hover:text-[#F5B800] transition" aria-label="YouTube">
                                    <x-lucide-youtube class="w-4 h-4" />
                                </a>
                            @endif
                            @if($siteSettings?->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings->whatsapp) }}" target="_blank" rel="noopener noreferrer" class="text-white/40 hover:text-[#F5B800] transition" aria-label="WhatsApp">
                                    <x-lucide-message-circle class="w-4 h-4" />
                                </a>
                            @endif
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         BAS DU FOOTER
    ====================================================== --}}

    <div class="border-t border-white/10">

        <div
            class="
                max-w-7xl
                mx-auto

                px-6
                lg:px-10

                py-5
            ">

            <div
                class="
                    flex
                    flex-col
                    sm:flex-row

                    items-start
                    sm:items-center

                    justify-between

                    gap-3
                ">

                <p
                    class="
                        text-[11px]
                        text-white/30
                    ">
                    © {{ date('Y') }} EMSI.
                    Tous droits réservés.
                </p>


                <p
                    class="
                        text-[11px]
                        text-white/25
                    ">
                    École de Formation Audiovisuelle
                </p>

            </div>

        </div>

    </div>

</footer>