<x-guest-layout>

    <div
        class="w-full max-w-[460px] rounded-3xl bg-[#101017]/90 backdrop-blur-2xl border border-white/10 shadow-[0_20px_60px_rgba(0,0,0,0.8)] p-7 sm:p-9 relative overflow-hidden"
        x-data="{ showPassword: false, showConfirmPassword: false }"
    >

        {{-- Lueur d'ambiance discrète interne à la carte --}}
        <div class="absolute -top-24 -right-24 w-48 h-48 rounded-full bg-[#F5B800]/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 rounded-full bg-[#310181]/20 blur-2xl pointer-events-none"></div>

        <div class="text-center mb-6 relative z-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/25 text-[#F5B800] text-[11px] font-bold uppercase tracking-[0.2em] mb-4">
                <x-lucide-shield-check class="w-3.5 h-3.5 text-[#F5B800]" />
                <span>Nouveau mot de passe</span>
            </div>

            <h1 class="text-2xl font-bold font-sans text-white tracking-tight">
                Réinitialisation
            </h1>

            <p class="mt-2 text-xs sm:text-sm text-white/60 font-light leading-relaxed">
                Définissez votre nouveau mot de passe sécurisé.
            </p>
        </div>

        {{-- Alertes d'erreurs --}}
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

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5 relative z-10">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
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
                        value="{{ old('email', $request->email) }}"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full pl-10 pr-4 py-3 rounded-xl bg-white/[0.04] hover:bg-white/[0.06] focus:bg-white/[0.08] border border-white/10 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 text-white placeholder-white/25 text-sm transition duration-200 outline-none"
                    >
                </div>
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs uppercase tracking-wider font-semibold text-white/80">
                    Nouveau Mot de passe
                </label>

                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/40 group-focus-within:text-[#F5B800] transition-colors">
                        <x-lucide-lock class="w-4 h-4" />
                    </div>

                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••••••"
                        class="w-full pl-10 pr-11 py-3 rounded-xl bg-white/[0.04] hover:bg-white/[0.06] focus:bg-white/[0.08] border border-white/10 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 text-white placeholder-white/25 text-sm transition duration-200 outline-none font-mono"
                    >

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

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs uppercase tracking-wider font-semibold text-white/80">
                    Confirmer le Mot de passe
                </label>

                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/40 group-focus-within:text-[#F5B800] transition-colors">
                        <x-lucide-lock class="w-4 h-4" />
                    </div>

                    <input
                        id="password_confirmation"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••••••"
                        class="w-full pl-10 pr-11 py-3 rounded-xl bg-white/[0.04] hover:bg-white/[0.06] focus:bg-white/[0.08] border border-white/10 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 text-white placeholder-white/25 text-sm transition duration-200 outline-none font-mono"
                    >

                    <button
                        type="button"
                        @click="showConfirmPassword = !showConfirmPassword"
                        aria-label="Afficher ou masquer la confirmation du mot de passe"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-white/40 hover:text-white transition-colors focus:outline-none"
                    >
                        <template x-if="!showConfirmPassword">
                            <x-lucide-eye class="w-4 h-4" />
                        </template>
                        <template x-if="showConfirmPassword">
                            <x-lucide-eye-off class="w-4 h-4" />
                        </template>
                    </button>
                </div>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="group w-full flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl bg-[#F5B800] hover:bg-white text-black font-bold text-sm uppercase tracking-wider transition-all duration-300 shadow-[0_8px_25px_rgba(245,184,0,0.35)] hover:shadow-[0_12px_35px_rgba(255,255,255,0.4)] cursor-pointer"
                >
                    <span>Enregistrer le nouveau mot de passe</span>
                    <x-lucide-check class="w-4 h-4 group-hover:scale-110 transition-transform" />
                </button>
            </div>
        </form>

    </div>

</x-guest-layout>
