@extends('layouts.public')

@section('title', 'Le Projet Officiel — EMSI & Grand Théâtre National de Dakar')
@section('description', 'Projet Intégré de Formation et de Certification par la VAE dans les Métiers Techniques de l’Audiovisuel et de l’Événementiel. 100 jeunes, 5 filières, 2 volets.')

@section('content')

{{-- =========================================================
     1. HERO SECTION — LE PROJET OFFICIEL
========================================================= --}}
<section class="relative pt-16 pb-20 lg:pt-24 lg:pb-28 bg-[#080808] text-white overflow-hidden border-b border-white/10">

    {{-- Lueurs d'ambiance cinématiques --}}
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#320080]/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 w-[550px] h-[550px] bg-[#F5B800]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-white/50 mb-6">
            <a href="{{ route('public.home') }}" class="hover:text-white transition-colors">Accueil</a>
            <span class="text-white/20">/</span>
            <span class="text-[#F5B800]">Le Projet</span>
        </div>

        <div class="grid lg:grid-cols-12 gap-10 lg:gap-14 items-center">

            <div class="lg:col-span-8">

                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#F5B800]/10 text-[#F5B800] border border-[#F5B800]/30 mb-6 backdrop-blur-md">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#F5B800] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#F5B800]"></span>
                    </span>
                    Programme Officiel 2026 — 2027 · EMSI & Grand Théâtre National
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-serif font-normal tracking-tight text-white leading-[1.08] mb-6">
                    Former & certifier les talents de l'audiovisuel et de l'événementiel.
                </h1>

                <p class="text-base sm:text-lg text-white/75 leading-relaxed font-light max-w-2xl">
                    Le <strong class="text-white font-semibold">Projet Intégré de Formation et de Certification par la VAE</strong>, co-porté par l'<strong>EMSI</strong> et le <strong>Grand Théâtre National Doudou Ndiaye Coumba Rose</strong>, répond aux exigences des méga-événements culturels et sportifs en ouvrant une voie d'ascension diplômante de niveau BTS sans obligation du Baccalauréat.
                </p>

                {{-- Boutons d'action --}}
                <div class="flex flex-wrap items-center gap-4 mt-8">
                    <a
                        href="{{ route('public.admissions.create') }}"
                        class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-full bg-[#F5B800] hover:bg-white text-black font-bold text-xs sm:text-sm uppercase tracking-wider transition-all duration-200 shadow-[0_0_25px_rgba(245,184,0,0.35)]"
                    >
                        <span>Candidater au programme</span>
                        <x-lucide-arrow-up-right class="w-4 h-4 stroke-[2.5]" />
                    </a>

                    <a
                        href="{{ route('public.vae') }}"
                        class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-full bg-white/[0.05] hover:bg-white/[0.12] border border-white/20 text-white font-semibold text-xs sm:text-sm transition-all duration-200"
                    >
                        <span>Comprendre la VAE</span>
                        <x-lucide-arrow-right class="w-4 h-4 text-white/60" />
                    </a>
                </div>

            </div>

            {{-- Carte Résumé Visuelle --}}
            <div class="lg:col-span-4">
                <div class="relative rounded-3xl bg-gradient-to-b from-white/[0.08] to-white/[0.02] border border-white/15 p-7 shadow-2xl backdrop-blur-xl">
                    <div class="text-[#F5B800] text-xs font-bold uppercase tracking-[0.2em] mb-4">
                        Chiffres Clés du Projet
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-white/10">
                            <span class="text-sm text-white/70">Bénéficiaires formés</span>
                            <span class="text-xl font-bold font-serif text-[#F5B800]">100 Jeunes</span>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-white/10">
                            <span class="text-sm text-white/70">Filières techniques</span>
                            <span class="text-xl font-bold font-serif text-white">5 Spécialités</span>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-white/10">
                            <span class="text-sm text-white/70">Volet 1 (Intensif)</span>
                            <span class="text-sm font-semibold text-white">40 Jeunes · 3 mois</span>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-white/10">
                            <span class="text-sm text-white/70">Volet 2 (BTS VAE)</span>
                            <span class="text-sm font-semibold text-white">60 Jeunes · 9 mois</span>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <span class="text-sm text-white/70">Immersion réelle</span>
                            <span class="text-sm font-semibold text-[#F5B800]">90% Pratique</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     2. CONTEXTE ET JUSTIFICATION DU PROJET
========================================================= --}}
<section class="py-20 lg:py-28 bg-[#0d0d10] text-white border-b border-white/10">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="max-w-3xl mb-16">
            <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.25em] text-[#F5B800] mb-3">
                <span class="w-2 h-2 rounded-full bg-[#F5B800]"></span>
                Contexte & Vision Stratégique
            </p>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal tracking-tight text-white leading-tight">
                Une bascule historique pour l'audiovisuel et l'événementiel au Sénégal.
            </h2>
            <p class="mt-5 text-base sm:text-lg text-white/70 leading-relaxed font-light">
                Les années 2026 et 2027 marquent un tournant majeur pour le Sénégal avec l'accueil de festivals internationaux, d'événements culturels d'envergure et des Jeux Olympiques de la Jeunesse Dakar 2026. Ce projet unifié relève trois défis majeurs :
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">

            {{-- Défi 1 : Excellence Technique --}}
            <div class="p-8 rounded-3xl bg-[#141418] border border-white/10 hover:border-[#F5B800]/40 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-2xl bg-[#F5B800]/10 border border-[#F5B800]/25 flex items-center justify-center text-[#F5B800] mb-6 group-hover:bg-[#F5B800] group-hover:text-black transition-colors">
                    <x-lucide-zap class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Excellence Technique</h3>
                <p class="text-sm text-white/65 leading-relaxed">
                    Combler le déficit structurel de techniciens capables de maîtriser l'ingénierie son (Line Array, Dante), la lumière asservie (GrandMA), la vidéo broadcast et le management technique d'événements majeurs.
                </p>
            </div>

            {{-- Défi 2 : Révolution Sociale & VAE --}}
            <div class="p-8 rounded-3xl bg-[#141418] border border-white/10 hover:border-[#F5B800]/40 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-2xl bg-[#320080]/40 border border-[#320080] flex items-center justify-center text-[#F5B800] mb-6 group-hover:bg-[#F5B800] group-hover:text-black transition-colors">
                    <x-lucide-award class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Ascension Sociale & VAE</h3>
                <p class="text-sm text-white/65 leading-relaxed">
                    Briser le plafond de verre académique du Baccalauréat pour les titulaires du CPS en leur permettant d'accéder au niveau BTS (Bac+2) par la Validation des Acquis de l'Expérience reconnue par l'État.
                </p>
            </div>

            {{-- Défi 3 : Souveraineté & Ancrage --}}
            <div class="p-8 rounded-3xl bg-[#141418] border border-white/10 hover:border-[#F5B800]/40 transition-all duration-300 group">
                <div class="w-12 h-12 rounded-2xl bg-[#F5B800]/10 border border-[#F5B800]/25 flex items-center justify-center text-[#F5B800] mb-6 group-hover:bg-[#F5B800] group-hover:text-black transition-colors">
                    <x-lucide-shield-check class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Souveraineté Nationale</h3>
                <p class="text-sm text-white/65 leading-relaxed">
                    Former une élite locale hautement qualifiée afin de réduire la dépendance aux expertises étrangères lors des méga-événements et pérenniser une filière créative autonome et compétitive.
                </p>
            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     3. LES DEUX VOLETS COMPLÉMENTAIRES
========================================================= --}}
<section class="py-24 lg:py-32 bg-[#08080a] text-white border-b border-white/10 relative overflow-hidden">

    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-[#320080]/20 rounded-full blur-[160px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10">

        <div class="text-center max-w-3xl mx-auto mb-16">
            <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.25em] text-[#F5B800] mb-3">
                <span class="w-2 h-2 rounded-full bg-[#F5B800]"></span>
                Architecture du Projet
            </p>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal tracking-tight text-white leading-tight">
                Deux volets complémentaires et séquentiels
            </h2>
            <p class="mt-4 text-base text-white/60 leading-relaxed font-light">
                Le Volet 1 répond à l'urgence opérationnelle des événements, tandis que le Volet 2 assure la montée en compétence diplômante à moyen terme.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-8 lg:gap-10">

            {{-- CARTE VOLET 1 --}}
            <div class="rounded-3xl bg-[#111116] border border-white/15 p-8 sm:p-10 flex flex-col justify-between relative overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 px-5 py-2 rounded-bl-2xl bg-[#F5B800]/15 border-b border-l border-[#F5B800]/30 text-[#F5B800] text-xs font-bold uppercase tracking-wider">
                    Septembre — Novembre 2026
                </div>

                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#F5B800]/10 text-[#F5B800] text-xs font-bold mb-4">
                        Volet 1
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-serif font-bold text-white mb-4">
                        Perfectionnement Intensif Pré-Événementiel
                    </h3>
                    <p class="text-sm text-white/70 leading-relaxed mb-8">
                        Camp de haute performance destiné à perfectionner 40 jeunes techniciens aux standards internationaux sur des équipements de pointe avant les grands rendez-vous événementiels.
                    </p>

                    {{-- Spécifications clés --}}
                    <div class="grid grid-cols-2 gap-4 pb-8 border-b border-white/10 text-xs">
                        <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/5">
                            <span class="text-white/50 block mb-1">Effectif ciblé</span>
                            <span class="text-base font-bold text-[#F5B800]">40 Jeunes</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/5">
                            <span class="text-white/50 block mb-1">Durée du cycle</span>
                            <span class="text-base font-bold text-white">3 mois · 12 semaines</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/5">
                            <span class="text-white/50 block mb-1">Volume horaire</span>
                            <span class="text-base font-bold text-white">~360 Heures</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/5">
                            <span class="text-white/50 block mb-1">Format pédagogique</span>
                            <span class="text-base font-bold text-[#F5B800]">10% Théorie · 90% Pratique</span>
                        </div>
                    </div>

                    {{-- Livrables et Certification --}}
                    <div class="pt-6 space-y-2.5 text-xs text-white/75">
                        <div class="flex items-center gap-2">
                            <x-lucide-check class="w-4 h-4 text-[#F5B800] shrink-0" />
                            <span>Diplôme d'École en partenariat avec la Direction des Concours</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-lucide-check class="w-4 h-4 text-[#F5B800] shrink-0" />
                            <span>Certificat de Compétences Techniques Avancées EMSI / Grand Théâtre</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-white/10">
                    <a
                        href="{{ route('public.admissions.create') }}"
                        class="flex items-center justify-center gap-2 w-full py-3.5 px-6 rounded-xl bg-white/[0.08] hover:bg-[#F5B800] text-white hover:text-black font-bold text-xs uppercase tracking-wider transition-all duration-200"
                    >
                        <span>Postuler au Volet 1</span>
                        <x-lucide-arrow-up-right class="w-4 h-4" />
                    </a>
                </div>
            </div>

            {{-- CARTE VOLET 2 --}}
            <div class="rounded-3xl bg-gradient-to-b from-[#181224] to-[#0f0b18] border border-[#320080]/60 p-8 sm:p-10 flex flex-col justify-between relative overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 px-5 py-2 rounded-bl-2xl bg-[#320080]/50 border-b border-l border-[#320080] text-[#F5B800] text-xs font-bold uppercase tracking-wider">
                    Démarrage 2027
                </div>

                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#320080] text-[#F5B800] text-xs font-bold mb-4">
                        Volet 2 — VAE Diplômante
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-serif font-bold text-white mb-4">
                        Cycle de Certification BTS par la VAE
                    </h3>
                    <p class="text-sm text-white/70 leading-relaxed mb-8">
                        Parcours complet d'ascension professionnelle permettant à 60 jeunes titulaires du CPS d'obtenir un diplôme officiel d'État de niveau BTS (Bac+2) via la Validation des Acquis de l'Expérience.
                    </p>

                    {{-- Spécifications clés --}}
                    <div class="grid grid-cols-2 gap-4 pb-8 border-b border-white/10 text-xs">
                        <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/5">
                            <span class="text-white/50 block mb-1">Effectif ciblé</span>
                            <span class="text-base font-bold text-[#F5B800]">60 Jeunes</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/5">
                            <span class="text-white/50 block mb-1">Durée du cycle</span>
                            <span class="text-base font-bold text-white">9 mois d'immersion</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/5">
                            <span class="text-white/50 block mb-1">Volume horaire</span>
                            <span class="text-base font-bold text-white">1 080 Heures (30h/semaine)</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/5">
                            <span class="text-white/50 block mb-1">Format pédagogique</span>
                            <span class="text-base font-bold text-[#F5B800]">Alternance École / Entreprises</span>
                        </div>
                    </div>

                    {{-- Livrables et Certification --}}
                    <div class="pt-6 space-y-2.5 text-xs text-white/75">
                        <div class="flex items-center gap-2">
                            <x-lucide-check class="w-4 h-4 text-[#F5B800] shrink-0" />
                            <span>Constitution et évaluation du Livret VAE individualisé</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-lucide-check class="w-4 h-4 text-[#F5B800] shrink-0" />
                            <span>Soutenance devant Jury Professionnel & Titre officiel de niveau BTS (Bac+2)</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-white/10">
                    <a
                        href="{{ route('public.vae') }}"
                        class="flex items-center justify-center gap-2 w-full py-3.5 px-6 rounded-xl bg-[#F5B800] hover:bg-white text-black font-bold text-xs uppercase tracking-wider transition-all duration-200"
                    >
                        <span>Découvrir le parcours BTS-VAE</span>
                        <x-lucide-arrow-right class="w-4 h-4" />
                    </a>
                </div>
            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     4. LES CINQ FILIÈRES D'EXCELLENCE
========================================================= --}}
<section class="py-24 lg:py-32 bg-[#0c0c0e] text-white border-b border-white/10">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-12 border-b border-white/10">
            <div>
                <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.25em] text-[#F5B800] mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#F5B800]"></span>
                    Filières Techniques
                </p>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal tracking-tight text-white">
                    Les 5 filières d'excellence du projet
                </h2>
            </div>
            <a
                href="{{ route('public.courses.index') }}"
                class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#F5B800] hover:text-white transition-colors shrink-0"
            >
                <span>Voir le catalogue complet</span>
                <x-lucide-arrow-right class="w-4 h-4" />
            </a>
        </div>

        <div class="mt-12 grid md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach($courses as $course)
                <div class="rounded-3xl bg-[#141418] border border-white/10 hover:border-[#F5B800]/50 p-7 flex flex-col justify-between transition-all duration-300 group hover:-translate-y-1">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-5">
                            <span class="px-3 py-1 rounded-full bg-white/[0.06] border border-white/10 text-[11px] font-semibold text-[#F5B800]">
                                {{ $course->category ?? 'Filière d\'excellence' }}
                            </span>
                            <span class="text-xs text-white/40 font-medium">
                                {{ $course->duration ?? '3 à 9 mois' }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-white group-hover:text-[#F5B800] transition-colors mb-3">
                            {{ $course->title }}
                        </h3>

                        <p class="text-xs text-white/65 leading-relaxed line-clamp-3 mb-6">
                            {{ $course->description }}
                        </p>
                    </div>

                    <div class="pt-5 border-t border-white/10 flex items-center justify-between">
                        <a
                            href="{{ route('public.courses.show', $course->slug) }}"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-white group-hover:text-[#F5B800] transition-colors"
                        >
                            <span>Fiche filière & compétences</span>
                            <x-lucide-chevron-right class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                        </a>

                        <a
                            href="{{ route('public.admissions.create') }}?course={{ $course->id }}"
                            class="w-8 h-8 rounded-full bg-white/[0.06] hover:bg-[#F5B800] text-white hover:text-black flex items-center justify-center transition-colors"
                            title="Postuler à cette filière"
                        >
                            <x-lucide-arrow-up-right class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            @endforeach

        </div>

    </div>

</section>


{{-- =========================================================
     5. CALENDRIER D'EXÉCUTION DU PROJET (2026 - 2027)
========================================================= --}}
<section class="py-24 lg:py-32 bg-[#08080a] text-white border-b border-white/10">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="max-w-3xl mb-16">
            <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.25em] text-[#F5B800] mb-3">
                <span class="w-2 h-2 rounded-full bg-[#F5B800]"></span>
                Planning & Déroulement
            </p>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal tracking-tight text-white leading-tight">
                Calendrier d'exécution 2026 — 2027
            </h2>
            <p class="mt-4 text-base text-white/65 leading-relaxed font-light">
                Un phasage rigoureux sur 18 mois articulant préparation, exécution intensive, alternance, soutenances de jurys et pérennisation.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-10">

            {{-- ANNÉE 2026 --}}
            <div class="rounded-3xl bg-[#121217] border border-white/10 p-8">
                <div class="flex items-center gap-3 pb-6 border-b border-white/10 mb-6">
                    <span class="px-3.5 py-1.5 rounded-xl bg-[#F5B800] text-black font-bold text-sm">
                        2026
                    </span>
                    <h3 class="text-xl font-bold text-white">
                        Phase 1 : Recrutement & Volet 1 Intensif
                    </h3>
                </div>

                <div class="space-y-6 relative before:absolute before:inset-0 before:left-3 before:w-0.5 before:bg-white/10">

                    {{-- Étape 1 --}}
                    <div class="relative flex items-start gap-5 pl-8">
                        <div class="absolute left-1.5 top-1.5 w-3.5 h-3.5 rounded-full bg-[#F5B800] ring-4 ring-[#121217]"></div>
                        <div>
                            <span class="text-xs font-bold text-[#F5B800] uppercase tracking-wider">Juin → Août 2026</span>
                            <h4 class="text-sm font-semibold text-white mt-1">Recrutement et Préparation</h4>
                            <p class="text-xs text-white/60 mt-1">Sélection des 40 bénéficiaires du Volet 1, mise à disposition des équipements et cadrage pédagogique.</p>
                        </div>
                    </div>

                    {{-- Étape 2 --}}
                    <div class="relative flex items-start gap-5 pl-8">
                        <div class="absolute left-1.5 top-1.5 w-3.5 h-3.5 rounded-full bg-[#F5B800] ring-4 ring-[#121217]"></div>
                        <div>
                            <span class="text-xs font-bold text-[#F5B800] uppercase tracking-wider">Septembre → Novembre 2026</span>
                            <h4 class="text-sm font-semibold text-white mt-1">Volet 1 — Formation Intensive (12 semaines)</h4>
                            <p class="text-xs text-white/60 mt-1">Camp pratique au Grand Théâtre National, calage Line Array, GrandMA, régie vidéo et mise en situation réelle.</p>
                        </div>
                    </div>

                    {{-- Étape 3 --}}
                    <div class="relative flex items-start gap-5 pl-8">
                        <div class="absolute left-1.5 top-1.5 w-3.5 h-3.5 rounded-full bg-[#F5B800] ring-4 ring-[#121217]"></div>
                        <div>
                            <span class="text-xs font-bold text-[#F5B800] uppercase tracking-wider">Novembre 2026</span>
                            <h4 class="text-sm font-semibold text-white mt-1">Évaluation & Certification Volet 1</h4>
                            <p class="text-xs text-white/60 mt-1">Délivrance du Diplôme d'École et du Certificat de Compétences Techniques Avancées.</p>
                        </div>
                    </div>

                    {{-- Étape 4 --}}
                    <div class="relative flex items-start gap-5 pl-8">
                        <div class="absolute left-1.5 top-1.5 w-3.5 h-3.5 rounded-full bg-white/40 ring-4 ring-[#121217]"></div>
                        <div>
                            <span class="text-xs font-bold text-white/60 uppercase tracking-wider">Décembre 2026</span>
                            <h4 class="text-sm font-semibold text-white mt-1">Bilan & Préparation du Volet 2 VAE</h4>
                            <p class="text-xs text-white/60 mt-1">Rapport d'évaluation, consolidation des acquis et ouverture des inscriptions pour le cycle BTS.</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ANNÉE 2027 --}}
            <div class="rounded-3xl bg-[#121217] border border-white/10 p-8">
                <div class="flex items-center gap-3 pb-6 border-b border-white/10 mb-6">
                    <span class="px-3.5 py-1.5 rounded-xl bg-[#320080] text-[#F5B800] border border-[#F5B800]/30 font-bold text-sm">
                        2027
                    </span>
                    <h3 class="text-xl font-bold text-white">
                        Phase 2 : Cycle de Certification BTS par la VAE
                    </h3>
                </div>

                <div class="space-y-6 relative before:absolute before:inset-0 before:left-3 before:w-0.5 before:bg-white/10">

                    {{-- Étape 1 --}}
                    <div class="relative flex items-start gap-5 pl-8">
                        <div class="absolute left-1.5 top-1.5 w-3.5 h-3.5 rounded-full bg-[#F5B800] ring-4 ring-[#121217]"></div>
                        <div>
                            <span class="text-xs font-bold text-[#F5B800] uppercase tracking-wider">Janvier 2027</span>
                            <h4 class="text-sm font-semibold text-white mt-1">Recrutement Volet 2 (60 apprenants)</h4>
                            <p class="text-xs text-white/60 mt-1">Examen des dossiers CPS, entretiens d'orientation et contractualisation des parcours.</p>
                        </div>
                    </div>

                    {{-- Étape 2 --}}
                    <div class="relative flex items-start gap-5 pl-8">
                        <div class="absolute left-1.5 top-1.5 w-3.5 h-3.5 rounded-full bg-[#F5B800] ring-4 ring-[#121217]"></div>
                        <div>
                            <span class="text-xs font-bold text-[#F5B800] uppercase tracking-wider">Février → Octobre 2027</span>
                            <h4 class="text-sm font-semibold text-white mt-1">Cycle de Formation BTS-VAE (9 mois)</h4>
                            <p class="text-xs text-white/60 mt-1">Alternance école-entreprises, ateliers intensifs, immersions sur spectacles vivants et remplissage du Livret VAE.</p>
                        </div>
                    </div>

                    {{-- Étape 3 --}}
                    <div class="relative flex items-start gap-5 pl-8">
                        <div class="absolute left-1.5 top-1.5 w-3.5 h-3.5 rounded-full bg-[#F5B800] ring-4 ring-[#121217]"></div>
                        <div>
                            <span class="text-xs font-bold text-[#F5B800] uppercase tracking-wider">Mai & Octobre 2027</span>
                            <h4 class="text-sm font-semibold text-white mt-1">Évaluations & Soutenances devant Jurys</h4>
                            <p class="text-xs text-white/60 mt-1">Point d'étape Livret VAE en Mai, soutenances finales devant Directeurs techniques et représentants de l'État en Octobre.</p>
                        </div>
                    </div>

                    {{-- Étape 4 --}}
                    <div class="relative flex items-start gap-5 pl-8">
                        <div class="absolute left-1.5 top-1.5 w-3.5 h-3.5 rounded-full bg-[#F5B800] ring-4 ring-[#121217]"></div>
                        <div>
                            <span class="text-xs font-bold text-[#F5B800] uppercase tracking-wider">Novembre → Décembre 2027</span>
                            <h4 class="text-sm font-semibold text-white mt-1">Certification BTS & Lancement Académie</h4>
                            <p class="text-xs text-white/60 mt-1">Délivrance des diplômes BTS d'État et pose des bases institutionnelles de l'Académie des Métiers Techniques.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     6. GOUVERNANCE & PARTENAIRES DU PROJET
========================================================= --}}
<section class="py-20 lg:py-28 bg-[#0a0a0c] text-white">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="text-center max-w-3xl mx-auto mb-14">
            <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.25em] text-[#F5B800] mb-3">
                <span class="w-2 h-2 rounded-full bg-[#F5B800]"></span>
                Gouvernance & Partenaires
            </p>
            <h2 class="text-3xl sm:text-4xl font-serif font-normal text-white">
                Une alliance institutionnelle solide pour l'excellence
            </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="p-8 rounded-3xl bg-[#141418] border border-white/10 text-center">
                <div class="h-16 flex items-center justify-center mb-4">
                    <span class="font-serif text-2xl font-bold text-[#F5B800]">EMSI</span>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">École des Métiers du Son et de l'Image</h3>
                <p class="text-xs text-white/60">Porteur technique et pédagogique, conception des référentiels de compétences et mobilisation des formateurs experts.</p>
            </div>

            <div class="p-8 rounded-3xl bg-[#141418] border border-white/10 text-center">
                <div class="h-16 flex items-center justify-center mb-4">
                    <span class="font-serif text-xl font-bold text-white">Grand Théâtre National</span>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Grand Théâtre Doudou Ndiaye Rose</h3>
                <p class="text-xs text-white/60">Co-porteur officiel, mise à disposition des scènes live, régies techniques, plateaux et logistique d'envergure.</p>
            </div>

            <div class="p-8 rounded-3xl bg-[#141418] border border-white/10 text-center">
                <div class="h-16 flex items-center justify-center mb-4">
                    <span class="font-serif text-lg font-bold text-[#F5B800]">Direction des Concours</span>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Ministères & Direction des Concours</h3>
                <p class="text-xs text-white/60">Cadre réglementaire de la VAE, organisation des jurys d'évaluation et délivrance des certifications d'État.</p>
            </div>
        </div>

        {{-- CTA Final --}}
        <div class="mt-16 p-10 sm:p-12 rounded-3xl bg-gradient-to-r from-[#320080]/60 via-[#181224] to-[#F5B800]/20 border border-white/15 text-center relative overflow-hidden">
            <h3 class="text-2xl sm:text-3xl font-serif font-bold text-white mb-4">
                Prêt à intégrer le programme d'excellence ?
            </h3>
            <p class="text-sm text-white/70 max-w-xl mx-auto mb-8 font-light">
                Déposez votre candidature en ligne en quelques minutes et faites reconnaître vos compétences au plus haut niveau.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a
                    href="{{ route('public.admissions.create') }}"
                    class="px-8 py-4 rounded-full bg-[#F5B800] hover:bg-white text-black font-bold text-xs sm:text-sm uppercase tracking-wider transition-all duration-200 shadow-xl"
                >
                    Déposer ma candidature
                </a>
                <a
                    href="{{ route('public.vae') }}"
                    class="px-7 py-4 rounded-full bg-white/10 hover:bg-white/20 text-white font-semibold text-xs sm:text-sm transition-all duration-200 border border-white/20"
                >
                    Consulter le dispositif VAE
                </a>
            </div>
        </div>

    </div>

</section>

@endsection
