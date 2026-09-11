@extends('layouts.public')

@section('title', 'Erreur serveur — 500 — EMSI')

@section('content')
<section class="min-h-[70vh] flex items-center justify-center py-20 px-6 bg-[#090A0D] text-white relative overflow-hidden">
    <div class="absolute w-[500px] h-[500px] bg-amber-500/10 rounded-full blur-[140px] pointer-events-none"></div>

    <div class="max-w-xl mx-auto text-center relative z-10">
        <span class="inline-flex items-center gap-2 px-4 py-1 rounded-full text-xs font-mono font-bold uppercase tracking-widest bg-amber-500/10 border border-amber-500/30 text-amber-400 mb-6">
            Erreur 500 • Incident Technique
        </span>

        <h1 class="text-5xl sm:text-6xl font-extrabold tracking-tight text-white mb-4">
            Interruption Technique
        </h1>

        <p class="text-base sm:text-lg text-white/60 leading-relaxed font-light mb-8">
            Une coupure momentanée s'est produite sur le serveur. Nos équipes techniques sont prévenues pour rétablir la situation dans les plus brefs délais.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <a
                href="{{ route('public.home') }}"
                class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-full bg-[#F5B800] hover:bg-white text-black font-bold text-xs uppercase tracking-wider transition duration-300 shadow-lg"
            >
                <x-lucide-home class="w-4 h-4" />
                <span>Retour à l'accueil</span>
            </a>
        </div>
    </div>
</section>
@endsection
