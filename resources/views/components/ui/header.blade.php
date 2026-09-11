<header class="h-20 bg-white border-b border-gray-200/80 flex items-center justify-between px-6 lg:px-8 shrink-0">

    <!-- Partie gauche : Titre de page dynamique -->
    <div>
        <h2 class="text-xl lg:text-2xl font-bold font-sans text-gray-900 leading-tight">
            @yield('header_title', 'Tableau de bord')
        </h2>

        <p class="text-xs lg:text-sm text-gray-500 font-medium">
            @yield('header_subtitle', 'Administration générale • EMSI × Grand Théâtre National')
        </p>
    </div>

    <!-- Partie droite : Actions, Voir le site & Profil -->
    <div class="flex items-center gap-3 sm:gap-4">

        {{-- Bouton Voir le site public --}}
        <a
            href="{{ route('public.home') }}"
            target="_blank"
            class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gray-50 hover:bg-[#F5B800]/10 hover:text-[#C15C03] border border-gray-200 text-xs font-semibold text-gray-700 transition"
        >
            <x-lucide-external-link class="w-3.5 h-3.5" />
            <span>Voir le site</span>
        </a>

        {{-- Profil Utilisateur & Rôle --}}
        <div class="flex items-center gap-3 pl-2 sm:pl-4 border-l border-gray-200">

            <a
                href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 group focus:outline-none"
            >
                <div class="w-10 h-10 rounded-xl bg-[#310181] text-white flex items-center justify-center font-bold text-sm shadow-sm group-hover:bg-[#C15C03] transition">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>

                <div class="hidden md:block text-left">
                    <div class="font-bold text-sm text-gray-900 group-hover:text-[#C15C03] transition">
                        {{ Auth::user()->name }}
                    </div>

                    <div class="inline-flex items-center gap-1 text-[11px] font-semibold text-gray-500 capitalize">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>{{ Auth::user()->role_label ?? Auth::user()->role }}</span>
                    </div>
                </div>
            </a>

        </div>

    </div>

</header>