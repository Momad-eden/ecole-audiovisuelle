<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EMSI') }} — Espace Administration</title>

    <!-- Favicon EMSI -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Polices Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#070709] text-white min-h-screen flex flex-col justify-between selection:bg-[#F5B800] selection:text-black">

    {{-- =========================================================
         ATMOSPHÈRE & LUEURS CINÉMATIQUES DE FOND
    ========================================================= --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        {{-- Lueur ambrée dorée --}}
        <div class="absolute top-[10%] right-[20%] w-[500px] lg:w-[650px] h-[500px] lg:h-[650px] rounded-full bg-[#F5B800]/12 blur-[140px]"></div>

        {{-- Lueur violette studio EMSI --}}
        <div class="absolute bottom-[15%] left-[15%] w-[550px] lg:w-[700px] h-[550px] lg:h-[700px] rounded-full bg-[#310181]/30 blur-[160px]"></div>

        {{-- Trame studio & vignettage --}}
        <div class="absolute inset-0 bg-gradient-to-t from-[#070709] via-transparent to-[#070709]/80"></div>
        <div class="absolute inset-0 opacity-[0.03] bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:24px_24px]"></div>
    </div>

    {{-- =========================================================
         BARRE SUPÉRIEURE DE NAVIGATION
    ========================================================= --}}
    <header class="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
        <a
            href="{{ route('public.home') }}"
            class="group inline-flex items-center gap-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B800] rounded-xl"
        >
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#F5B800] to-[#C15C03] flex items-center justify-center shadow-[0_4px_16px_rgba(245,184,0,0.3)] group-hover:scale-105 transition-transform duration-300">
                <x-lucide-clapperboard class="w-5 h-5 text-black" />
            </div>
            <div class="flex flex-col">
                <span class="font-extrabold text-base tracking-wider text-white group-hover:text-[#F5B800] transition">EMSI</span>
                <span class="text-[9px] uppercase tracking-[0.2em] text-white/50">Dakar • Grand Théâtre</span>
            </div>
        </a>

        <a
            href="{{ route('public.home') }}"
            class="inline-flex items-center gap-2 text-xs font-medium text-white/60 hover:text-white px-3.5 py-1.5 rounded-full bg-white/[0.04] hover:bg-white/[0.08] border border-white/10 transition backdrop-blur-md"
        >
            <x-lucide-arrow-left class="w-3.5 h-3.5" />
            <span>Retour au site public</span>
        </a>
    </header>

    {{-- =========================================================
         ZONE CENTRALE : CONTENU DE LA PAGE AUTH (SLOT)
    ========================================================= --}}
    <main class="relative z-10 flex-1 flex flex-col justify-center items-center px-4 sm:px-6 py-8">
        {{ $slot }}
    </main>

    {{-- =========================================================
         PIED DE PAGE AUTHENTIFICATION
    ========================================================= --}}
    <footer class="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 text-center text-xs text-white/40 border-t border-white/[0.06]">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span>Portail Sécurisé EMSI • Grand Théâtre National de Dakar</span>
            </div>
            <div>
                © {{ date('Y') }} EMSI. Tous droits réservés.
            </div>
        </div>
    </footer>

</body>
</html>
