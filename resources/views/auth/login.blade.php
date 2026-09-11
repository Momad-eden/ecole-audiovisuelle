<x-guest-layout>

    <div
        class="w-full max-w-[460px] rounded-3xl bg-[#101017]/90 backdrop-blur-2xl border border-white/10 shadow-[0_20px_60px_rgba(0,0,0,0.8)] p-7 sm:p-9 relative overflow-hidden"
        x-data="{ showPassword: false }"
    >

        {{-- Lueur d'ambiance discrète interne à la carte --}}
        <div class="absolute -top-24 -right-24 w-48 h-48 rounded-full bg-[#F5B800]/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 rounded-full bg-[#310181]/20 blur-2xl pointer-events-none"></div>

        {{-- =====================================================
             EN-TÊTE DE LA CARTE DE CONNEXION
        ====================================================== --}}
        <div class="text-center mb-7 relative z-10">

            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/25 text-[#F5B800] text-[11px] font-bold uppercase tracking-[0.2em] mb-4">
                <span class="w-1.5 h-1.5 rounded-full bg-[#F5B800] animate-pulse"></span>
                <span>Espace d'Administration</span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold font-sans text-white tracking-tight">
                Connexion Sécurisée
            </h1>

            <p class="mt-2 text-xs sm:text-sm text-white/60 font-light leading-relaxed">
                Portail de gestion pédagogique, financière et administrative de l'EMSI.
            </p>

        </div>

        {{-- =====================================================
             STATUT DE SESSION (Ex: Réinitialisation de mot de passe)
        ====================================================== --}}
        @if (session('status'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-xs flex items-center gap-2.5">
                <x-lucide-check-circle-2 class="w-4 h-4 flex-shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- =====================================================
             ALERTES D'ERREURS GLOBALES
        ====================================================== --}}
        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-red-500/10 border border-red-500/25 text-red-400 text-xs flex items-start gap-2.5">
                <x-lucide-alert-circle class="w-4 h-4 flex-shrink-0 mt-0.5" />
                <div class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif


        {{-- =====================================================
             FORMULAIRE DE CONNEXION
        ====================================================== --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-5 relative z-10">
            @csrf

            {{-- 1. ADRESSE EMAIL --}}
            <div class="space-y-1.5">
                <label for="email" class="block text-xs uppercase tracking-wider font-semibold text-white/80">
                    Adresse Email
                </label>

                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/40 group-focus-within:text-[#F5B800] transition-colors">
                        <x-lucide-mail class="w-4 h-4" />
                    </div>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="admin@emsi.sn"
                        class="w-full pl-10 pr-4 py-3 rounded-xl bg-white/[0.04] hover:bg-white/[0.06] focus:bg-white/[0.08] border border-white/10 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 text-white placeholder-white/25 text-sm transition duration-200 outline-none"
                    >
                </div>
            </div>


            {{-- 2. MOT DE PASSE --}}
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs uppercase tracking-wider font-semibold text-white/80">
                        Mot de passe
                    </label>

                    @if (Route::has('password.request'))
                        <a
                            href="{{ route('password.request') }}"
                            class="text-xs text-[#F5B800] hover:text-white transition-colors"
                        >
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/40 group-focus-within:text-[#F5B800] transition-colors">
                        <x-lucide-lock class="w-4 h-4" />
                    </div>

                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••••••"
                        class="w-full pl-10 pr-11 py-3 rounded-xl bg-white/[0.04] hover:bg-white/[0.06] focus:bg-white/[0.08] border border-white/10 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 text-white placeholder-white/25 text-sm transition duration-200 outline-none font-mono"
                    >

                    {{-- Bouton Afficher / Masquer Mot de passe --}}
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        aria-label="Afficher ou masquer le mot de passe"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-white/40 hover:text-white transition-colors focus:outline-none"
                    >
                        <template x-if="!showPassword">
                            <x-lucide-eye class="w-4 h-4" />
                        </template>
                        <template x-if="showPassword">
                            <x-lucide-eye-off class="w-4 h-4" />
                        </template>
                    </button>
                </div>
            </div>


            {{-- 3. SE SOUVENIR DE MOI --}}
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="rounded bg-white/10 border-white/20 text-[#F5B800] focus:ring-[#F5B800] focus:ring-offset-0 transition cursor-pointer"
                    >
                    <span class="text-xs text-white/70 hover:text-white transition-colors">
                        Rester connecté
                    </span>
                </label>
            </div>


            {{-- 4. BOUTON DE CONNEXION PRINCIPAL --}}
            <div class="pt-2">
                <button
                    type="submit"
                    class="group w-full flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl bg-[#F5B800] hover:bg-white text-black font-bold text-sm uppercase tracking-wider transition-all duration-300 shadow-[0_8px_25px_rgba(245,184,0,0.35)] hover:shadow-[0_12px_35px_rgba(255,255,255,0.4)] cursor-pointer"
                >
                    <span>Accéder à l'espace admin</span>
                    <x-lucide-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </button>
            </div>

        </form>

        {{-- =====================================================
             BADGE DE SÉCURITÉ & ROLES AUTORISÉS
        ====================================================== --}}
        <div class="mt-8 pt-5 border-t border-white/10 text-center relative z-10">
            <div class="inline-flex items-center gap-1.5 text-[11px] text-white/45 font-medium">
                <x-lucide-shield-check class="w-3.5 h-3.5 text-emerald-400" />
                <span>Connexion sécurisée SSL 256-bit • Accès réservé</span>
            </div>
            <div class="mt-2 flex items-center justify-center gap-2 text-[10px] text-white/35">
                <span>Directeur</span>
                <span>•</span>
                <span>Gestionnaire</span>
                <span>•</span>
                <span>Communication</span>
                <span>•</span>
                <span>Formateur</span>
            </div>
        </div>

    </div>

</x-guest-layout>
