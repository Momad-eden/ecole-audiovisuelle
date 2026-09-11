@extends('layouts.public')

@section('title', 'Dispositif VAE — Validation des Acquis de l’Expérience | EMSI Dakar')
@section('description', 'Obtenez votre Brevet de Technicien Supérieur (BTS Bac+2) par la Validation des Acquis de l’Expérience à l’EMSI, au Grand Théâtre National de Dakar. Double tutelle ministérielle.')

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
            <span class="text-[#F5B800]">Dispositif VAE BTS d'État</span>
        </div>

        <div class="max-w-4xl space-y-6">

            {{-- Badges Officiels --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#F5B800]/15 text-[#F5B800] border border-[#F5B800]/30 text-xs font-mono font-bold uppercase tracking-wider backdrop-blur-md">
                    <x-lucide-award class="w-3.5 h-3.5 text-[#F5B800]" />
                    <span>Diplôme d'État · Niveau BTS (Bac+2)</span>
                </div>
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.06] text-white/80 border border-white/15 text-xs font-mono font-medium tracking-wider backdrop-blur-md">
                    <x-lucide-shield-check class="w-3.5 h-3.5 text-emerald-400" />
                    <span>Sans Obligation de Baccalauréat Préalable</span>
                </div>
            </div>

            {{-- Titre Principal --}}
            <h1 class="font-serif text-3xl sm:text-5xl lg:text-6xl xl:text-7xl font-normal tracking-tight text-white leading-[1.06]">
                Transformez votre savoir-faire terrain <br>
                <span class="italic text-transparent bg-clip-text bg-gradient-to-r from-[#F5B800] via-[#FFE59E] to-white font-light">en un Brevet de Technicien Supérieur.</span>
            </h1>

            {{-- Paragraphe d'Accroche --}}
            <p class="text-base sm:text-lg text-white/75 font-light leading-relaxed max-w-3xl">
                Vous exercez déjà comme technicien du son, éclairagiste, cadreur, monteur ou régisseur ? La <strong class="text-white font-medium">Validation des Acquis de l'Expérience (VAE)</strong> vous permet d'obtenir un titre officiel d'État de niveau <strong>Bac+2 (BTS)</strong> en valorisant votre pratique réelle, soutenu par la double tutelle des ministères sénégalais.
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
                    <span>Voir les 5 spécialités BTS</span>
                    <x-lucide-arrow-down class="w-4 h-4" />
                </a>
            </div>

        </div>

        {{-- Chiffres clés de réassurance en bas de hero --}}
        <div class="mt-14 pt-8 border-t border-white/10 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <div class="text-2xl sm:text-3xl font-serif font-bold text-[#F5B800]">Bac + 2</div>
                <div class="text-xs font-mono text-white/60 uppercase mt-1">Niveau Reconnu d'État</div>
                <div class="text-[11px] text-white/40">Grille indiciaire officielle</div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-serif font-bold text-white">9 Mois</div>
                <div class="text-xs font-mono text-white/60 uppercase mt-1">Accompagnement Sur-Mesure</div>
                <div class="text-[11px] text-white/40">Compatible avec votre emploi</div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-serif font-bold text-[#F5B800]">5 Spécialités</div>
                <div class="text-xs font-mono text-white/60 uppercase mt-1">Filières Techniques</div>
                <div class="text-[11px] text-white/40">Son, Lumière, Vidéo, Motion, Régie</div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-serif font-bold text-white">100% Souverain</div>
                <div class="text-xs font-mono text-white/60 uppercase mt-1">Jury Mixte Professionnels & État</div>
                <div class="text-[11px] text-white/40">Direction des Examens & Concours</div>
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
            if (this.experience === '<1' && this.diplome === 'aucun') return 'faible';
            if (this.experience === '<1') return 'conditionnel';
            return 'excellent';
        },

        get specialiteLabel() {
            const map = {
                'son': 'BTS Ingénierie Son & Mixage Live',
                'lumiere': 'BTS Régie Lumière & Scénographie',
                'video': 'BTS Cadrage & Réalisation Broadcast',
                'motion': 'BTS Motion Design & Régie d\'Écrans LED',
                'regie': 'BTS Régie Générale & Logistique de Spectacle'
            };
            return map[this.specialite] || 'BTS Métiers de l\'Audiovisuel';
        },

        get recommendationText() {
            if (this.isEligible === 'excellent') {
                return 'Votre profil correspond aux critères d\'accès direct au Volet 2 (Certification BTS par VAE). Vos années d\'expérience vous dispensent de modules généraux et vous permettent de vous concentrer directement sur la rédaction du Livret 2 et la préparation de la soutenance officielle.';
            } else if (this.isEligible === 'conditionnel') {
                return 'Vous êtes éligible avec un aménagement préparatoire. Nous vous conseillons d\'intégrer d\'abord le Volet 1 (Perfectionnement intensif de 3 mois au Grand Théâtre) pour consolider vos preuves techniques avant de soutenir votre Livret 2.';
            } else {
                return 'Une première expérience pratique supplémentaire est recommandée. Nous vous invitons à suivre le Volet 1 immersif de 3 mois pour acquérir les heures opérationnelles requises au Grand Théâtre National.';
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
                Indiquez votre parcours en 4 critères pour recevoir un diagnostic immédiat sur votre accès au titre BTS.
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
                            Autodidacte (Sans diplôme)
                        </button>
                        <button
                            type="button"
                            @click="diplome = 'cps'"
                            class="p-3 rounded-xl border text-xs font-semibold text-left transition"
                            :class="diplome === 'cps' ? 'border-[#F5B800] bg-[#F5B800]/10 text-white ring-1 ring-[#F5B800]' : 'border-white/10 bg-white/[0.02] text-white/70 hover:bg-white/[0.05]'"
                        >
                            Certificat CPS / CAP / BEP
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
                        <option value="son">Son & Mixage Façade / Retours / Studio</option>
                        <option value="lumiere">Lumière & Pupitre GrandMA3 / Scénographie</option>
                        <option value="video">Cadrage Broadcast & Réalisation Multicaméra</option>
                        <option value="motion">Motion Design, Écrans LED Novastar & XR</option>
                        <option value="regie">Régie Générale, Logistique & Sécurité ERP</option>
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
                                    <span>Forte Éligibilité VAE Directe (BTS Bac+2)</span>
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
                        Accompagnement par des directeurs techniques et jurys officiels d'État.
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
                    [Référentiel des Titres d'État]
                </span>
                <h2 class="text-3xl sm:text-5xl font-serif font-normal tracking-tight text-white leading-tight">
                    Les 5 Brevets de Technicien Supérieur certifiés.
                </h2>
            </div>
            <div class="max-w-md">
                <p class="text-sm text-white/70 leading-relaxed font-light">
                    Chaque filière est adossée au référentiel officiel du Ministère de la Formation Professionnelle et validée en situation réelle au Grand Théâtre National.
                </p>
            </div>
        </div>

        <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            {{-- 1. BTS SON --}}
            <div class="p-8 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-emerald-400/50 transition-all duration-300 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-400 font-mono text-xs font-bold border border-emerald-500/30">
                            BTS 01 &bull; SON
                        </span>
                        <x-lucide-sliders class="w-5 h-5 text-emerald-400" />
                    </div>
                    <h3 class="text-xl font-serif font-bold text-white">Ingénierie du Son & Mixage Live</h3>
                    <p class="text-xs text-white/70 leading-relaxed font-light">
                        Validation de vos compétences en calage de systèmes Line Array, mixage numérique façade et retours (DiGiCo / Yamaha), réseaux audionumériques Dante et prise de son studio.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/10 space-y-2 text-xs font-mono text-white/60">
                    <div>Épreuve clé : <strong class="text-white">Mixage live sous pression & Dante</strong></div>
                    <div>Débouché : <strong class="text-emerald-400">Chef Opérateur Son, Ingé Façade</strong></div>
                </div>
            </div>

            {{-- 2. BTS LUMIÈRE --}}
            <div class="p-8 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/50 transition-all duration-300 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-[#F5B800]/15 text-[#F5B800] font-mono text-xs font-bold border border-[#F5B800]/30">
                            BTS 02 &bull; LUMIÈRE
                        </span>
                        <x-lucide-sun class="w-5 h-5 text-[#F5B800]" />
                    </div>
                    <h3 class="text-xl font-serif font-bold text-white">Régie Lumière & Scénographie</h3>
                    <p class="text-xs text-white/70 leading-relaxed font-light">
                        Validation de la programmation sur consoles GrandMA3 et ChamSys, gestion des protocoles DMX/Art-Net, accroche et rigging sécurisé sous grill 45m et création d'ambiances scéniques.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/10 space-y-2 text-xs font-mono text-white/60">
                    <div>Épreuve clé : <strong class="text-white">Show timecodé & sécurité rigging</strong></div>
                    <div>Débouché : <strong class="text-[#F5B800]">Directeur d'Éclairage, Éclairagiste</strong></div>
                </div>
            </div>

            {{-- 3. BTS BROADCAST --}}
            <div class="p-8 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-purple-400/50 transition-all duration-300 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-purple-500/15 text-purple-400 font-mono text-xs font-bold border border-purple-500/30">
                            BTS 03 &bull; BROADCAST
                        </span>
                        <x-lucide-video class="w-5 h-5 text-purple-400" />
                    </div>
                    <h3 class="text-xl font-serif font-bold text-white">Cadrage & Réalisation Live</h3>
                    <p class="text-xs text-white/70 leading-relaxed font-light">
                        Certification de l'exploitation de caméras de plateau broadcast à longues focales (80x), mélangeurs live 4K, serveurs de ralentis Super Slow Motion et liaisons fibre SMPTE.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/10 space-y-2 text-xs font-mono text-white/60">
                    <div>Épreuve clé : <strong class="text-white">Réalisation multicam de direct</strong></div>
                    <div>Débouché : <strong class="text-purple-400">Chef Cadreur TV, Réalisateur Live</strong></div>
                </div>
            </div>

            {{-- 4. BTS MOTION & XR --}}
            <div class="p-8 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-blue-400/50 transition-all duration-300 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-blue-500/15 text-blue-400 font-mono text-xs font-bold border border-blue-500/30">
                            BTS 04 &bull; MOTION & XR
                        </span>
                        <x-lucide-monitor-play class="w-5 h-5 text-blue-400" />
                    </div>
                    <h3 class="text-xl font-serif font-bold text-white">Motion Design & Régie d'Écrans</h3>
                    <p class="text-xs text-white/70 leading-relaxed font-light">
                        Valorisation de vos compétences en création graphique 3D temps réel (Unreal Engine / After Effects), gestion de processeurs murs LED Novastar et vidéo-mapping scénique.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/10 space-y-2 text-xs font-mono text-white/60">
                    <div>Épreuve clé : <strong class="text-white">Habillage dynamique live sur mur LED</strong></div>
                    <div>Débouché : <strong class="text-blue-400">Opérateur Murs LED, Motion Designer</strong></div>
                </div>
            </div>

            {{-- 5. BTS RÉGIE GÉNÉRALE --}}
            <div class="p-8 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-amber-400/50 transition-all duration-300 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-amber-500/15 text-amber-400 font-mono text-xs font-bold border border-amber-500/30">
                            BTS 05 &bull; RÉGIE GÉNÉRALE
                        </span>
                        <x-lucide-layers class="w-5 h-5 text-amber-400" />
                    </div>
                    <h3 class="text-xl font-serif font-bold text-white">Régie Générale & Logistique ERP</h3>
                    <p class="text-xs text-white/70 leading-relaxed font-light">
                        Reconnaissance de votre autorité sur la coordination générale de plateaux de spectacle, sécurité incendie ERP, gestion des fiches techniques internationales et management d'équipes.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/10 space-y-2 text-xs font-mono text-white/60">
                    <div>Épreuve clé : <strong class="text-white">Plan d'implantation & sécurité ERP</strong></div>
                    <div>Débouché : <strong class="text-amber-400">Régisseur Général de Festival / Salle</strong></div>
                </div>
            </div>

            {{-- CARTE D'ACCOMPAGNEMENT EMSI --}}
            <div class="p-8 rounded-3xl bg-gradient-to-b from-[#310181]/50 to-[#120a22] border border-[#310181] flex flex-col justify-between space-y-6 shadow-xl">
                <div class="space-y-4">
                    <span class="px-3 py-1 rounded-full bg-[#F5B800] text-black font-mono text-xs font-bold">
                        TUTORAT VIP
                    </span>
                    <h3 class="text-xl font-serif font-bold text-white">Accompagnement Sur-Mesure EMSI</h3>
                    <p class="text-xs text-white/80 leading-relaxed font-light">
                        Bénéficiez de 9 mois d'ateliers méthodologiques, de séances d'écriture assistée du Livret 2 et d'accès illimité aux régies du Grand Théâtre pour vos répétitions de soutenance.
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
                Les Deux Piliers du Dossier VAE
            </h2>
            <p class="text-xs sm:text-sm text-white/60 leading-relaxed font-light">
                La VAE repose sur la démonstration concrète de votre maîtrise technique à travers deux étapes documentées.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">

            {{-- LIVRET 1 --}}
            <div class="p-8 sm:p-10 rounded-3xl bg-[#111118] border border-white/15 space-y-6 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="px-3.5 py-1.5 rounded-full bg-white/10 text-white font-mono text-xs font-bold border border-white/15">
                        PHASE ADMINISTRATIVE
                    </span>
                    <span class="text-2xl font-serif font-bold text-[#F5B800]">Livret 01</span>
                </div>

                <h3 class="text-2xl font-serif font-bold text-white">
                    Dossier de Recevabilité & Parcours
                </h3>

                <p class="text-sm text-white/70 font-light leading-relaxed">
                    Le Livret 1 certifie la conformité de vos années d'expérience et votre éligibilité légale au titre BTS. Il recense l'ensemble de votre historique professionnel :
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
                        <span>Attestations de stages ou certificat initial (CPS, BEP ou pratique autodidacte certifiée)</span>
                    </li>
                </ul>

                <div class="p-4 rounded-2xl bg-black/40 border border-white/10 text-xs font-mono text-white/60">
                    Validation par le Comité Pédagogique EMSI sous 7 jours.
                </div>
            </div>

            {{-- LIVRET 2 --}}
            <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-b from-[#1c142c] to-[#120d20] border border-[#F5B800]/30 space-y-6 relative overflow-hidden shadow-2xl">
                <div class="flex items-center justify-between">
                    <span class="px-3.5 py-1.5 rounded-full bg-[#F5B800] text-black font-mono text-xs font-bold">
                        PHASE D'EXPERTISE TECHNIQUE
                    </span>
                    <span class="text-2xl font-serif font-bold text-[#F5B800]">Livret 02</span>
                </div>

                <h3 class="text-2xl font-serif font-bold text-white">
                    Dossier d'Expérience & Analyse de Cas Réels
                </h3>

                <p class="text-sm text-white/80 font-light leading-relaxed">
                    Le cœur de la VAE. Vous y décrivez avec précision méthodologique plusieurs interventions professionnelles d'envergure, appuyé par un tuteur EMSI :
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
                        <span>Soutenance orale de 45 minutes devant le Jury Officiel d'État</span>
                    </li>
                </ul>

                <div class="p-4 rounded-2xl bg-[#F5B800]/10 border border-[#F5B800]/20 text-xs font-mono text-[#F5B800]">
                    Accompagnement individuel et jurys blancs d'entraînement inclus.
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
                Un accompagnement structuré pas-à-pas sur 9 mois pour transformer votre savoir-faire en diplôme officiel.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- 1 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-[#F5B800] px-3 py-1 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/30">01</span>
                    <x-lucide-user-check class="w-4 h-4 text-white/50" />
                </div>
                <h4 class="text-base font-bold text-white">Candidature & Diagnostic</h4>
                <p class="text-xs text-white/60 leading-relaxed">Dépôt du formulaire en ligne, étude de votre profil et validation de la recevabilité du Livret 1.</p>
                <div class="text-[11px] font-mono text-[#F5B800] pt-1">Mois 1</div>
            </div>

            {{-- 2 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-white px-3 py-1 rounded-full bg-white/10 border border-white/15">02</span>
                    <x-lucide-compass class="w-4 h-4 text-white/50" />
                </div>
                <h4 class="text-base font-bold text-white">Affectation du Tuteur</h4>
                <p class="text-xs text-white/60 leading-relaxed">Attribution d'un directeur technique référent pour vous guider tout au long du parcours VAE.</p>
                <div class="text-[11px] font-mono text-white/40 pt-1">Mois 2</div>
            </div>

            {{-- 3 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-[#F5B800] px-3 py-1 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/30">03</span>
                    <x-lucide-book-open class="w-4 h-4 text-white/50" />
                </div>
                <h4 class="text-base font-bold text-white">Ateliers d'Écriture Technique</h4>
                <p class="text-xs text-white/60 leading-relaxed">Méthodologie de rédaction, formalisation des compétences et structuration des cas d'expertise.</p>
                <div class="text-[11px] font-mono text-[#F5B800] pt-1">Mois 3 - 4</div>
            </div>

            {{-- 4 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-white px-3 py-1 rounded-full bg-white/10 border border-white/15">04</span>
                    <x-lucide-tv class="w-4 h-4 text-white/50" />
                </div>
                <h4 class="text-base font-bold text-white">Immersion Grand Théâtre</h4>
                <p class="text-xs text-white/60 leading-relaxed">Mise en pratique sur les régies broadcast et le grill 45m pour documenter vos réalisations techniques.</p>
                <div class="text-[11px] font-mono text-white/40 pt-1">Mois 5</div>
            </div>

            {{-- 5 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-[#F5B800] px-3 py-1 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/30">05</span>
                    <x-lucide-file-text class="w-4 h-4 text-white/50" />
                </div>
                <h4 class="text-base font-bold text-white">Finalisation du Livret 2</h4>
                <p class="text-xs text-white/60 leading-relaxed">Relecture approfondie par le tuteur, vérification des annexes techniques et dépôt officiel.</p>
                <div class="text-[11px] font-mono text-[#F5B800] pt-1">Mois 6 - 7</div>
            </div>

            {{-- 6 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-white px-3 py-1 rounded-full bg-white/10 border border-white/15">06</span>
                    <x-lucide-presentation class="w-4 h-4 text-white/50" />
                </div>
                <h4 class="text-base font-bold text-white">Simulation Jury Blanc</h4>
                <p class="text-xs text-white/60 leading-relaxed">Entraînement en conditions réelles à la prise de parole, à l'argumentation et aux questions pièges.</p>
                <div class="text-[11px] font-mono text-white/40 pt-1">Mois 8</div>
            </div>

            {{-- 7 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-[#F5B800] px-3 py-1 rounded-full bg-[#F5B800]/10 border border-[#F5B800]/30">07</span>
                    <x-lucide-award class="w-4 h-4 text-[#F5B800]" />
                </div>
                <h4 class="text-base font-bold text-white">Soutenance Officielle</h4>
                <p class="text-xs text-white/60 leading-relaxed">Exposé de 45 minutes devant le Jury souverain composé de professionnels et représentants de l'État.</p>
                <div class="text-[11px] font-mono text-[#F5B800] pt-1">Mois 9</div>
            </div>

            {{-- 8 --}}
            <div class="p-6 rounded-3xl bg-gradient-to-b from-[#310181]/40 to-[#140b25] border border-[#310181] space-y-3 shadow-xl">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-black px-3 py-1 rounded-full bg-[#F5B800]">08</span>
                    <x-lucide-sparkles class="w-4 h-4 text-[#F5B800]" />
                </div>
                <h4 class="text-base font-bold text-white">Diplôme BTS Délivré</h4>
                <p class="text-xs text-white/80 leading-relaxed">Remise officielle du Brevet de Technicien Supérieur d'État et inscription au registre national.</p>
                <div class="text-[11px] font-mono text-[#F5B800] font-bold pt-1">Consécration Bac+2</div>
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
                    [Garantie Légale & Reconnaissance]
                </span>

                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal text-white leading-tight">
                    Un Jury souverain et un titre à haute valeur ajoutée.
                </h2>

                <p class="text-sm sm:text-base text-white/70 leading-relaxed font-light">
                    Le diplôme de BTS délivré par la voie de la VAE est strictement identique à celui obtenu par la formation initiale. Il confère les mêmes prérogatives légales et professionnelles.
                </p>

                <div class="grid sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#F5B800]">
                            <x-lucide-check class="w-4 h-4" />
                            <span>REVALORISATION DE CARRIÈRE</span>
                        </div>
                        <p class="text-xs text-white/60 font-light leading-relaxed">Accès aux échelons de cadres techniques, chefs d'équipes et grilles indiciaires supérieures.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-emerald-400">
                            <x-lucide-check class="w-4 h-4" />
                            <span>RESPONSABILITÉ ERP & SÉCURITÉ</span>
                        </div>
                        <p class="text-xs text-white/60 font-light leading-relaxed">Habilitation à signer les dossiers de sécurité pour les événements recevant du public.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-purple-400">
                            <x-lucide-check class="w-4 h-4" />
                            <span>ACCÈS AUX MARCHÉS PUBLICS</span>
                        </div>
                        <p class="text-xs text-white/60 font-light leading-relaxed">Éligibilité aux appels d'offres d'État exigeant des profils certifiés Bac+2.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-blue-400">
                            <x-lucide-check class="w-4 h-4" />
                            <span>MOBILITÉ INTERNATIONALE</span>
                        </div>
                        <p class="text-xs text-white/60 font-light leading-relaxed">Reconnaissance dans l'espace UEMOA et auprès des diffuseurs internationaux.</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-b from-[#13131c] to-[#1c142c] border border-white/15 shadow-2xl space-y-6">
                    <div class="w-14 h-14 rounded-2xl bg-[#F5B800]/15 text-[#F5B800] flex items-center justify-center border border-[#F5B800]/30">
                        <x-lucide-graduation-cap class="w-7 h-7" />
                    </div>

                    <div>
                        <span class="text-xs font-mono uppercase tracking-wider text-[#F5B800]">COMPOSITION DU JURY D'ÉTAT</span>
                        <h3 class="text-2xl font-serif font-bold text-white mt-1">L'Élite des Professionnels</h3>
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
                            <span>Représentants officiels des Ministères de tutelle</span>
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
                    Non. C'est précisément l'objectif de la VAE : permettre à des techniciens en activité, qu'ils soient autodidactes ou diplômés du CPS, d'accéder au niveau supérieur BTS (Bac+2) sur la base exclusive de leurs compétences et réalisations sur le terrain.
                </div>
            </div>

            {{-- FAQ 2 --}}
            <div class="rounded-2xl bg-white/[0.03] border border-white/10 overflow-hidden transition">
                <button
                    type="button"
                    @click="activeFaq = (activeFaq === 2 ? null : 2)"
                    class="w-full p-5 sm:p-6 text-left flex items-center justify-between gap-4"
                >
                    <span class="text-sm sm:text-base font-bold text-white">Puis-je continuer à travailler pendant mon accompagnement VAE ?</span>
                    <span class="transition-transform duration-300" :class="activeFaq === 2 ? 'rotate-180' : ''">
                        <x-lucide-chevron-down class="w-5 h-5 text-[#F5B800]" />
                    </span>
                </button>
                <div x-show="activeFaq === 2" x-collapse class="px-5 sm:px-6 pb-6 text-xs sm:text-sm text-white/70 font-light leading-relaxed border-t border-white/5 pt-4">
                    Oui, absolument. Le calendrier pédagogique du Volet 2 est spécialement adapté aux professionnels en activité. Les séances de tutorat individuel et les ateliers d'écriture se déroulent en horaires aménagés et peuvent être suivis en partie à distance.
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
                    Le parcours complet s'étend sur 9 mois. Il comprend la recevabilité du Livret 1, la rédaction méthodique du Livret 2, la mise en situation technique sur les régies du Grand Théâtre, la simulation en jury blanc et la soutenance finale officielle.
                </div>
            </div>

            {{-- FAQ 4 --}}
            <div class="rounded-2xl bg-white/[0.03] border border-white/10 overflow-hidden transition">
                <button
                    type="button"
                    @click="activeFaq = (activeFaq === 4 ? null : 4)"
                    class="w-full p-5 sm:p-6 text-left flex items-center justify-between gap-4"
                >
                    <span class="text-sm sm:text-base font-bold text-white">Quelle est la valeur officielle du diplôme obtenu ?</span>
                    <span class="transition-transform duration-300" :class="activeFaq === 4 ? 'rotate-180' : ''">
                        <x-lucide-chevron-down class="w-5 h-5 text-[#F5B800]" />
                    </span>
                </button>
                <div x-show="activeFaq === 4" x-collapse class="px-5 sm:px-6 pb-6 text-xs sm:text-sm text-white/70 font-light leading-relaxed border-t border-white/5 pt-4">
                    Le diplôme obtenu est le Brevet de Technicien Supérieur (BTS) d'État, délivré sous le contrôle direct du Ministère de la Formation Professionnelle. Il a exactement la même valeur juridique et académique qu'un BTS obtenu après deux ans de scolarité classique.
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
                    Ne laissez pas votre savoir-faire sans reconnaissance officielle. Nos conseillers VAE sont à votre disposition pour analyser gratuitement votre dossier initial et vous orienter vers la filière BTS la plus pertinente.
                </p>

                <div class="pt-4 grid sm:grid-cols-2 gap-4">
                    <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 space-y-1">
                        <div class="text-xs font-mono text-[#F5B800]">PÔLE CONSEIL VAE</div>
                        <div class="text-sm font-semibold text-white">Grand Théâtre National, Dakar</div>
                        <div class="text-xs text-white/50">Entretiens d'orientation sur rendez-vous</div>
                    </div>

                    <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 space-y-1">
                        <div class="text-xs font-mono text-[#F5B800]">LIGNE DIRECTE VAE</div>
                        <div class="text-sm font-semibold text-white">{{ $siteSettings?->phone ?? '+221 33 800 00 00' }}</div>
                        <div class="text-xs text-white/50">{{ $siteSettings?->email ?? 'contact@emsi.sn' }}</div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-b from-[#13131c] to-[#1c142c] border border-white/15 shadow-2xl space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#F5B800]/15 text-[#F5B800] text-xs font-bold font-mono">
                        <span>SESSION VAE 2026-2027</span>
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
