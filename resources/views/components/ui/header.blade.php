<header class="h-20 bg-white border-b border-border flex items-center justify-between px-8">

    <!-- Partie gauche -->
    <div>
        <h2 class="text-2xl font-semibold text-gray-900">
            Tableau de bord
        </h2>

        <p class="text-sm text-muted">
            Bienvenue dans l'administration EMSI.
        </p>
    </div>

    <!-- Partie droite -->
    <div class="flex items-center gap-5">

        <!-- Recherche -->
        <div class="relative">

            <x-lucide-search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"/>

            <input
                type="text"
                placeholder="Rechercher..."
                class="w-80 rounded-xl border border-border bg-gray-50 pl-12 pr-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition"/>

        </div>

        <!-- Notifications -->
        <button
            class="w-11 h-11 rounded-xl border border-border flex items-center justify-center hover:bg-gray-100 transition">

            <x-lucide-bell class="w-5 h-5"/>

        </button>

        <!-- Utilisateur -->
        <div class="flex items-center gap-3">

            <div class="w-11 h-11 rounded-full bg-primary text-white flex items-center justify-center font-semibold">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>

            <div class="hidden md:block">

                <div class="font-semibold">
                    {{ Auth::user()->name }}
                </div>

                <div class="text-sm text-muted">
                    Administrateur
                </div>

            </div>

        </div>

    </div>

</header>