@extends('layouts.public')

@section('title', 'Candidature reçue avec succès — EMSI Dakar')

@section('content')

<section class="min-h-[85vh] bg-[#080808] text-white flex items-center justify-center px-6 py-24 relative overflow-hidden">

    {{-- Lueur d'ambiance --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[#320080]/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-2xl w-full text-center relative z-10">

        {{-- Badge Statut --}}
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#F5B800]/10 text-[#F5B800] border border-[#F5B800]/30 mb-8 shadow-[0_0_30px_rgba(245,184,0,0.2)]">
            <x-lucide-check-circle-2 class="w-8 h-8" />
        </div>

        <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#F5B800] mb-3">
            Dossier transmis avec succès
        </p>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal tracking-tight text-white leading-tight mb-6">
            Merci @if(session('candidate_name')) <span class="text-[#F5B800]">{{ session('candidate_name') }}</span> @endif !
        </h1>

        <p class="text-base text-white/70 leading-relaxed max-w-lg mx-auto mb-10">
            Votre demande d'admission a bien été enregistrée. Notre commission pédagogique va étudier votre profil et prendra contact avec vous sous <strong class="text-white">48 heures</strong> pour convenir de votre entretien d'orientation.
        </p>

        {{-- Prochaines étapes --}}
        <div class="bg-white/[0.04] border border-white/10 rounded-2xl p-6 mb-10 text-left">
            <h3 class="text-xs font-bold uppercase tracking-wider text-white/60 mb-4 flex items-center gap-2">
                <x-lucide-sparkles class="w-4 h-4 text-[#F5B800]" />
                Ce qui va se passer maintenant :
            </h3>
            <div class="space-y-3 text-xs sm:text-sm text-white/80">
                <div class="flex items-start gap-3">
                    <span class="w-5 h-5 rounded-full bg-[#F5B800] text-black font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                    <span>Un conseiller pédagogique examine vos informations.</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-5 h-5 rounded-full bg-white/20 text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                    <span>Vous recevrez un appel ou message WhatsApp pour planifier votre entretien.</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-5 h-5 rounded-full bg-white/20 text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                    <span>Visite des studios au Grand Théâtre National et finalisation de votre inscription.</span>
                </div>
            </div>
        </div>

        {{-- Boutons d'action --}}
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a
                href="{{ route('public.home') }}"
                class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full bg-[#F5B800] text-black font-bold text-xs uppercase tracking-wider hover:bg-white transition shadow-lg"
            >
                <span>Retour à l'accueil</span>
                <x-lucide-arrow-right class="w-4 h-4" />
            </a>

            <a
                href="{{ route('public.courses.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-white/[0.08] text-white hover:bg-white/20 border border-white/15 text-xs font-semibold uppercase tracking-wider transition"
            >
                <span>Explorer les formations</span>
            </a>
        </div>

    </div>

</section>

@endsection