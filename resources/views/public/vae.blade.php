@extends('layouts.public')

@section('title', 'Dispositif VAE — Validation des Acquis de l’Expérience | EMSI Dakar')
@section('description', 'Cycle de certification de niveau BTS (équivalent Bac+2) par la Validation des Acquis de l’Expérience, pour les titulaires d’un CPS ou d’un CS — programme EMSI × Grand Théâtre National Doudou Ndiaye Coumba Rose.')

@section('content')

{{-- =========================================================
     1. HERO SECTION — LA PASSERELLE DIPLÔMANTE VAE
========================================================= --}}
<section class="relative min-h-[85vh] bg-[#050507] text-white flex flex-col justify-center overflow-hidden border-b border-white/10">

    {{-- Arrière-plan cinématique avec lueurs et texture --}}
    <div class="absolute inset-0 z-0">
        <img
            src="{{ asset('images/lieux/regie_broadcast.jpg') }}"
            alt="Régie Broadcast EMSI VAE"
            class="w-full h-full object-cover object-center filter brightness-[0.3] contrast-[1.2] transform scale-105"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-[#050507] via-[#050507]/75 to-[#050507]/90"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(49,1,129,0.4)_0%,transparent_70%)]"></div>
        <div class="absolute inset-0 opacity-[0.03] bg-[linear-gradient(to_right,#ffffff_1px,transparent_1px),linear-gradient(to_bottom,#ffffff_1px,transparent_1px)] [background-size:36px_36px]"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 sm:px-10 lg:px-16 pt-28 sm:pt-36 pb-20 w-full">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-xs font-mono uppercase tracking-widest text-white/50 mb-6">
            <a href="{{ route('public.home') }}" class="hover:text-white transition">Accueil</a>
            <span class="text-white/30">/</span>
            <span class="text-[#F5B800]">Certification de niveau BTS par la VAE</span>
        </div>

        <div class="max-w-4xl space-y-6">

            {{-- Badges Officiels --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#F5B800]/15 text-[#F5B800] border border-[#F5B800]/30 text-xs font-mono font-bold uppercase tracking-wider backdrop-blur-md">
                    <x-lucide-award class="w-3.5 h-3.5 text-[#F5B800]" />
                    <span>Certification de niveau BTS (Bac+2)</span>
                </div>
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.06] text-white/80 border border-white/15 text-xs font-mono font-medium tracking-wider backdrop-blur-md">
                    <x-lucide-shield-check class="w-3.5 h-3.5 text-emerald-400" />
                    <span>Sans Obligation de Baccalauréat Préalable</span>
                </div>
            </div>

            {{-- Titre Principal --}}
            <h1 class="font-serif text-3xl sm:text-5xl lg:text-6xl xl:text-7xl font-normal tracking-tight text-white leading-[1.06]">
                Transformez votre savoir-faire terrain <br>
                <span class="italic text-transparent bg-clip-text bg-gradient-to-r from-[#F5B800] via-[#FFE59E] to-white font-light">en une certification de niveau BTS.</span>
            </h1>

            {{-- Paragraphe d'Accroche --}}
            <p class="text-base sm:text-lg text-white/75 font-light leading-relaxed max-w-3xl">
                                Vous êtes titulaire d'un CPS ou d'un CS dans les métiers du son, de la lumière, de l'image ou du spectacle ? La <strong class="text-white font-medium">Validation des Acquis de l'Expérience (VAE)</strong> vous ouvre, sans Baccalauréat, une <strong>certification de niveau BTS (équivalent Bac+2)</strong> au terme d'un cycle de 9 mois en alternance.
            </p>

            {{-- Actions Rapides --}}
            <div class="pt-4 flex flex-wrap items-center gap-4">
                <a
                    href="#simulateur-vae"
                    class="inline-flex items-center gap-2.5 px-7 py-4 rounded-full bg-[#F5B800] text-black font-bold text-xs uppercase tracking-wider hover:bg-white transition shadow-[0_0_30px_rgba(245,184,0,0.35)]"
                >
                    <x-lucide-calculator class="w-4 h-4" />
                    <span>Tester mon éligibilité en 2 min</span>
                </a>

                <a
                    href="{{ route('public.admissions.create', ['volet' => 'vae']) }}"
                    class="inline-flex items-center gap-2.5 px-6 py-4 rounded-full bg-white/10 hover:bg-white/20 text-white font-semibold text-xs uppercase tracking-wider transition border border-white/15 backdrop-blur-md"
                >
                    <x-lucide-file-text class="w-4 h-4 text-white/70" />
                    <span>Déposer mon dossier VAE</span>
                </a>

                <a
                    href="#filieres-bts"
                    class="inline-flex items-center gap-2 px-5 py-4 text-xs font-mono font-bold text-white/70 hover:text-white transition"
                >
                    <span>Voir les 5 filières</span>
                    <x-lucide-arrow-down class="w-4 h-4" />
                </a>
            </div>

        </div>

        {{-- Chiffres clés de réassurance en bas de hero --}}
        <div class="mt-14 pt-8 border-t border-white/10 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <div class="text-2xl sm:text-3xl font-serif font-bold text-[#F5B800]">Bac + 2</div>
                <div class="text-xs font-mono text-white/60 uppercase mt-1">Niveau BTS</div>
                <div class="text-[11px] text-white/40">Certification par la VAE</div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-serif font-bold text-white">9 Mois</div>
                <div class="text-xs font-mono text-white/60 uppercase mt-1">1 080 heures</div>
                <div class="text-[11px] text-white/40">30 h par semaine en alternance</div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-serif font-bold text-[#F5B800]">5 Spécialités</div>
                <div class="text-xs font-mono text-white/60 uppercase mt-1">Filières Techniques</div>
                <div class="text-[11px] text-white/40">Son, Lumière, Vidéo, Motion, Régie</div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-serif font-bold text-white">Jury</div>
                <div class="text-xs font-mono text-white/60 uppercase mt-1">Professionnels & représentants de l'État</div>
                <div class="text-[11px] text-white/40">Soutenance professionnelle</div>
            </div>
        </div>

    </div>

</section>


{{-- =========================================================
     2. LE SIMULATEUR D'ÉLIGIBILITÉ VAE INTERACTIF (ALPINE.JS)
========================================================= --}}
<section id="simulateur-vae" class="py-24 lg:py-32 bg-[#0a0a0f] text-white relative overflow-hidden border-b border-white/10">

    <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10" x-data="{
        step: 1,
        experience: '3-5', // '<1' | '1-3' | '3-5' | '>5'
        diplome: 'cps', // 'aucun' | 'cps' | 'bac' | 'autre'
        specialite: 'son', // 'son' | 'lumiere' | 'video' | 'motion' | 'regie'
        statut: 'prestataire', // 'prestataire' | 'salarie' | 'freelance' | 'autre'

        get isEligible() {
                        if (this.diplome !== 'cps') return 'faible';
            if (this.experience === '<1') return 'conditionnel';
            return 'excellent';
        },

        get specialiteLabel() {
            const map = {
                                'son': 'Filière Son',
                                'lumiere': 'Filière Technicien Lumière',
                                'video': 'Filière Cadrage Sportif et Régie Vidéo',
                                'motion': 'Filière Infographie et Création Numérique',
                                'regie': 'Filière Régie Générale Spectacle'
            };
            return map[this.specialite] || 'Filière du programme';
        },

        get recommendationText() {
            if (this.isEligible === 'excellent') {
                                return 'Votre profil correspond au public du Volet 2 : titulaire d\'un CPS ou d\'un CS avec une première expérience. Le recrutement se fait sur dossier et entretien de motivation.';
            } else if (this.isEligible === 'conditionnel') {
                                return 'Votre CPS ou CS correspond au public du Volet 2 ; une première expérience pratique renforcera votre dossier. Le recrutement se fait sur dossier et entretien de motivation.';
            } else {
                                return 'Le programme professionnel s\'adresse aux titulaires d\'un CPS ou d\'un CS. Contactez l\'EMSI pour étudier votre situation et les formations qui y préparent.';
            }
        }
    }">

        {{-- Titre de Section --}}
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span class="text-xs font-mono uppercase tracking-[0.25em] text-[#F5B800] font-bold">
                [Outil d'Orientation Rapide]
            </span>
            <h2 class="text-3xl sm:text-5xl font-serif font-normal text-white">
                Simulateur d'Éligibilité VAE Immédiat
            </h2>
            <p class="text-sm text-white/60 leading-relaxed font-light">
                Indiquez votre parcours en 4 critères pour recevoir un diagnostic immédiat sur votre accès au Volet 2.
            </p>
        </div>

        {{-- BOÎTIER DU SIMULATEUR --}}
        <div class="max-w-4xl mx-auto rounded-3xl bg-gradient-to-b from-[#13131b] to-[#0d0d13] border border-white/15 p-6 sm:p-10 shadow-2xl">

            {{-- 4 ÉTAPES DE SÉLECTION --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- CRITÈRE 1 : ANNÉES D'EXPÉRIENCE TERRAIN --}}
                <div class="space-y-3">
                    <label class="block text-xs font-mono uppercase tracking-wider text-[#F5B800] font-bold">
                        1. Expérience pratique sur le terrain
                    </label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <button
                            type="button"
                            @click="experience = '<1'"
                            class="p-3 rounded-xl border text-xs font-semibold text-left transition"
                            :class="experience === '<1' ? 'border-[#F5B800] bg-[#F5B800]/10 text-white ring-1 ring-[#F5B800]' : 'border-white/10 bg-white/[0.02] text-white/70 hover:bg-white/[0.05]'"
                        >
                            Moins d'un an
                        </button>
                        <button
                            type="button"
                            @click="experience = '1-3'"
                            class="p-3 rounded-xl border text-xs font-semibold text-left transition"
                            :class="experience === '1-3' ? 'border-[#F5B800] bg-[#F5B800]/10 text-white ring-1 ring-[#F5B800]' : 'border-white/10 bg-white/[0.02] text-white/70 hover:bg-white/[0.05]'"
                        >
                            1 à 3 ans
                        </button>
                        <button
                            type="button"
                            @click="experience = '3-5'"
                            class="p-3 rounded-xl border text-xs font-semibold text-left transition"
                            :class="experience === '3-5' ? 'border-[#F5B800] bg-[#F5B800]/10 text-white ring-1 ring-[#F5B800]' : 'border-white/10 bg-white/[0.02] text-white/70 hover:bg-white/[0.05]'"
                        >
                            3 à 5 ans
                        </button>
                        <button
                            type="button"
                            @click="experience = '>5'"
                            class="p-3 rounded-xl border text-xs font-semibold text-left transition"
                            :class="experience === '>5' ? 'border-[#F5B800] bg-[#F5B800]/10 text-white ring-1 ring-[#F5B800]' : 'border-white/10 bg-white/[0.02] text-white/70 hover:bg-white/[0.05]'"
                        >
                            Plus de 5 ans
                        </button>
                    </div>
                </div>

                {{-- CRITÈRE 2 : NIVEAU INITIAL / DIPLÔME --}}
                <div class="space-y-3">
                    <label class="block text-xs font-mono uppercase tracking-wider text-[#F5B800] font-bold">
                        2. Niveau ou diplôme initial
                    </label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <button
                            type="button"
                            @click="diplome = 'aucun'"
                            class="p-3 rounded-xl border text-xs font-semibold text-left transition"
                            :class="diplome === 'aucun' ? 'border-[#F5B800] bg-[#F5B800]/10 text-white ring-1 ring-[#F5B800]' : 'border-white/10 bg-white/[0.02] text-white/70 hover:bg-white/[0.05]'"
                        >
                            Sans diplôme
                        </button>
                        <button
                            type="button"
                            @click="diplome = 'cps'"
                            class="p-3 rounded-xl border text-xs font-semibold text-left transition"
                            :class="diplome === 'cps' ? 'border-[#F5B800] bg-[#F5B800]/10 text-white ring-1 ring-[#F5B800]' : 'border-white/10 bg-white/[0.02] text-white/70 hover:bg-white/[0.05]'"
                        >
                            CPS ou CS
                        </button>
                        <button
                            type="button"
                            @click="diplome = 'bac'"
                            class="p-3 rounded-xl border text-xs font-semibold text-left transition"
                            :class="diplome === 'bac' ? 'border-[#F5B800] bg-[#F5B800]/10 text-white ring-1 ring-[#F5B800]' : 'border-white/10 bg-white/[0.02] text-white/70 hover:bg-white/[0.05]'"
                        >
                            Baccalauréat
                        </button>
                        <button
                            type="button"
                            @click="diplome = 'autre'"
                            class="p-3 rounded-xl border text-xs font-semibold text-left transition"
                            :class="diplome === 'autre' ? 'border-[#F5B800] bg-[#F5B800]/10 text-white ring-1 ring-[#F5B800]' : 'border-white/10 bg-white/[0.02] text-white/70 hover:bg-white/[0.05]'"
                        >
                            Autre Formation Sup.
                        </button>
                    </div>
                </div>

                {{-- CRITÈRE 3 : SPÉCIALITÉ VISÉE --}}
                <div class="space-y-3">
                    <label class="block text-xs font-mono uppercase tracking-wider text-[#F5B800] font-bold">
                        3. Spécialité technique visée
                    </label>
                    <select
                        x-model="specialite"
                        class="w-full p-3.5 rounded-xl border border-white/15 bg-black/60 text-white text-xs font-semibold focus:border-[#F5B800] focus:ring-1 focus:ring-[#F5B800] outline-none"
                    >
                        <option value="son">Son</option>
                        <option value="lumiere">Technicien Lumière</option>
                        <option value="video">Cadrage Sportif et Régie Vidéo</option>
                        <option value="motion">Infographie et Création Numérique</option>
                        <option value="regie">Régie Générale Spectacle</option>
                    </select>
                </div>

                {{-- CRITÈRE 4 : STATUT ACTUEL --}}
                <div class="space-y-3">
                    <label class="block text-xs font-mono uppercase tracking-wider text-[#F5B800] font-bold">
                        4. Statut d'exercice actuel
                    </label>
                    <select
                        x-model="statut"
                        class="w-full p-3.5 rounded-xl border border-white/15 bg-black/60 text-white text-xs font-semibold focus:border-[#F5B800] focus:ring-1 focus:ring-[#F5B800] outline-none"
                    >
                        <option value="prestataire">Prestataire intermittent / Technicien de festivals</option>
                        <option value="salarie">Salarié d'une chaîne TV / Maison de production</option>
                        <option value="freelance">Freelance indépendant / Entrepreneur technique</option>
                        <option value="autre">En reconversion / Pratique bénévole régulière</option>
                    </select>
                </div>

            </div>

            {{-- PANNEAU DE RÉSULTAT DU DIAGNOSTIC VAE --}}
            <div class="mt-8 pt-8 border-t border-white/10 rounded-2xl bg-white/[0.02] p-6 sm:p-8 space-y-6">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="text-xs font-mono uppercase tracking-wider text-white/50">Diagnostic d'Éligibilité :</div>
                        <div class="text-xl sm:text-2xl font-bold flex items-center gap-2.5">
                            <template x-if="isEligible === 'excellent'">
                                <span class="text-emerald-400 flex items-center gap-2">
                                    <x-lucide-check-circle-2 class="w-6 h-6 text-emerald-400" />
                                    <span>Profil correspondant au Volet 2</span>
                                </span>
                            </template>
                            <template x-if="isEligible === 'conditionnel'">
                                <span class="text-[#F5B800] flex items-center gap-2">
                                    <x-lucide-alert-circle class="w-6 h-6 text-[#F5B800]" />
                                    <span>Éligibilité avec Renforcement Pratique</span>
                                </span>
                            </template>
                            <template x-if="isEligible === 'faible'">
                                <span class="text-rose-400 flex items-center gap-2">
                                    <x-lucide-help-circle class="w-6 h-6 text-rose-400" />
                                    <span>Orientation Initiale Recommandée</span>
                                </span>
                            </template>
                        </div>
                    </div>

                    <div class="px-4 py-2 rounded-xl bg-black/50 border border-white/10 text-right">
                        <div class="text-[10px] font-mono uppercase text-white/50">Filière Cible</div>
                        <div class="text-xs font-bold text-[#F5B800]" x-text="specialiteLabel"></div>
                    </div>
                </div>

                <p class="text-xs sm:text-sm text-white/80 leading-relaxed font-light bg-black/30 p-4 rounded-xl border border-white/5" x-text="recommendationText"></p>

                <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
                    <div class="text-xs font-mono text-white/60">
                        Recrutement sur dossier et entretien de motivation.
                    </div>

                    <a
                        :href="'{{ route('public.admissions.create') }}?volet=vae&specialite=' + specialite"
                        class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-[#F5B800] text-black font-bold text-xs uppercase tracking-wider hover:bg-white transition shadow-xl"
                    >
                        <span>Candidater avec ce profil</span>
                        <x-lucide-arrow-right class="w-4 h-4" />
                    </a>
                </div>

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     3. LES 5 FILIÈRES DIPLÔMANTES BTS ACCESSIBLES EN VAE
========================================================= --}}
<section id="filieres-bts" class="py-24 lg:py-32 bg-[#050507] text-white border-b border-white/10">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-16 border-b border-white/10">
            <div class="max-w-3xl">
                <span class="text-xs font-mono uppercase tracking-[0.25em] text-[#F5B800] block mb-3 font-bold">
                    [Les 5 filières]
                </span>
                <h2 class="text-3xl sm:text-5xl font-serif font-normal tracking-tight text-white leading-tight">
                    Cinq filières de niveau BTS.
                </h2>
            </div>
            <div class="max-w-md">
                <p class="text-sm text-white/70 leading-relaxed font-light">
                    Chaque filière s'appuie sur le référentiel de compétences du programme et sur des mises en situation réelles.
                </p>
            </div>
        </div>

        <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            {{-- 1. BTS SON --}}
            <div class="p-8 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-emerald-400/50 transition-all duration-300 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-400 font-mono text-xs font-bold border border-emerald-500/30">
                            FILIÈRE 01 &bull; SON
                        </span>
                        <x-lucide-sliders class="w-5 h-5 text-emerald-400" />
                    </div>
                    <h3 class="text-xl font-serif font-bold text-white">Son</h3>
                    <p class="text-xs text-white/70 leading-relaxed font-light">
                        Calage de systèmes Line Array, consoles numériques professionnelles (Yamaha, Allen & Heath, DiGiCo, Midas), mixage live, réseaux audio Dante et MADI, mastering studio.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/10 space-y-2 text-xs font-mono text-white/60">
                    <div>Parcours : <strong class="text-white">Volet 1 et Volet 2</strong></div>
                    <div>Débouchés : <strong class="text-emerald-400">Ingénieur du son live, technicien système, concepteur sonore</strong></div>
                </div>
            </div>

            {{-- 2. BTS LUMIÈRE --}}
            <div class="p-8 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/50 transition-all duration-300 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-[#F5B800]/15 text-[#F5B800] font-mono text-xs font-bold border border-[#F5B800]/30">
                            FILIÈRE 02 &bull; LUMIÈRE
                        </span>
                        <x-lucide-sun class="w-5 h-5 text-[#F5B800]" />
                    </div>
                    <h3 class="text-xl font-serif font-bold text-white">Technicien Lumière</h3>
                    <p class="text-xs text-white/70 leading-relaxed font-light">
                        Programmation sur consoles GrandMA, Chamsys et Avolites, réseaux DMX/Art-Net/sACN, dimensionnement d'un parc projecteurs, synchronisation lumière-son-vidéo et conduite de spectacle.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/10 space-y-2 text-xs font-mono text-white/60">
                    <div>Parcours : <strong class="text-white">Volet 1 et Volet 2</strong></div>
                    <div>Débouchés : <strong class="text-[#F5B800]">Régisseur lumière, concepteur lumière, programmateur lumière</strong></div>
                </div>
            </div>

            {{-- 3. BTS BROADCAST --}}
            <div class="p-8 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-purple-400/50 transition-all duration-300 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-purple-500/15 text-purple-400 font-mono text-xs font-bold border border-purple-500/30">
                            FILIÈRE 03 &bull; CADRAGE
                        </span>
                        <x-lucide-video class="w-5 h-5 text-purple-400" />
                    </div>
                    <h3 class="text-xl font-serif font-bold text-white">Cadrage Sportif et Régie Vidéo</h3>
                    <p class="text-xs text-white/70 leading-relaxed font-light">
                        Cadrage multicaméra sportif, ralenti Super Slow Motion, caméras broadcast (Fiber, RF), anticipation des trajectoires et communication avec la régie.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/10 space-y-2 text-xs font-mono text-white/60">
                    <div>Parcours : <strong class="text-white">Volet 1 et Volet 2</strong></div>
                    <div>Débouchés : <strong class="text-purple-400">Cadreur sportif, assistant réalisateur, chef opérateur</strong></div>
                </div>
            </div>

            {{-- 4. BTS MOTION & XR --}}
            <div class="p-8 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-blue-400/50 transition-all duration-300 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-blue-500/15 text-blue-400 font-mono text-xs font-bold border border-blue-500/30">
                            FILIÈRE 04 &bull; INFOGRAPHIE
                        </span>
                        <x-lucide-monitor-play class="w-5 h-5 text-blue-400" />
                    </div>
                    <h3 class="text-xl font-serif font-bold text-white">Infographie et Création Numérique</h3>
                    <p class="text-xs text-white/70 leading-relaxed font-light">
                        Motion design (After Effects, Cinema 4D), habillage d'émission, modélisation 3D, incrustation Chroma Key en temps réel et identité visuelle événementielle.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/10 space-y-2 text-xs font-mono text-white/60">
                    <div>Parcours : <strong class="text-white">Volet 1 et Volet 2</strong></div>
                    <div>Débouchés : <strong class="text-blue-400">Infographiste et motion designer, directeur artistique graphique</strong></div>
                </div>
            </div>

            {{-- 5. BTS RÉGIE GÉNÉRALE --}}
            <div class="p-8 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-amber-400/50 transition-all duration-300 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-amber-500/15 text-amber-400 font-mono text-xs font-bold border border-amber-500/30">
                            FILIÈRE 05 &bull; RÉGIE GÉNÉRALE
                        </span>
                        <x-lucide-layers class="w-5 h-5 text-amber-400" />
                    </div>
                    <h3 class="text-xl font-serif font-bold text-white">Régie Générale Spectacle</h3>
                    <p class="text-xs text-white/70 leading-relaxed font-light">
                        Cahier des charges, dimensionnement matériel et humain, coordination des équipes son, lumière, vidéo et sécurité, synoptiques et gestion des flux de publics.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/10 space-y-2 text-xs font-mono text-white/60">
                    <div>Parcours : <strong class="text-white">Volet 2 (module transversal au Volet 1)</strong></div>
                    <div>Débouchés : <strong class="text-amber-400">Régisseur général, régisseur adjoint, coordinateur technique</strong></div>
                </div>
            </div>

            {{-- CARTE D'ACCOMPAGNEMENT EMSI --}}
            <div class="p-8 rounded-3xl bg-gradient-to-b from-[#310181]/50 to-[#120a22] border border-[#310181] flex flex-col justify-between space-y-6 shadow-xl">
                <div class="space-y-4">
                    <span class="px-3 py-1 rounded-full bg-[#F5B800] text-black font-mono text-xs font-bold">
                        ACCOMPAGNEMENT
                    </span>
                    <h3 class="text-xl font-serif font-bold text-white">Un tuteur EMSI à vos côtés</h3>
                    <p class="text-xs text-white/80 leading-relaxed font-light">
                        Un tuteur pédagogique de l'EMSI suit chaque apprenant pendant les 9 mois du cycle et valide la qualité des preuves du Livret VAE.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/15">
                    <a
                        href="{{ route('public.admissions.create', ['volet' => 'vae']) }}"
                        class="w-full inline-flex items-center justify-center gap-2 py-3 px-5 rounded-full bg-white text-black font-bold text-xs uppercase tracking-wider hover:bg-[#F5B800] transition"
                    >
                        <span>Initier mon dossier VAE</span>
                        <x-lucide-arrow-right class="w-4 h-4" />
                    </a>
                </div>
            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     4. LES DEUX LIVRETS DE PREUVES (LIVRET 1 & LIVRET 2)
========================================================= --}}
<section class="py-24 bg-[#08080c] text-white border-b border-white/10">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span class="text-xs font-mono uppercase tracking-[0.25em] text-[#F5B800] font-bold">
                [Méthodologie de Preuves]
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal text-white">
                Le Livret de compétences VAE
            </h2>
            <p class="text-xs sm:text-sm text-white/60 leading-relaxed font-light">
                Dès le premier mois du cycle, chaque apprenant ouvre son Livret VAE, alimenté en continu pendant 9 mois : c'est la pièce maîtresse de la soutenance.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">

            {{-- LIVRET 1 --}}
            <div class="p-8 sm:p-10 rounded-3xl bg-[#111118] border border-white/15 space-y-6 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="px-3.5 py-1.5 rounded-full bg-white/10 text-white font-mono text-xs font-bold border border-white/15">
                        CONTENU DU LIVRET
                    </span>
                    <span class="text-2xl font-serif font-bold text-[#F5B800]">Les preuves<</span>
                </div>

                <h3 class="text-2xl font-serif font-bold text-white">
                    Chaque projet documenté
                </h3>

                <p class="text-sm text-white/70 font-light leading-relaxed">
                    Chaque projet réalisé en entreprise ou sur un événement est consigné, photographié, filmé et rédigé pour prouver l'acquisition des compétences de niveau BTS :
                </p>

                <ul class="space-y-3 text-xs text-white/80 font-light">
                    <li class="flex items-start gap-2.5">
                        <x-lucide-check-circle-2 class="w-4 h-4 text-[#F5B800] shrink-0 mt-0.5" />
                        <span>Contrats de travail, bulletins de paie ou attestations de prestations de services</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <x-lucide-check-circle-2 class="w-4 h-4 text-[#F5B800] shrink-0 mt-0.5" />
                        <span>Feuilles de route de festivals, génériques de productions télévisées et captations</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <x-lucide-check-circle-2 class="w-4 h-4 text-[#F5B800] shrink-0 mt-0.5" />
                        <span>Copie du CPS ou du CS et attestations d'expérience</span>
                    </li>
                </ul>

                <div class="p-4 rounded-2xl bg-black/40 border border-white/10 text-xs font-mono text-white/60">
                    Le tuteur EMSI valide la qualité de chaque preuve.
                </div>
            </div>

            {{-- LIVRET 2 --}}
            <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-b from-[#1c142c] to-[#120d20] border border-[#F5B800]/30 space-y-6 relative overflow-hidden shadow-2xl">
                <div class="flex items-center justify-between">
                    <span class="px-3.5 py-1.5 rounded-full bg-[#F5B800] text-black font-mono text-xs font-bold">
                        SUIVI & SOUTENANCE
                    </span>
                    <span class="text-2xl font-serif font-bold text-[#F5B800]">Le jury<</span>
                </div>

                <h3 class="text-2xl font-serif font-bold text-white">
                    Suivi individualisé et soutenance
                </h3>

                <p class="text-sm text-white/80 font-light leading-relaxed">
                    Un tuteur pédagogique de l'EMSI valide les preuves et vous oriente vers les compétences encore non couvertes :
                </p>

                <ul class="space-y-3 text-xs text-white/85 font-light">
                    <li class="flex items-start gap-2.5">
                        <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                        <span>Synoptiques de câblage détaillés, patchs d'entrées/sorties et fiches de régie</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                        <span>Résolution de pannes complexes en conditions de direct sans filet</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                        <span>Soutenance devant un jury professionnel : présentation du parcours, défense du Livret et mise en situation technique</span>
                    </li>
                </ul>

                <div class="p-4 rounded-2xl bg-[#F5B800]/10 border border-[#F5B800]/20 text-xs font-mono text-[#F5B800]">
                    Point d'étape du Livret en mai 2027, soutenances en octobre 2027.
                </div>
            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     5. LE PARCOURS EN 8 ÉTAPES NUMÉROTÉES
========================================================= --}}
<section class="py-24 lg:py-32 bg-[#050507] text-white border-b border-white/10">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span class="text-xs font-mono uppercase tracking-[0.25em] text-[#F5B800] font-bold">
                [Chronologie d'Ascension]
            </span>
            <h2 class="text-3xl sm:text-5xl font-serif font-normal text-white">
                Les 8 Étapes du Parcours de Certification
            </h2>
            <p class="text-sm text-white/60 leading-relaxed font-light">
                Un cycle de 9 mois, de la sélection à la certification de niveau BTS.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- 1 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-[#F5B800] px-3 py-1 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/30">01</span>
                    <x-lucide-user-check class="w-4 h-4 text-white/50" />
                </div>
                <h4 class="text-base font-bold text-white">Candidature & sélection</h4>
                <p class="text-xs text-white/60 leading-relaxed">Dépôt du dossier (CPS ou CS, expérience), présélection et entretien de motivation.</p>
                <div class="text-[11px] font-mono text-[#F5B800] pt-1">Janvier 2027</div>
            </div>

            {{-- 2 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-white px-3 py-1 rounded-full bg-white/10 border border-white/15">02</span>
                    <x-lucide-compass class="w-4 h-4 text-white/50" />
                </div>
                <h4 class="text-base font-bold text-white">Ouverture du Livret</h4>
                <p class="text-xs text-white/60 leading-relaxed">Ouverture du Livret VAE dès le premier mois et affectation d'un tuteur pédagogique EMSI.</p>
                <div class="text-[11px] font-mono text-white/40 pt-1">Février 2027</div>
            </div>

            {{-- 3 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-[#F5B800] px-3 py-1 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/30">03</span>
                    <x-lucide-book-open class="w-4 h-4 text-white/50" />
                </div>
                <h4 class="text-base font-bold text-white">Alternance école-entreprise</h4>
                <p class="text-xs text-white/60 leading-relaxed">30 h par semaine : 10 h de théorie et de gestion de projet, 20 h de pratique en situation réelle.</p>
                <div class="text-[11px] font-mono text-[#F5B800] pt-1">Février – octobre 2027</div>
            </div>

            {{-- 4 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-white px-3 py-1 rounded-full bg-white/10 border border-white/15">04</span>
                    <x-lucide-tv class="w-4 h-4 text-white/50" />
                </div>
                <h4 class="text-base font-bold text-white">Mises en situation réelles</h4>
                <p class="text-xs text-white/60 leading-relaxed">Position d'assistant chef de projet sur de grands événements, sous supervision.</p>
                <div class="text-[11px] font-mono text-white/40 pt-1">Tout au long du cycle</div>
            </div>

            {{-- 5 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-[#F5B800] px-3 py-1 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/30">05</span>
                    <x-lucide-file-text class="w-4 h-4 text-white/50" />
                </div>
                <h4 class="text-base font-bold text-white">Documentation continue</h4>
                <p class="text-xs text-white/60 leading-relaxed">Chaque projet est consigné, photographié, filmé et rédigé dans le Livret VAE.</p>
                <div class="text-[11px] font-mono text-[#F5B800] pt-1">En continu</div>
            </div>

            {{-- 6 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-white px-3 py-1 rounded-full bg-white/10 border border-white/15">06</span>
                    <x-lucide-presentation class="w-4 h-4 text-white/50" />
                </div>
                <h4 class="text-base font-bold text-white">Point d'étape</h4>
                <p class="text-xs text-white/60 leading-relaxed">Évaluation intermédiaire du Livret VAE avec le tuteur.</p>
                <div class="text-[11px] font-mono text-white/40 pt-1">Mai 2027</div>
            </div>

            {{-- 7 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-[#F5B800] px-3 py-1 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/30">07</span>
                    <x-lucide-award class="w-4 h-4 text-[#F5B800]" />
                </div>
                <h4 class="text-base font-bold text-white">Soutenance</h4>
                <p class="text-xs text-white/60 leading-relaxed">Soutenance devant un jury de professionnels du secteur et de représentants de l'État.</p>
                <div class="text-[11px] font-mono text-[#F5B800] pt-1">Octobre 2027</div>
            </div>

            {{-- 8 --}}
            <div class="p-6 rounded-3xl bg-gradient-to-b from-[#310181]/40 to-[#140b25] border border-[#310181] space-y-3 shadow-xl">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-black px-3 py-1 rounded-full bg-[#F5B800]">08</span>
                    <x-lucide-sparkles class="w-4 h-4 text-[#F5B800]" />
                </div>
                <h4 class="text-base font-bold text-white">Certification</h4>
                <p class="text-xs text-white/80 leading-relaxed">Délivrance de la certification de niveau BTS (équivalent Bac+2).</p>
                <div class="text-[11px] font-mono text-[#F5B800] font-bold pt-1">Novembre 2027</div>
            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     6. JURY D'EXCELLENCE & VALEUR DU DIPLÔME
========================================================= --}}
<section class="py-24 bg-[#08080a] text-white border-b border-white/10">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="grid lg:grid-cols-12 gap-10 lg:gap-14 items-center">

            <div class="lg:col-span-7 space-y-6">
                <span class="text-xs font-mono uppercase tracking-[0.25em] text-[#F5B800] font-bold">
                    [Jury & certification]
                </span>

                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal text-white leading-tight">
                    Un jury professionnel, une certification de niveau BTS.
                </h2>

                <p class="text-sm sm:text-base text-white/70 leading-relaxed font-light">
                    En fin de cycle, un jury évalue le Livret VAE de chaque apprenant et le soumet à une soutenance professionnelle. La certification délivrée équivaut à un Baccalauréat+2 (niveau BTS) et ouvre l'accès à des postes de responsabilité.
                </p>

                <div class="grid sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#F5B800]">
                            <x-lucide-check class="w-4 h-4" />
                            <span>PRÉSENTATION DU PARCOURS</span>
                        </div>
                        <p class="text-xs text-white/60 font-light leading-relaxed">L'apprenant présente son parcours et les projets menés pendant le cycle.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-emerald-400">
                            <x-lucide-check class="w-4 h-4" />
                            <span>DÉFENSE DU LIVRET VAE</span>
                        </div>
                        <p class="text-xs text-white/60 font-light leading-relaxed">Il défend les preuves consignées dans son Livret de compétences.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-purple-400">
                            <x-lucide-check class="w-4 h-4" />
                            <span>MISE EN SITUATION TECHNIQUE</span>
                        </div>
                        <p class="text-xs text-white/60 font-light leading-relaxed">Le jury vérifie l'acquisition effective des compétences en situation.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-blue-400">
                            <x-lucide-check class="w-4 h-4" />
                            <span>DÉBOUCHÉS</span>
                        </div>
                        <p class="text-xs text-white/60 font-light leading-relaxed">Chef de projet, cadreur principal, régisseur adjoint ou création d'entreprise.</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-b from-[#13131c] to-[#1c142c] border border-white/15 shadow-2xl space-y-6">
                    <div class="w-14 h-14 rounded-2xl bg-[#F5B800]/15 text-[#F5B800] flex items-center justify-center border border-[#F5B800]/30">
                        <x-lucide-graduation-cap class="w-7 h-7" />
                    </div>

                    <div>
                        <span class="text-xs font-mono uppercase tracking-wider text-[#F5B800]">COMPOSITION DU JURY</span>
                        <h3 class="text-2xl font-serif font-bold text-white mt-1">Des professionnels du secteur</h3>
                    </div>

                    <ul class="space-y-3 text-xs text-white/75 font-light">
                        <li class="flex items-center gap-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#F5B800]"></span>
                            <span>Directeurs techniques de chaînes de télévision et régies broadcast</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#F5B800]"></span>
                            <span>Régisseurs généraux de grands festivals et spectacles vivants</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#F5B800]"></span>
                            <span>Directeurs d'agences de production et de création numérique</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#F5B800]"></span>
                            <span>Représentants de l'État</span>
                        </li>
                    </ul>

                    <div class="pt-4 border-t border-white/10">
                        <a
                            href="{{ route('public.admissions.create', ['volet' => 'vae']) }}"
                            class="w-full inline-flex items-center justify-center gap-2 py-4 px-6 rounded-full bg-[#F5B800] text-black font-bold text-xs uppercase tracking-wider hover:bg-white transition"
                        >
                            <span>Déposer ma candidature VAE</span>
                            <x-lucide-arrow-up-right class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     7. FAQ INTERACTIVE SUR LE DISPOSITIF VAE (ALPINE.JS)
========================================================= --}}
<section class="py-24 bg-[#050507] text-white border-b border-white/10" x-data="{ activeFaq: 1 }">

    <div class="max-w-4xl mx-auto px-6 lg:px-10">

        <div class="text-center mb-16 space-y-3">
            <span class="text-xs font-mono uppercase tracking-[0.25em] text-[#F5B800] font-bold">
                [Réponses Claires]
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal text-white">
                Questions Fréquentes sur la VAE
            </h2>
            <p class="text-xs sm:text-sm text-white/60 leading-relaxed font-light">
                Tout ce que vous devez savoir avant de déposer votre dossier.
            </p>
        </div>

        <div class="space-y-4">

            {{-- FAQ 1 --}}
            <div class="rounded-2xl bg-white/[0.03] border border-white/10 overflow-hidden transition">
                <button
                    type="button"
                    @click="activeFaq = (activeFaq === 1 ? null : 1)"
                    class="w-full p-5 sm:p-6 text-left flex items-center justify-between gap-4"
                >
                    <span class="text-sm sm:text-base font-bold text-white">Faut-il obligatoirement avoir le Baccalauréat pour intégrer la VAE ?</span>
                    <span class="transition-transform duration-300" :class="activeFaq === 1 ? 'rotate-180' : ''">
                        <x-lucide-chevron-down class="w-5 h-5 text-[#F5B800]" />
                    </span>
                </button>
                <div x-show="activeFaq === 1" x-collapse class="px-5 sm:px-6 pb-6 text-xs sm:text-sm text-white/70 font-light leading-relaxed border-t border-white/5 pt-4">
                    Non. Le Volet 2 s'adresse aux titulaires d'un CPS ou d'un CS sans Baccalauréat : la VAE leur permet d'accéder à une certification de niveau BTS sur la base des compétences acquises.
                </div>
            </div>

            {{-- FAQ 2 --}}
            <div class="rounded-2xl bg-white/[0.03] border border-white/10 overflow-hidden transition">
                <button
                    type="button"
                    @click="activeFaq = (activeFaq === 2 ? null : 2)"
                    class="w-full p-5 sm:p-6 text-left flex items-center justify-between gap-4"
                >
                    <span class="text-sm sm:text-base font-bold text-white">Quel est le rythme du cycle ?</span>
                    <span class="transition-transform duration-300" :class="activeFaq === 2 ? 'rotate-180' : ''">
                        <x-lucide-chevron-down class="w-5 h-5 text-[#F5B800]" />
                    </span>
                </button>
                <div x-show="activeFaq === 2" x-collapse class="px-5 sm:px-6 pb-6 text-xs sm:text-sm text-white/70 font-light leading-relaxed border-t border-white/5 pt-4">
                    30 heures par semaine pendant 9 mois : 10 heures de théorie et de gestion de projet, 20 heures de travaux pratiques en situation réelle, en alternance école-entreprise.
                </div>
            </div>

            {{-- FAQ 3 --}}
            <div class="rounded-2xl bg-white/[0.03] border border-white/10 overflow-hidden transition">
                <button
                    type="button"
                    @click="activeFaq = (activeFaq === 3 ? null : 3)"
                    class="w-full p-5 sm:p-6 text-left flex items-center justify-between gap-4"
                >
                    <span class="text-sm sm:text-base font-bold text-white">Combien de temps dure le cycle de certification VAE ?</span>
                    <span class="transition-transform duration-300" :class="activeFaq === 3 ? 'rotate-180' : ''">
                        <x-lucide-chevron-down class="w-5 h-5 text-[#F5B800]" />
                    </span>
                </button>
                <div x-show="activeFaq === 3" x-collapse class="px-5 sm:px-6 pb-6 text-xs sm:text-sm text-white/70 font-light leading-relaxed border-t border-white/5 pt-4">
                    Le cycle dure 9 mois (février à octobre 2027). Le Livret VAE est ouvert dès le premier mois, un point d'étape a lieu en mai et les soutenances se tiennent en octobre.
                </div>
            </div>

            {{-- FAQ 4 --}}
            <div class="rounded-2xl bg-white/[0.03] border border-white/10 overflow-hidden transition">
                <button
                    type="button"
                    @click="activeFaq = (activeFaq === 4 ? null : 4)"
                    class="w-full p-5 sm:p-6 text-left flex items-center justify-between gap-4"
                >
                    <span class="text-sm sm:text-base font-bold text-white">Quelle certification est délivrée ?</span>
                    <span class="transition-transform duration-300" :class="activeFaq === 4 ? 'rotate-180' : ''">
                        <x-lucide-chevron-down class="w-5 h-5 text-[#F5B800]" />
                    </span>
                </button>
                <div x-show="activeFaq === 4" x-collapse class="px-5 sm:px-6 pb-6 text-xs sm:text-sm text-white/70 font-light leading-relaxed border-t border-white/5 pt-4">
                    Une certification de niveau BTS, équivalente à un Baccalauréat+2, délivrée après évaluation du Livret VAE et soutenance devant un jury de professionnels du secteur et de représentants de l'État.
                </div>
            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     8. SECTION FINALE DE CANDIDATURE & CONTACT DIRECT
========================================================= --}}
<section class="py-24 lg:py-32 bg-[#08080c] text-white">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="grid lg:grid-cols-12 gap-10 lg:gap-14 items-center">

            <div class="lg:col-span-7 space-y-6">
                <span class="text-xs font-mono uppercase tracking-[0.25em] text-[#F5B800] font-bold">
                    [Passez à l'Action]
                </span>

                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal text-white leading-tight">
                    Faites valoir vos années d'expérience dès aujourd'hui.
                </h2>

                <p class="text-sm sm:text-base text-white/70 leading-relaxed font-light">
                    Nos équipes étudient votre dossier et vous orientent vers la filière la plus adaptée à votre parcours.
                </p>

                <div class="pt-4 grid sm:grid-cols-2 gap-4">
                    <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 space-y-1">
                        <div class="text-xs font-mono text-[#F5B800]">PÔLE CONSEIL VAE</div>
                        <div class="text-sm font-semibold text-white">{{ $siteSettings?->address ?: 'Dakar, Sénégal' }}</div>
                        <div class="text-xs text-white/50">Entretiens d'orientation sur rendez-vous</div>
                    </div>

                    <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 space-y-1">
                        <div class="text-xs font-mono text-[#F5B800]">LIGNE DIRECTE VAE</div>
                        <div class="text-sm font-semibold text-white">{{ $siteSettings?->phone ?: '—' }}</div>
                        <div class="text-xs text-white/50">{{ $siteSettings?->email }}</div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-b from-[#13131c] to-[#1c142c] border border-white/15 shadow-2xl space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#F5B800]/15 text-[#F5B800] text-xs font-bold font-mono">
                        <span>VOLET 2 · RECRUTEMENT EN JANVIER 2027</span>
                    </div>

                    <h3 class="text-2xl font-serif font-bold text-white">
                        Déposez votre dossier VAE
                    </h3>

                    <p class="text-xs sm:text-sm text-white/65 leading-relaxed font-light">
                        Remplissez le formulaire en ligne en sélectionnant le Volet 2 pour lancer l'étude de votre recevabilité administrative.
                    </p>

                    <div class="space-y-3 pt-2">
                        <a
                            href="{{ route('public.admissions.create', ['volet' => 'vae']) }}"
                            class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-4 rounded-full bg-[#F5B800] text-black font-bold text-xs uppercase tracking-wider hover:bg-white transition shadow-xl"
                        >
                            <span>Déposer mon dossier en ligne</span>
                            <x-lucide-arrow-up-right class="w-4 h-4" />
                        </a>

                        @if($siteSettings?->whatsapp)
                            <a
                                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings->whatsapp) }}?text={{ urlencode('Bonjour, je souhaite des informations sur le dispositif VAE pour obtenir le BTS à l\'EMSI.') }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition text-center"
                            >
                                <x-lucide-message-circle class="w-4 h-4" />
                                <span>Échanger avec le responsable VAE</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>

</section>

@endsection
