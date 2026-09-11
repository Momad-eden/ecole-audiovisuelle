<x-guest-layout>

    <div class="w-full max-w-[460px] rounded-3xl bg-[#101017]/90 backdrop-blur-2xl border border-white/10 shadow-[0_20px_60px_rgba(0,0,0,0.8)] p-7 sm:p-9 relative overflow-hidden">

        {{-- Lueur d'ambiance discrète interne à la carte --}}
        <div class="absolute -top-24 -right-24 w-48 h-48 rounded-full bg-[#F5B800]/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 rounded-full bg-[#310181]/20 blur-2xl pointer-events-none"></div>

        {{-- En-tête --}}
        <div class="text-center mb-6 relative z-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/25 text-[#F5B800] text-[11px] font-bold uppercase tracking-[0.2em] mb-4">
                <x-lucide-key-round class="w-3.5 h-3.5 text-[#F5B800]" />
                <span>Récupération de compte</span>
            </div>

            <h1 class="text-2xl font-bold font-sans text-white tracking-tight">
                Mot de passe oublié ?
            </h1>

            <p class="mt-2 text-xs sm:text-sm text-white/60 font-light leading-relaxed">
                Indiquez votre adresse email professionnelle et nous vous enverrons un lien sécurisé de réinitialisation.
            </p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-xs flex items-center gap-2.5">
                <x-lucide-check-circle-2 class="w-4 h-4 flex-shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

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

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5 relative z-10">
            @csrf

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
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="admin@emsi.sn"
                        class="w-full pl-10 pr-4 py-3 rounded-xl bg-white/[0.04] hover:bg-white/[0.06] focus:bg-white/[0.08] border border-white/10 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 text-white placeholder-white/25 text-sm transition duration-200 outline-none"
                    >
                </div>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="group w-full flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl bg-[#F5B800] hover:bg-white text-black font-bold text-sm uppercase tracking-wider transition-all duration-300 shadow-[0_8px_25px_rgba(245,184,0,0.35)] hover:shadow-[0_12px_35px_rgba(255,255,255,0.4)] cursor-pointer"
                >
                    <span>Envoyer le lien de réinitialisation</span>
                    <x-lucide-send class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </button>
            </div>

            <div class="text-center pt-3">
                <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center gap-1.5 text-xs text-white/60 hover:text-[#F5B800] transition-colors"
                >
                    <x-lucide-arrow-left class="w-3.5 h-3.5" />
                    <span>Retour à la page de connexion</span>
                </a>
            </div>
        </form>

    </div>

</x-guest-layout>
