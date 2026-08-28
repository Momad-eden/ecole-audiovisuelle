@extends('layouts.public')

@section('title', 'L’école — EMSI | École de Formation Audiovisuelle Dakar')
@section('description', 'Découvrez l’EMSI, école de référence des métiers du cinéma et de l’audiovisuel au cœur du Grand Théâtre National Doudou Ndiaye Rose à Dakar.')

@section('content')

{{-- =========================================================
     HERO SECTION — L'ÉCOLE
========================================================= --}}
<section class="relative pt-16 pb-24 lg:pt-24 lg:pb-32 bg-[#080808] text-white overflow-hidden border-b border-white/10">

    {{-- Lueurs de fond --}}
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#320080]/25 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-[#F5B800]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10">

        {{-- Breadcrumb & Tag --}}
        <div class="flex items-center gap-3 text-xs uppercase tracking-widest text-[#F5B800] mb-6 font-bold">
            <a href="{{ route('public.home') }}" class="text-white/40 hover:text-white transition">Accueil</a>
            <span class="text-white/20">/</span>
            <span>L'école</span>
        </div>

        <div class="max-w-3xl">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-normal tracking-tight text-white leading-[1.1]">
                Former les créateurs de l'image et du son de demain.
            </h1>

            <p class="mt-6 text-base sm:text-lg text-white/60 leading-relaxed max-w-2xl font-light">
                Implantée au cœur du prestigieux <span class="text-white font-medium">Grand Théâtre National Doudou Ndiaye Rose</span> à Dakar, l'EMSI est l'institution de référence dédiée à l'apprentissage intensif des métiers du cinéma, de la télévision et des nouveaux médias.
            </p>
        </div>

        {{-- Métriques clés --}}
        <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6 pt-12 border-t border-white/10">
            <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/5 backdrop-blur-sm">
                <p class="text-3xl lg:text-4xl font-serif font-bold text-[#F5B800]">80%</p>
                <p class="text-xs uppercase tracking-wider text-white/50 mt-2 font-medium">Pratique & Tournages</p>
            </div>
            <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/5 backdrop-blur-sm">
                <p class="text-3xl lg:text-4xl font-serif font-bold text-white">4K / 6K</p>
                <p class="text-xs uppercase tracking-wider text-white/50 mt-2 font-medium">Équipements Cinéma</p>
            </div>
            <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/5 backdrop-blur-sm">
                <p class="text-3xl lg:text-4xl font-serif font-bold text-[#F5B800]">100%</p>
                <p class="text-xs uppercase tracking-wider text-white/50 mt-2 font-medium">Intervenants Pros</p>
            </div>
            <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/5 backdrop-blur-sm">
                <p class="text-3xl lg:text-4xl font-serif font-bold text-white">Dakar</p>
                <p class="text-xs uppercase tracking-wider text-white/50 mt-2 font-medium">Grand Théâtre National</p>
            </div>
        </div>

    </div>

</section>


{{-- =========================================================
     MISSION & HISTOIRE
========================================================= --}}
<section class="py-24 lg:py-32 bg-white text-[#111111]">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#C15C03] mb-3">
                    Notre Mission
                </p>

                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal tracking-tight text-[#111111] leading-tight">
                    Une formation taillée pour l'industrie créative africaine et internationale.
                </h2>

                <div class="mt-8 space-y-5 text-black/70 leading-relaxed text-sm sm:text-base font-light">
                    <p>
                        Le secteur audiovisuel sénégalais et africain connaît une expansion sans précédent : séries télévisées diffusées mondialement, longs-métrages primés en festivals, documentaires engagés et création digitale effervescente.
                    </p>
                    <p>
                        Face à ces opportunités, l'EMSI s'est fixé une ambition claire : combler le fossé entre la théorie académique et les exigences réelles des plateaux de tournage. Dès le premier jour, nos étudiants manipulent le matériel, écrivent, cadrent, éclairent, montent et mixent leurs propres œuvres.
                    </p>
                    <p>
                        Notre pédagogie repose sur le mentorat direct assuré par des directeurs de la photographie, ingénieurs du son, réalisateurs et chefs monteurs en activité.
                    </p>
                </div>

                <div class="mt-10 flex items-center gap-4">
                    <a
                        href="{{ route('public.courses.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-[#111111] text-white text-xs font-semibold hover:bg-[#C15C03] transition-colors duration-200"
                    >
                        <span>Explorer nos cursus</span>
                        <x-lucide-arrow-right class="w-4 h-4" />
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="aspect-[4/3] rounded-3xl overflow-hidden bg-gray-100 shadow-2xl border border-black/5">
                    <div class="w-full h-full bg-gradient-to-br from-[#1a1a1a] to-[#2a1050] flex flex-col justify-between p-8 text-white relative overflow-hidden">
                        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#F5B800_1px,transparent_1px)] [background-size:16px_16px]"></div>
                        <div class="relative z-10">
                            <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-white/10 backdrop-blur-md text-[#F5B800]">
                                <x-lucide-award class="w-3.5 h-3.5" />
                                Méthode EMSI
                            </span>
                            <h3 class="text-2xl font-serif mt-4">Apprendre par le geste, créer par la vision.</h3>
                        </div>
                        <div class="relative z-10 pt-8 border-t border-white/10 text-xs text-white/60 space-y-2">
                            <p class="flex items-center gap-2">
                                <x-lucide-check-circle-2 class="w-4 h-4 text-[#F5B800]" />
                                Tournages en conditions réelles de production
                            </p>
                            <p class="flex items-center gap-2">
                                <x-lucide-check-circle-2 class="w-4 h-4 text-[#F5B800]" />
                                Projets de fin d'études encadrés et diffusés
                            </p>
                            <p class="flex items-center gap-2">
                                <x-lucide-check-circle-2 class="w-4 h-4 text-[#F5B800]" />
                                Insertion professionnelle et réseau d'alumni
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     LE CAMPUS — GRAND THÉÂTRE NATIONAL
========================================================= --}}
<section class="py-24 lg:py-32 bg-[#0c0c0c] text-white relative overflow-hidden border-y border-white/10">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="max-w-2xl mb-16">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#F5B800] mb-3">
                Notre Cadre
            </p>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal tracking-tight">
                Un campus au cœur du monument culturel de Dakar.
            </h2>
            <p class="mt-4 text-white/50 text-sm sm:text-base leading-relaxed">
                Étudier à l'EMSI, c'est évoluer au quotidien au sein du Grand Théâtre National Doudou Ndiaye Rose, un carrefour artistique d'exception offrant un cadre stimulant pour la création.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">

            <div class="p-8 rounded-3xl bg-[#141414] border border-white/10 hover:border-[#F5B800]/40 transition duration-300">
                <div class="w-12 h-12 rounded-2xl bg-[#320080] text-[#F5B800] flex items-center justify-center mb-6">
                    <x-lucide-video class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Plateaux & Studios</h3>
                <p class="text-sm text-white/50 leading-relaxed">
                    Espaces modulables pour les cours de cadrage, d'éclairage de studio, de direction d'acteurs et de captation multicaméras.
                </p>
            </div>

            <div class="p-8 rounded-3xl bg-[#141414] border border-white/10 hover:border-[#F5B800]/40 transition duration-300">
                <div class="w-12 h-12 rounded-2xl bg-[#320080] text-[#F5B800] flex items-center justify-center mb-6">
                    <x-lucide-sliders class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Régies & Post-Production</h3>
                <p class="text-sm text-white/50 leading-relaxed">
                    Salles équipées de stations de montage hautes performances, étalonnage DaVinci Resolve et logiciels de sound design professionnels.
                </p>
            </div>

            <div class="p-8 rounded-3xl bg-[#141414] border border-white/10 hover:border-[#F5B800]/40 transition duration-300">
                <div class="w-12 h-12 rounded-2xl bg-[#320080] text-[#F5B800] flex items-center justify-center mb-6">
                    <x-lucide-mic class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Cabine Prise de Son</h3>
                <p class="text-sm text-white/50 leading-relaxed">
                    Espace acoustiquement traité pour l'enregistrement de voix-off, le doublage, les bruitages et la prise de son instrumentale.
                </p>
            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     PARTENAIRES
========================================================= --}}
@if($partners->count())
<section class="py-20 bg-white text-[#111111] border-b border-black/5">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="text-center max-w-xl mx-auto mb-12">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#C15C03] mb-2">
                Notre Réseau
            </p>
            <h2 class="text-2xl sm:text-3xl font-serif text-[#111111]">
                Ils nous font confiance.
            </h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 items-center">
            @foreach($partners as $partner)
                <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center text-center hover:shadow-md transition">
                    @if($partner->logo)
                        <img
                            src="{{ asset('storage/' . $partner->logo) }}"
                            alt="{{ $partner->name }}"
                            class="max-h-12 w-auto object-contain grayscale hover:grayscale-0 transition duration-300"
                        >
                    @else
                        <span class="text-xs font-bold text-gray-700">{{ $partner->name }}</span>
                    @endif
                </div>
            @endforeach
        </div>

    </div>

</section>
@endif


{{-- =========================================================
     CTA FINAL
========================================================= --}}
<section class="py-20 bg-gradient-to-r from-[#1b0045] to-[#320080] text-white">

    <div class="max-w-4xl mx-auto px-6 text-center">

        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif leading-tight">
            Prêt à donner vie à vos projets audiovisuels ?
        </h2>

        <p class="mt-4 text-white/75 text-sm sm:text-base max-w-xl mx-auto font-light">
            Rejoignez la prochaine promotion de l'EMSI. Les candidatures pour les différentes filières sont actuellement ouvertes.
        </p>

        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <a
                href="{{ route('public.admissions.create') }}"
                class="px-8 py-4 rounded-full bg-[#F5B800] text-black font-bold text-sm hover:bg-white transition-colors duration-200 shadow-xl"
            >
                Déposer ma candidature
            </a>
            <a
                href="{{ route('public.courses.index') }}"
                class="px-8 py-4 rounded-full bg-white/10 hover:bg-white/20 text-white font-semibold text-sm border border-white/20 transition-colors duration-200"
            >
                Consulter les formations
            </a>
        </div>

    </div>

</section>

@endsection
