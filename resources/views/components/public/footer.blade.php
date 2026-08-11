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

                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="EMSI — École de Formation Audiovisuelle"
                        class="h-14 w-auto object-contain">

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
                    École de formation audiovisuelle
                    au cœur du Grand Théâtre National
                    Doudou Ndiaye Rose.
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
                        href="{{ url('/') }}"
                        class="
                            text-sm
                            text-white/55

                            hover:text-white

                            transition
                        ">
                        Accueil
                    </a>


                    <a
                        href="{{ url('/#ecole') }}"
                        class="
                            text-sm
                            text-white/55

                            hover:text-white

                            transition
                        ">
                        L'école
                    </a>


                    <a
                        href="{{ url('/#formations') }}"
                        class="
                            text-sm
                            text-white/55

                            hover:text-white

                            transition
                        ">
                        Formations
                    </a>


                    <a
                        href="{{ url('/#galerie') }}"
                        class="
                            text-sm
                            text-white/55

                            hover:text-white

                            transition
                        ">
                        Galerie
                    </a>


                    <a
                        href="{{ url('/#actualites') }}"
                        class="
                            text-sm
                            text-white/55

                            hover:text-white

                            transition
                        ">
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
                            Grand Théâtre National<br>
                            Doudou Ndiaye Rose<br>
                            Dakar, Sénégal
                        </span>

                    </div>


                    {{-- À compléter lorsque les informations
                         seront gérées depuis l'administration --}}

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