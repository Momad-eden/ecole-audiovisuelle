@extends('layouts.public')

@section('title', 'Le Grand Théâtre & L’École — EMSI Dakar | Sanctuaire des Arts Audiovisuels & Scéniques')
@section('description', 'Explorez sous tous ses angles le Grand Théâtre National de Dakar et le campus EMSI : auditorium 1 800 places, cage de scène 45m, régie broadcast 4K, studios son -60dB, laboratoire LED XR et façade monumentale.')

@section('content')

{{-- =========================================================
     1. HERO MONUMENTAL DU GRAND THÉÂTRE & CAMPUS EMSI
========================================================= --}}
<section
    id="hero-sanctuaire"
    class="relative min-h-[92vh] lg:min-h-screen bg-[#050507] text-white flex flex-col justify-between overflow-hidden"
    x-data="{
        activeAngle: 'facade', // facade | salle | grill | regie | studio | lab
        timecode: '00:01:24:18',
        init() {
            let frames = 18;
            let seconds = 24;
            let minutes = 1;
            setInterval(() => {
                frames++;
                if (frames >= 24) {
                    frames = 0;
                    seconds++;
                    if (seconds >= 60) {
                        seconds = 0;
                        minutes++;
                    }
                }
                const pad = (n) => n.toString().padStart(2, '0');
                this.timecode = `00:${pad(minutes)}:${pad(seconds)}:${pad(frames)}`;
            }, 1000 / 24);
        }
    }"
>
    {{-- Arrière-plan héroïque avec photo monumentale et voile cinématographique --}}
    <div class="absolute inset-0 z-0">
        <img
            src="{{ asset('images/lieux/facade_monumentale.jpg') }}"
            alt="Façade Monumentale du Grand Théâtre National de Dakar"
            class="w-full h-full object-cover object-center transform scale-105 filter brightness-[0.45] contrast-[1.15]"
        >
        {{-- Dégradés de profondeur --}}
        <div class="absolute inset-0 bg-gradient-to-t from-[#050507] via-[#050507]/60 to-[#050507]/80"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,transparent_0%,rgba(5,5,7,0.85)_80%)]"></div>
        {{-- Grille Studio --}}
        <div class="absolute inset-0 opacity-[0.04] bg-[linear-gradient(to_right,#ffffff_1px,transparent_1px),linear-gradient(to_bottom,#ffffff_1px,transparent_1px)] [background-size:40px_40px]"></div>
    </div>

    {{-- Overlay HUD Viseur Cinéma --}}
    <div class="absolute inset-4 sm:inset-8 lg:inset-12 pointer-events-none z-20 border border-white/10 rounded-3xl flex flex-col justify-between p-4 sm:p-6 lg:p-8">
        {{-- Coins du viseur cinéma (Crosshairs) --}}
        <div class="absolute -top-1 -left-1 w-6 h-6 border-t-2 border-l-2 border-[#F5B800]"></div>
        <div class="absolute -top-1 -right-1 w-6 h-6 border-t-2 border-r-2 border-[#F5B800]"></div>
        <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-2 border-l-2 border-[#F5B800]"></div>
        <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-2 border-r-2 border-[#F5B800]"></div>

        {{-- Barre Supérieure HUD --}}
        <div class="flex items-center justify-between text-[10px] sm:text-xs font-mono tracking-widest text-white/70">
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-950/80 border border-rose-500/40 text-rose-400 font-bold">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                    DIRECT [4K]
                </span>
                <span class="hidden sm:inline text-[#F5B800]" x-text="timecode">00:01:24:18</span>
                <span class="hidden md:inline text-white/40">|</span>
                <span class="hidden md:inline text-white/60">GRAND THÉÂTRE NATIONAL &bull; DAKAR</span>
            </div>

            <div class="flex items-center gap-4 text-white/80">
                <span>JAUGE <strong class="text-[#F5B800]">1 800 PLACES</strong></span>
                <span class="hidden sm:inline">HAUTEUR SCÈNE <strong class="text-white">45M</strong></span>
                <span class="hidden md:inline">RÉSEAU <strong class="text-emerald-400">DANTE / SMPTE</strong></span>
            </div>
        </div>

        {{-- Barre Inférieure HUD --}}
        <div class="flex items-center justify-between text-[10px] sm:text-xs font-mono text-white/60">
            <div class="flex items-center gap-2">
                <span class="text-[#F5B800]">EMSI CAMPUS SPATIAL</span>
                <span class="text-white/30">&bull;</span>
                <span>SANCTUAIRE DES MÉTIERS DU SPECTACLE</span>
            </div>

            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span class="text-emerald-400 font-bold">6 RÉGIES OPÉRATIONNELLES</span>
            </div>
        </div>
    </div>

    {{-- Contenu Principal Hero --}}
    <div class="relative z-30 max-w-7xl mx-auto w-full px-6 sm:px-10 lg:px-16 pt-28 sm:pt-36 pb-12 flex-1 flex flex-col justify-between">
        <div>
            <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-white/[0.08] border border-white/20 backdrop-blur-xl text-xs font-mono uppercase tracking-[0.2em] text-[#F5B800] mb-6">
                <x-lucide-building-2 class="w-3.5 h-3.5 text-[#F5B800]" />
                <span>Le Sanctuaire National du Spectacle Vivant & du Broadcast</span>
            </div>

            <h1 class="font-serif text-4xl sm:text-6xl lg:text-7xl xl:text-8xl font-normal tracking-tight text-white leading-[1.04] max-w-5xl">
                Le Grand Théâtre National <br>
                <span class="italic text-transparent bg-clip-text bg-gradient-to-r from-[#F5B800] via-[#FFE59E] to-white font-light">sous tous ses angles.</span>
            </h1>

            <p class="mt-8 text-base sm:text-lg lg:text-xl text-white/80 font-light leading-relaxed max-w-3xl">
                Entrez dans les coulisses du <strong class="text-white font-medium">plus grand complexe scénique d'Afrique de l'Ouest</strong>. L'EMSI forme l'élite des techniciens du son, de la lumière, de la vidéo broadcast et de la scénographie au cœur même de ce monument d'exception.
            </p>
        </div>

        {{-- Cartouche de statistiques d'infrastructures réelles --}}
        <div class="mt-10 pt-8 border-t border-white/10 grid grid-cols-2 md:grid-cols-4 gap-6 pointer-events-auto">
            <div class="space-y-1">
                <div class="text-2xl sm:text-3xl font-serif font-bold text-[#F5B800]">1 800</div>
                <div class="text-xs font-mono uppercase tracking-wider text-white/60">Places Auditorium</div>
                <div class="text-[11px] text-white/40">3 niveaux de balcons dorés</div>
            </div>
            <div class="space-y-1">
                <div class="text-2xl sm:text-3xl font-serif font-bold text-white">45 m</div>
                <div class="text-xs font-mono uppercase tracking-wider text-white/60">Sous Grill Technique</div>
                <div class="text-[11px] text-white/40">30 tonnes de levage motorisé</div>
            </div>
            <div class="space-y-1">
                <div class="text-2xl sm:text-3xl font-serif font-bold text-[#F5B800]">6 Régies</div>
                <div class="text-xs font-mono uppercase tracking-wider text-white/60">Broadcast & Live</div>
                <div class="text-[11px] text-white/40">4K SDI 12G & Dante Network</div>
            </div>
            <div class="space-y-1">
                <div class="text-2xl sm:text-3xl font-serif font-bold text-white">-60 dB</div>
                <div class="text-xs font-mono uppercase tracking-wider text-white/60">Isolation Studios</div>
                <div class="text-[11px] text-white/40">Cabines flottantes & Dolby Atmos</div>
            </div>
        </div>

        {{-- Barres de navigation d'accès direct --}}
        <div class="mt-8 flex flex-wrap items-center justify-between gap-4 pointer-events-auto">
            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="#visite-interactive"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#F5B800] text-black font-bold text-xs uppercase tracking-wider hover:bg-white transition shadow-xl"
                >
                    <x-lucide-compass class="w-4 h-4" />
                    <span>Visiter les 6 Lieux Clés</span>
                </a>
                <a
                    href="#architecture-monument"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white/10 text-white font-semibold text-xs uppercase tracking-wider hover:bg-white/20 transition border border-white/15"
                >
                    <x-lucide-layers class="w-4 h-4" />
                    <span>Anatomie & Chiffres</span>
                </a>
                <a
                    href="#vie-campus"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white/10 text-white font-semibold text-xs uppercase tracking-wider hover:bg-white/20 transition border border-white/15"
                >
                    <x-lucide-users class="w-4 h-4" />
                    <span>La Vie au Campus</span>
                </a>
            </div>

            <div class="text-xs font-mono text-white/50 flex items-center gap-2">
                <x-lucide-map-pin class="w-3.5 h-3.5 text-[#F5B800]" />
                <span>Parc Culturel National &bull; Face Gare Maritime, Dakar</span>
            </div>
        </div>
    </div>
</section>


{{-- =========================================================
     2. L'EXPLORATEUR SPATIAL DU GRAND THÉÂTRE & DES LIEUX EMSI
========================================================= --}}
<section
    id="visite-interactive"
    class="py-24 lg:py-36 bg-[#08080c] text-white relative overflow-hidden border-b border-white/10"
    x-data="{
        selectedLieu: 'grande-salle',
        lieux: {
            'grande-salle': {
                titre: 'La Grande Salle d\'Auditorium',
                sousTitre: 'Le joyau acoustique de 1 800 places',
                badge: 'ESPACE A-01 &bull; SCÈNE D\'OPÉRA',
                image: '{{ asset('images/lieux/grande_salle.jpg') }}',
                jauge: '1 800 fauteuils velours',
                hauteur: '3 niveaux de corbeilles & balcons dorés',
                acoustique: 'Temps de réverbération maîtrisé (0.8s)',
                connectique: 'Réseau Dante 64 canaux, multipaires optiques',
                description: 'Conçue selon les standards des plus grands opéras internationaux, la Grande Salle offre une acoustique d\'une pureté absolue avec une absorption latérale contrôlée et une visibilité parfaite depuis chaque siège.',
                pratique: 'Nos étudiants en Ingénierie Son y calent les façades Line Array (L-Acoustics / d&b) et mixent des orchestres philharmoniques ou des méga-concerts en direct.',
                points: [
                    'Fosse d\'orchestre mobile escamotable pour 80 musiciens',
                    '3 balcons courbés avec dorures architecturales sculptées',
                    'Passerelles techniques intégrées au plafond pour projecteurs de face',
                    'Cabines de régie de salle vitrées et insonorisées'
                ]
            },
            'grill-technique': {
                titre: 'Le Plateau Scénique & Le Grill Technique',
                sousTitre: '45 mètres sous plafond et 30 tonnes de levage',
                badge: 'ESPACE B-02 &bull; CAGE DE SCÈNE',
                image: '{{ asset('images/lieux/grill_technique.jpg') }}',
                jauge: 'Plateau scénique de 45m d\'ouverture',
                hauteur: '45.00 m sous grill motorisé',
                acoustique: 'Cage noire absorbante avec draperies velours ignifugées',
                connectique: 'DMX512 / RDM / Art-Net / sACN / 400A Triphasé',
                description: 'La plus haute cage de scène de l\'Afrique de l\'Ouest. Une structure d\'ingénierie suspendue vertigineuse équipée de passerelles de circulation, de perches motorisées informatisées et d\'un parc complet de projecteurs asservis.',
                pratique: 'Les apprentis régisseurs et éclairagistes apprennent le rigging de sécurité, l\'accroche de ponts Prolyte et l\'encodage de shows timecodés sur consoles GrandMA3.',
                points: [
                    '42 porteuses motorisées informatisées à vitesse variable',
                    'Passerelles de grill à double garde-corps pour interventions en hauteur',
                    'Parc de projecteurs asservis Beam, Spot, Wash et découpes théâtrales',
                    'Trappes de scène, dessous de scène et monte-décors industriels'
                ]
            },
            'regie-broadcast': {
                titre: 'La Régie Vidéo Broadcast & Cadrage Sportif',
                sousTitre: 'Le centre névralgique de la captation live 4K',
                badge: 'ESPACE C-03 &bull; BROADCAST ROOM',
                image: '{{ asset('images/lieux/regie_broadcast.jpg') }}',
                jauge: '6 postes opérateurs simultanés',
                hauteur: 'Matrice SDI 12G 40x40 & Fibre SMPTE',
                acoustique: 'Traitement acoustique régie & écoute multicanale',
                connectique: 'Fibre hybride SMPTE 311M, Tally, Intercom Clear-Com',
                description: 'Une régie de production digne des plus grands diffuseurs internationaux. Mur de monitoring multiviewer 4K HDR, mélangeurs de production, pupitres de ralentis Super Slow Motion et système d\'interphonie d\'ordre numérique.',
                pratique: 'Les étudiants alternent entre la réalisation multicaméra en direct, le cadrage plateau sur optiques broadcast 80x et l\'édition instantanée de ralentis sportifs.',
                points: [
                    'Mélangeurs Blackmagic ATEM Constellation 8K avec panneaux 2 M/E',
                    'Parc de caméras Broadcast Studio URSA G2 sur tourelles et pieds fluides',
                    'Serveurs de ralentis vidéo instantanés pour les captations sportives',
                    'Matrice de communication et casques d\'ordres isolés phoniquement'
                ]
            },
            'studio-son': {
                titre: 'Les Studios de Mixage & Cabines Voix-Off',
                sousTitre: 'Sanctuaire acoustique d\'une isolation de -60 dB',
                badge: 'ESPACE D-04 &bull; AUDIO RECORDING',
                image: '{{ asset('images/lieux/studio_son.jpg') }}',
                jauge: 'Cabine de contrôle + Live Room acoustique',
                hauteur: 'Plafond suspendu désolidarisé & diffuseurs QRD',
                acoustique: 'Isolation phonique -60 dB, bruit résiduel NC-15',
                connectique: 'Stagebox Dante 32 In / 16 Out, Préamplis Neve',
                description: 'Conçus comme une boîte dans la boîte sur plots antivibratoires, nos studios son permettent la prise de son instrumentale de haute volée, le mixage multicanal, le doublage voix-off et la post-production cinématographique.',
                pratique: 'Prise de son de percussions traditionnelles sabar et kora, mixage sur consoles numériques DiGiCo et mastering aux normes de diffusion streaming et radio.',
                points: [
                    'Consoles numériques DiGiCo SD Series et Yamaha CL Series',
                    'Parc de microphones de légende : Neumann U87, DPA, Shure KSM',
                    'Système d\'écoute de monitoring amplifié Genelec & Neumann',
                    'Cabines voix-off traitées en bois noble avec vitrage acoustique feuilleté'
                ]
            },
            'lab-motion': {
                titre: 'Le Laboratoire Numérique & Mur LED XR',
                sousTitre: 'L\'intersection du spectacle et des technologies virtuelles',
                badge: 'ESPACE E-05 &bull; XR & MOTION DESIGN',
                image: '{{ asset('images/lieux/lab_motion.jpg') }}',
                jauge: '16 stations graphiques haute performance',
                hauteur: 'Mur LED incurvé pitch 1.9mm Novastar',
                acoustique: 'Espace climatisé silencieux & calibration lumière D65',
                connectique: 'Serveurs de médias Unreal Engine 5 & Resolume Arena',
                description: 'Un laboratoire ultra-moderne dédié à la création d\'environnements 3D temps réel pour murs d\'écrans géants, à l\'habillage vidéo de scènes de concerts, au vidéo-mapping architectural et à l\'étalonnage DaVinci Resolve.',
                pratique: 'Conception d\'identités graphiques animées, synchronisation vidéo en temps réel sur la musique (Resolume/Notch) et étalonnage cinéma sur moniteurs calibrés.',
                points: [
                    'Mur d\'écrans géants LED P1.9 avec processeurs de mur Novastar 4K',
                    'Stations de travail équipées de GPU RTX 4090 pour rendu 3D immédiat',
                    'Pupitres d\'étalonnage DaVinci Resolve Mini Panel',
                    'Pipeline complet d\'intégration 3D temps réel avec Unreal Engine'
                ]
            },
            'facade-monumentale': {
                titre: 'La Façade Monumentale & L\'Esplanade',
                sousTitre: 'Le repère architectural emblématique de Dakar',
                badge: 'ESPACE F-06 &bull; ESPLANADE & ARCHITECTURE',
                image: '{{ asset('images/lieux/facade_monumentale.jpg') }}',
                jauge: 'Esplanade piétonne extérieure de 15 000 m²',
                hauteur: 'Façade sculpturale en courbe de 35 mètres',
                acoustique: 'Ouverture directe sur la brise marine de la baie',
                connectique: 'Points de raccordement régies mobiles & fibres de façade',
                description: 'Inauguré en 2011, le Grand Théâtre National Doudou Ndiaye Coumba Rose dresse ses formes sculpturales blanches face à la baie de Dakar. Son esplanade monumentale accueille festivals en plein air et captations grand public.',
                pratique: 'Déploiement de régies mobiles extérieures (Car-Régie OB Van), sonorisation d\'espaces ouverts et captations aériennes par drone homologué.',
                points: [
                    'Emplacement d\'exception face à la gare maritime de Dakar',
                    'Accès direct aux réseaux de transport modernes TER et BRT',
                    'Plateforme technique pour déploiement de scènes extérieures',
                    'Architecture bioclimatique contemporaine inspirée des vagues de l\'Atlantique'
                ]
            }
        }
    }"
>
    <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10">

        {{-- Titre de Section --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-12 border-b border-white/10">
            <div class="max-w-3xl">
                <span class="text-xs font-mono uppercase tracking-[0.25em] text-[#F5B800] block mb-3 font-bold">
                    [Visite Spatiale Interactive]
                </span>
                <h2 class="text-3xl sm:text-5xl font-serif font-normal tracking-tight text-white leading-tight">
                    Explorez les 6 espaces d'exception du Grand Théâtre.
                </h2>
            </div>
            <div class="max-w-md">
                <p class="text-sm text-white/70 leading-relaxed font-light">
                    Cliquez sur un espace pour inspecter sa configuration technique, ses équipements réels et les activités pédagogiques conduites par nos apprenants.
                </p>
            </div>
        </div>

        {{-- SÉLECTEUR D'ONGLETS / LIEUX --}}
        <div class="mt-8 flex flex-wrap gap-2 sm:gap-3 p-2 rounded-2xl bg-white/[0.03] border border-white/10">
            
            <button
                type="button"
                @click="selectedLieu = 'grande-salle'"
                class="flex-1 min-w-[150px] sm:min-w-[180px] px-4 py-3 rounded-xl text-xs font-mono font-bold transition-all duration-300 flex items-center justify-center gap-2 border text-center"
                :class="selectedLieu === 'grande-salle' ? 'bg-[#F5B800] text-black border-[#F5B800] shadow-[0_0_25px_rgba(245,184,0,0.35)]' : 'bg-transparent text-white/70 border-transparent hover:bg-white/5 hover:text-white'"
            >
                <x-lucide-mic-2 class="w-4 h-4 flex-shrink-0" />
                <span>01. Grande Salle (1 800p)</span>
            </button>

            <button
                type="button"
                @click="selectedLieu = 'grill-technique'"
                class="flex-1 min-w-[150px] sm:min-w-[180px] px-4 py-3 rounded-xl text-xs font-mono font-bold transition-all duration-300 flex items-center justify-center gap-2 border text-center"
                :class="selectedLieu === 'grill-technique' ? 'bg-[#F5B800] text-black border-[#F5B800] shadow-[0_0_25px_rgba(245,184,0,0.35)]' : 'bg-transparent text-white/70 border-transparent hover:bg-white/5 hover:text-white'"
            >
                <x-lucide-sun class="w-4 h-4 flex-shrink-0" />
                <span>02. Grill & Cage Scène (45m)</span>
            </button>

            <button
                type="button"
                @click="selectedLieu = 'regie-broadcast'"
                class="flex-1 min-w-[150px] sm:min-w-[180px] px-4 py-3 rounded-xl text-xs font-mono font-bold transition-all duration-300 flex items-center justify-center gap-2 border text-center"
                :class="selectedLieu === 'regie-broadcast' ? 'bg-[#F5B800] text-black border-[#F5B800] shadow-[0_0_25px_rgba(245,184,0,0.35)]' : 'bg-transparent text-white/70 border-transparent hover:bg-white/5 hover:text-white'"
            >
                <x-lucide-video class="w-4 h-4 flex-shrink-0" />
                <span>03. Régie Vidéo 4K</span>
            </button>

            <button
                type="button"
                @click="selectedLieu = 'studio-son'"
                class="flex-1 min-w-[150px] sm:min-w-[180px] px-4 py-3 rounded-xl text-xs font-mono font-bold transition-all duration-300 flex items-center justify-center gap-2 border text-center"
                :class="selectedLieu === 'studio-son' ? 'bg-[#F5B800] text-black border-[#F5B800] shadow-[0_0_25px_rgba(245,184,0,0.35)]' : 'bg-transparent text-white/70 border-transparent hover:bg-white/5 hover:text-white'"
            >
                <x-lucide-sliders class="w-4 h-4 flex-shrink-0" />
                <span>04. Studios Son (-60dB)</span>
            </button>

            <button
                type="button"
                @click="selectedLieu = 'lab-motion'"
                class="flex-1 min-w-[150px] sm:min-w-[180px] px-4 py-3 rounded-xl text-xs font-mono font-bold transition-all duration-300 flex items-center justify-center gap-2 border text-center"
                :class="selectedLieu === 'lab-motion' ? 'bg-[#F5B800] text-black border-[#F5B800] shadow-[0_0_25px_rgba(245,184,0,0.35)]' : 'bg-transparent text-white/70 border-transparent hover:bg-white/5 hover:text-white'"
            >
                <x-lucide-monitor-play class="w-4 h-4 flex-shrink-0" />
                <span>05. Lab Numérique & XR</span>
            </button>

            <button
                type="button"
                @click="selectedLieu = 'facade-monumentale'"
                class="flex-1 min-w-[150px] sm:min-w-[180px] px-4 py-3 rounded-xl text-xs font-mono font-bold transition-all duration-300 flex items-center justify-center gap-2 border text-center"
                :class="selectedLieu === 'facade-monumentale' ? 'bg-[#F5B800] text-black border-[#F5B800] shadow-[0_0_25px_rgba(245,184,0,0.35)]' : 'bg-transparent text-white/70 border-transparent hover:bg-white/5 hover:text-white'"
            >
                <x-lucide-landmark class="w-4 h-4 flex-shrink-0" />
                <span>06. Façade & Esplanade</span>
            </button>

        </div>

        {{-- AFFICHAGE DÉTAILLÉ DU LIEU ACTIF --}}
        <div class="mt-8 rounded-3xl bg-gradient-to-b from-[#111118] to-[#0a0a0f] border border-white/15 p-6 sm:p-10 shadow-2xl overflow-hidden">
            
            <template x-for="(lieuData, lieuKey) in lieux" :key="lieuKey">
                <div x-show="selectedLieu === lieuKey" x-transition.opacity.duration.400ms class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    
                    {{-- COLONNE GAUCHE : PHOTO IMMERSIVE & BADGES --}}
                    <div class="lg:col-span-7 space-y-4">
                        <div class="relative rounded-2xl overflow-hidden border border-white/20 shadow-2xl aspect-[16/10] group">
                            <img
                                :src="lieuData.image"
                                :alt="lieuData.titre"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                            >
                            {{-- Voile et tag --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/20"></div>
                            
                            <div class="absolute top-4 left-4">
                                <span class="px-3.5 py-1.5 rounded-full bg-black/70 backdrop-blur-md border border-white/20 text-[#F5B800] text-xs font-mono font-bold uppercase tracking-wider" x-text="lieuData.badge">
                                </span>
                            </div>

                            <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between text-xs font-mono text-white/80 bg-black/60 backdrop-blur-md px-4 py-2 rounded-xl border border-white/10">
                                <span>ANGLE IMMERSIF RÉEL</span>
                                <span class="text-[#F5B800]">VUE CERTIFIÉE EMSI</span>
                            </div>
                        </div>

                        {{-- Grille de spécifications techniques immédiates --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                            <div class="p-3 rounded-xl bg-white/[0.04] border border-white/10">
                                <div class="text-[10px] font-mono text-[#F5B800] uppercase">Jauge / Échelle</div>
                                <div class="text-xs font-semibold text-white mt-1 truncate" x-text="lieuData.jauge"></div>
                            </div>
                            <div class="p-3 rounded-xl bg-white/[0.04] border border-white/10">
                                <div class="text-[10px] font-mono text-[#F5B800] uppercase">Dimension / Hauteur</div>
                                <div class="text-xs font-semibold text-white mt-1 truncate" x-text="lieuData.hauteur"></div>
                            </div>
                            <div class="p-3 rounded-xl bg-white/[0.04] border border-white/10">
                                <div class="text-[10px] font-mono text-[#F5B800] uppercase">Acoustique / Isolation</div>
                                <div class="text-xs font-semibold text-white mt-1 truncate" x-text="lieuData.acoustique"></div>
                            </div>
                            <div class="p-3 rounded-xl bg-white/[0.04] border border-white/10">
                                <div class="text-[10px] font-mono text-[#F5B800] uppercase">Connectivité</div>
                                <div class="text-xs font-semibold text-white mt-1 truncate" x-text="lieuData.connectique"></div>
                            </div>
                        </div>
                    </div>

                    {{-- COLONNE DROITE : DESCRIPTION, PÉDAGOGIE & POINTS FORTS --}}
                    <div class="lg:col-span-5 space-y-6">
                        <div>
                            <span class="text-xs font-mono uppercase tracking-widest text-[#F5B800] font-bold block mb-1" x-text="lieuData.sousTitre"></span>
                            <h3 class="text-2xl sm:text-3xl font-serif font-bold text-white" x-text="lieuData.titre"></h3>
                            <p class="mt-3 text-sm text-white/75 font-light leading-relaxed" x-text="lieuData.description"></p>
                        </div>

                        {{-- Ce que les apprenants font dans cette salle --}}
                        <div class="p-4 rounded-2xl bg-gradient-to-r from-[#310181]/40 to-[#180a33] border border-[#310181]/80 space-y-2">
                            <div class="flex items-center gap-2 text-xs font-mono font-bold text-[#F5B800]">
                                <x-lucide-zap class="w-4 h-4 text-[#F5B800]" />
                                <span>IMMERSION PÉDAGOGIQUE RÉELLE</span>
                            </div>
                            <p class="text-xs text-white/85 leading-relaxed font-light" x-text="lieuData.pratique"></p>
                        </div>

                        {{-- Caractéristiques phares --}}
                        <div class="space-y-2">
                            <div class="text-xs font-mono uppercase tracking-wider text-white/50">Équipements & Particularités :</div>
                            <ul class="space-y-2">
                                <template x-for="point in lieuData.points" :key="point">
                                    <li class="flex items-start gap-2.5 text-xs text-white/80 font-light">
                                        <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                                        <span x-text="point"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        {{-- Bouton d'action contextuel --}}
                        <div class="pt-4 border-t border-white/10 flex items-center justify-between">
                            <a
                                href="{{ route('public.courses.index') }}"
                                class="inline-flex items-center gap-2 text-xs font-mono font-bold text-[#F5B800] hover:text-white transition"
                            >
                                <span>Voir les formations associées à ce lieu</span>
                                <x-lucide-arrow-right class="w-4 h-4" />
                            </a>
                        </div>
                    </div>

                </div>
            </template>

        </div>

    </div>
</section>


{{-- =========================================================
     3. BLUEPRINT ARCHITECTURAL & CHIFFRES CLÉS DU MONUMENT
========================================================= --}}
<section id="architecture-monument" class="py-24 lg:py-36 bg-[#050507] text-white relative overflow-hidden border-b border-white/10">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10">

        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
            <span class="text-xs font-mono uppercase tracking-[0.25em] text-[#F5B800] font-bold">
                [Anatomie & Ingénierie du Sanctuaire]
            </span>
            <h2 class="text-3xl sm:text-5xl font-serif font-normal text-white">
                Un chef-d'œuvre architectural dédié aux arts de la scène.
            </h2>
            <p class="text-sm sm:text-base text-white/60 leading-relaxed font-light">
                Le Grand Théâtre National Doudou Ndiaye Coumba Rose a été conçu pour réunir sous un même toit toutes les exigences du spectacle vivant, de l'opéra et du broadcast télévisuel de pointe.
            </p>
        </div>

        {{-- SCHÉMA EN COUPE & CHIFFRES D'INGÉNIERIE --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- 1. La Cage de Scène --}}
            <div class="p-8 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/50 transition-all duration-300 space-y-5 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs font-mono text-[#F5B800]">
                        <span>COUPE VERTICALE</span>
                        <span>H = 45.00 M</span>
                    </div>
                    <h3 class="text-xl font-bold text-white">La Cage de Scène Monumentale</h3>
                    <p class="text-xs text-white/70 leading-relaxed font-light">
                        Une hauteur sous plafond de 45 mètres permettant l'escamotage complet des décors lourds à la vue du public, le déploiement de rideaux de fer pare-flamme et la manipulation de 42 porteuses motorisées.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/10 text-xs font-mono text-white/50 space-y-1">
                    <div>Capacité de levage : <strong class="text-white">30 Tonnes</strong></div>
                    <div>Machinerie : <strong class="text-white">Informatisée & Synchrone</strong></div>
                </div>
            </div>

            {{-- 2. La Diffusion Électro-Acoustique --}}
            <div class="p-8 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-emerald-400/50 transition-all duration-300 space-y-5 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs font-mono text-emerald-400">
                        <span>CALAGE ACOUSTIQUE</span>
                        <span>1 800 FAUTEUILS</span>
                    </div>
                    <h3 class="text-xl font-bold text-white">L'Acoustique d'Opéra</h3>
                    <p class="text-xs text-white/70 leading-relaxed font-light">
                        Une géométrie en fer à cheval avec 3 niveaux de corbeilles dorées. L'étude acoustique volumétrique permet une intelligibilité vocale parfaite sans fatigue auditive, du parterre au dernier balcon.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/10 text-xs font-mono text-white/50 space-y-1">
                    <div>Temps de réverbération : <strong class="text-white">0,8 à 1,2 s</strong></div>
                    <div>Diffusion : <strong class="text-white">Line Array Multidiffusé</strong></div>
                </div>
            </div>

            {{-- 3. Le Réseau Fibre Optique & Dante --}}
            <div class="p-8 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-purple-400/50 transition-all duration-300 space-y-5 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs font-mono text-purple-400">
                        <span>INFRASTRUCTURE RÉSEAU</span>
                        <span>12 KM DE FIBRE</span>
                    </div>
                    <h3 class="text-xl font-bold text-white">L'Interconnexion 10 Gbps</h3>
                    <p class="text-xs text-white/70 leading-relaxed font-light">
                        Une dorsale en fibre optique monomode et multimode reliant les régies de salle, les cabines régies broadcast, les studios son, la scène et les points de raccordement extérieurs de car-régie.
                    </p>
                </div>
                <div class="pt-4 border-t border-white/10 text-xs font-mono text-white/50 space-y-1">
                    <div>Protocole Audio : <strong class="text-white">Dante Audinate Gigabit</strong></div>
                    <div>Protocole Vidéo : <strong class="text-white">12G-SDI & SMPTE 2110</strong></div>
                </div>
            </div>

        </div>

    </div>
</section>


{{-- =========================================================
     4. LA VIE AU CAMPUS : 24H EN IMMERSION AU GRAND THÉÂTRE
========================================================= --}}
<section id="vie-campus" class="py-24 lg:py-36 bg-[#08080c] text-white relative overflow-hidden border-b border-white/10">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10">

        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-16 border-b border-white/10">
            <div class="max-w-3xl">
                <span class="text-xs font-mono uppercase tracking-[0.25em] text-[#F5B800] block mb-3 font-bold">
                    [Immersion Réelle au Quotidien]
                </span>
                <h2 class="text-3xl sm:text-5xl font-serif font-normal tracking-tight text-white leading-tight">
                    24 heures dans la vie d'un technicien formé à l'EMSI.
                </h2>
            </div>
            <div class="max-w-md">
                <p class="text-sm text-white/70 leading-relaxed font-light">
                    À l'EMSI, l'apprentissage s'effectue dans le feu de l'action. Pas de simulations théoriques : des spectacles réels, des artistes en résidence et l'épreuve du direct.
                </p>
            </div>
        </div>

        {{-- CHRONOLOGIE D'UNE JOURNÉE TECHNIQUE --}}
        <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- 08H30 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition duration-300 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full bg-white/10 text-white font-mono text-xs font-bold">08H30</span>
                    <x-lucide-clipboard-check class="w-4 h-4 text-[#F5B800]" />
                </div>
                <h4 class="text-lg font-bold text-white">Briefing & Check-list Fibre</h4>
                <p class="text-xs text-white/65 leading-relaxed font-light">
                    Analyse du rider technique du spectacle du soir, vérification des patchs DMX, test de continuité des liaisons fibre et affectation des postes de régie.
                </p>
                <div class="text-[11px] font-mono text-[#F5B800] pt-2">Lieu : Régie Générale & Coulisses</div>
            </div>

            {{-- 11H00 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition duration-300 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full bg-white/10 text-white font-mono text-xs font-bold">11H00</span>
                    <x-lucide-activity class="w-4 h-4 text-emerald-400" />
                </div>
                <h4 class="text-lg font-bold text-white">Calage Son & Lumière</h4>
                <p class="text-xs text-white/65 leading-relaxed font-light">
                    Mesure acoustique par micro de mesure Smaart dans la Grande Salle, alignement de phase des subwoofers et focus des projecteurs de découpe sur scène.
                </p>
                <div class="text-[11px] font-mono text-emerald-400 pt-2">Lieu : Auditorium 1 800 places</div>
            </div>

            {{-- 15H30 --}}
            <div class="p-6 rounded-3xl bg-white/[0.02] border border-white/10 hover:border-[#F5B800]/40 transition duration-300 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full bg-white/10 text-white font-mono text-xs font-bold">15H30</span>
                    <x-lucide-video class="w-4 h-4 text-purple-400" />
                </div>
                <h4 class="text-lg font-bold text-white">Répétition & Balance Multicam</h4>
                <p class="text-xs text-white/65 leading-relaxed font-light">
                    Essais sons avec les musiciens, cadrage dynamique sur 8 flux broadcast, réglage des colorimétries caméras et synchronisation du timecode spectacle.
                </p>
                <div class="text-[11px] font-mono text-purple-400 pt-2">Lieu : Régie Vidéo 4K & Plateau</div>
            </div>

            {{-- 20H00 --}}
            <div class="p-6 rounded-3xl bg-gradient-to-b from-[#310181]/40 to-[#120a22] border border-[#310181] space-y-4 shadow-xl">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full bg-[#F5B800] text-black font-mono text-xs font-bold">20H00 [DIRECT]</span>
                    <x-lucide-zap class="w-4 h-4 text-[#F5B800]" />
                </div>
                <h4 class="text-lg font-bold text-white">Le Show Devant 1 800 Spectateurs</h4>
                <p class="text-xs text-white/75 leading-relaxed font-light">
                    La salle est comble. Conduite du show lumière en temps réel, mixage façade sous pression, réalisation vidéo sans filet et diffusion live sur les chaînes partenaires.
                </p>
                <div class="text-[11px] font-mono text-[#F5B800] font-bold pt-2">Immersion Totale Réussie</div>
            </div>

        </div>

    </div>
</section>


{{-- =========================================================
     5. L'ÉCOSYSTÈME INSTITUTIONNEL & LES PARTENARIATS D'ÉTAT
========================================================= --}}
<section class="py-24 bg-[#050507] text-white border-b border-white/10">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span class="text-xs font-mono uppercase tracking-[0.25em] text-[#F5B800] font-bold">
                [Reconnaissance & Diplômation]
            </span>
            <h2 class="text-3xl sm:text-4xl font-serif font-normal text-white">
                Un ancrage institutionnel garantissant l'excellence.
            </h2>
            <p class="text-xs sm:text-sm text-white/60 leading-relaxed font-light">
                L'EMSI bénéficie de la double tutelle des ministères sénégalais pour délivrer des certifications et diplômes d'État (BTS / VAE) de renommée internationale.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/10 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-[#F5B800]/15 text-[#F5B800] flex items-center justify-center mx-auto">
                    <x-lucide-landmark class="w-6 h-6" />
                </div>
                <h4 class="text-base font-bold text-white">Grand Théâtre National</h4>
                <p class="text-xs text-white/60 font-light">Résidence permanente, mise à disposition des scènes et régies de prestige.</p>
            </div>

            <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/10 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-emerald-500/15 text-emerald-400 flex items-center justify-center mx-auto">
                    <x-lucide-award class="w-6 h-6" />
                </div>
                <h4 class="text-base font-bold text-white">Ministère de la Formation</h4>
                <p class="text-xs text-white/60 font-light">Homologation des référentiels pédagogiques et délivrance du Brevet de Technicien Supérieur (BTS).</p>
            </div>

            <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/10 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-purple-500/15 text-purple-400 flex items-center justify-center mx-auto">
                    <x-lucide-sparkles class="w-6 h-6" />
                </div>
                <h4 class="text-base font-bold text-white">Ministère de la Culture</h4>
                <p class="text-xs text-white/60 font-light">Accompagnement du patrimoine culturel vivant et des industries créatives sénégalaises.</p>
            </div>

            <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/10 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-blue-500/15 text-blue-400 flex items-center justify-center mx-auto">
                    <x-lucide-tv class="w-6 h-6" />
                </div>
                <h4 class="text-base font-bold text-white">Diffuseurs & Chaînes TV</h4>
                <p class="text-xs text-white/60 font-light">Partenariats de stages et d'embauches directes (RTS, Canal+ Afrique, chaînes privées).</p>
            </div>
        </div>

    </div>
</section>


{{-- =========================================================
     6. PLANIFIER UNE VISITE GUIDÉE DU CAMPUS & CANDIDATURE
========================================================= --}}
<section id="contact" class="py-24 lg:py-32 bg-[#08080c] text-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="grid lg:grid-cols-12 gap-10 lg:gap-14 items-center">

            <div class="lg:col-span-7 space-y-6">
                <span class="text-xs font-mono uppercase tracking-[0.25em] text-[#F5B800] font-bold">
                    [Visite Privée du Monument]
                </span>

                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal text-white leading-tight">
                    Venez fouler les planches du Grand Théâtre.
                </h2>

                <p class="text-sm sm:text-base text-white/70 leading-relaxed font-light">
                    Vous souhaitez découvrir nos régies broadcast, monter sur le grill technique, tester nos studios son et échanger avec nos formateurs sur votre projet professionnel ou votre éligibilité VAE ? Réservez une visite personnalisée.
                </p>

                <div class="pt-4 grid sm:grid-cols-2 gap-4">
                    <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 space-y-1">
                        <div class="text-xs font-mono text-[#F5B800]">ADRESSE PHYSIQUE</div>
                        <div class="text-sm font-semibold text-white">Grand Théâtre National Doudou Ndiaye Coumba Rose</div>
                        <div class="text-xs text-white/50">Parc Culturel National, Voie Express de Dakar &bull; Arrêt TER / BRT Gare de Dakar</div>
                    </div>

                    <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 space-y-1">
                        <div class="text-xs font-mono text-[#F5B800]">LIGNE DIRECTE ADMISSIONS</div>
                        <div class="text-sm font-semibold text-white">{{ $siteSettings?->phone ?? '+221 33 800 00 00' }}</div>
                        <div class="text-xs text-white/50">{{ $siteSettings?->email ?? 'contact@emsi.sn' }}</div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-b from-[#14141d] to-[#1a1429] border border-white/15 shadow-2xl space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#F5B800]/15 text-[#F5B800] text-xs font-bold font-mono">
                        <span>SESSION ADMISSIONS 2026-2027</span>
                    </div>

                    <h3 class="text-2xl font-serif font-bold text-white">
                        Prenez les commandes de votre avenir.
                    </h3>

                    <p class="text-xs sm:text-sm text-white/65 leading-relaxed font-light">
                        Les places en formation initiale et les dossiers de Validation des Acquis de l'Expérience (VAE) sont traités par ordre d'arrivée.
                    </p>

                    <div class="space-y-3 pt-2">
                        <a
                            href="{{ route('public.admissions.create') }}"
                            class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-4 rounded-full bg-[#F5B800] text-black font-bold text-xs uppercase tracking-wider hover:bg-white transition shadow-xl"
                        >
                            <span>Déposer ma candidature en ligne</span>
                            <x-lucide-arrow-up-right class="w-4 h-4" />
                        </a>

                        @if($siteSettings?->whatsapp)
                            <a
                                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings->whatsapp) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition text-center"
                            >
                                <x-lucide-message-circle class="w-4 h-4" />
                                <span>Échanger sur WhatsApp avec l'équipe</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

@endsection
