@extends('layouts.public')

@section('title', 'Page non trouvée — 404 — EMSI')

@section('content')
<section class="min-h-[70vh] flex items-center justify-center py-20 px-6 bg-[#090A0D] text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(#F5B800_1px,transparent_1px)] [background-size:24px_24px] opacity-10"></div>
    <div class="absolute w-[500px] h-[500px] bg-[#F5B800]/10 rounded-full blur-[140px] pointer-events-none"></div>

    <div class="max-w-xl mx-auto text-center relative z-10">
        <span class="inline-flex items-center gap-2 px-4 py-1 rounded-full text-xs font-mono font-bold uppercase tracking-widest bg-[#F5B800]/10 border border-[#F5B800]/30 text-[#F5B800] mb-6">
            Erreur 404
        </span>

        <h1 class="text-6xl sm:text-7xl font-extrabold tracking-tight text-white mb-4">
            Plan Hors-Champ
        </h1>

        <p class="text-base sm:text-lg text-white/60 leading-relaxed font-light mb-8">
            La page ou la scène que vous recherchez semble introuvable ou a été déplacée dans nos archives de production.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <a
                href="{{ route('public.home') }}"
                class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-full bg-[#F5B800] hover:bg-white text-black font-bold text-xs uppercase tracking-wider transition duration-300 shadow-lg shadow-[#F5B800]/20"
            >
                <x-lucide-home class="w-4 h-4" />
                <span>Retour à l'accueil</span>
            </a>

            <a
                href="{{ route('public.courses.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-white/5 hover:bg-white/10 text-white text-xs font-semibold uppercase tracking-wider transition border border-white/10"
            >
                <span>Voir les formations</span>
            </a>
        </div>
    </div>
</section>
@endsection
