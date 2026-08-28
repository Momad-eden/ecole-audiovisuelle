@extends('layouts.public')

@section('title', 'Nos Formations Audiovisuelles — EMSI Dakar')
@section('description', 'Consultez l’ensemble des cursus professionnels de l’EMSI : Réalisation, Cadre & Caméra, Montage vidéo, Prise de son, Étalonnage et Photographie à Dakar.')

@section('content')

{{-- =========================================================
     HERO SECTION — FORMATIONS
========================================================= --}}
<section class="relative pt-16 pb-20 lg:pt-24 lg:pb-28 bg-[#080808] text-white overflow-hidden border-b border-white/10">

    {{-- Lueur d'ambiance --}}
    <div class="absolute -top-24 right-1/4 w-96 h-96 bg-[#320080]/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-[#F5B800]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-3 text-xs uppercase tracking-widest text-[#F5B800] mb-6 font-bold">
            <a href="{{ route('public.home') }}" class="text-white/40 hover:text-white transition">Accueil</a>
            <span class="text-white/20">/</span>
            <span>Formations</span>
        </div>

        <div class="max-w-3xl">
            <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-[#F5B800] mb-3">
                Programmes Académiques & Professionnels
            </p>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-normal tracking-tight text-white leading-tight">
                Des cursus d'excellence axés sur la pratique.
            </h1>

            <p class="mt-6 text-base sm:text-lg text-white/60 leading-relaxed font-light">
                Choisissez votre parcours de formation parmi nos programmes diplômants et certifiants, encadrés par des professionnels réputés du cinéma et de la télévision.
            </p>
        </div>

    </div>

</section>


{{-- =========================================================
     CATALOGUE & FILTRES
========================================================= --}}
<section class="py-20 bg-[#f8f7f4] text-[#111111]" x-data="{
    activeCategory: '{{ request('category', 'all') }}',
    filterUrl(cat) {
        let url = new URL(window.location.href);
        if (cat === 'all') {
            url.searchParams.delete('category');
        } else {
            url.searchParams.set('category', cat);
        }
        window.location.href = url.toString();
    }
}">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        {{-- Barre de filtrage par catégorie --}}
        @if($categories->count() > 0)
        <div class="flex flex-wrap items-center gap-2 pb-8 border-b border-black/10">

            <button
                type="button"
                @click="filterUrl('all')"
                class="px-5 py-2.5 rounded-full text-xs font-bold tracking-wide transition-all duration-200"
                :class="activeCategory === 'all' ? 'bg-[#111111] text-white shadow-md' : 'bg-white text-black/60 hover:text-black border border-black/10 hover:border-black/30'"
            >
                Toutes les filières ({{ $courses->count() }})
            </button>

            @foreach($categories as $category)
                <button
                    type="button"
                    @click="filterUrl('{{ $category }}')"
                    class="px-5 py-2.5 rounded-full text-xs font-bold tracking-wide transition-all duration-200"
                    :class="activeCategory === '{{ $category }}' ? 'bg-[#111111] text-white shadow-md' : 'bg-white text-black/60 hover:text-black border border-black/10 hover:border-black/30'"
                >
                    {{ $category }}
                </button>
            @endforeach

        </div>
        @endif


        {{-- Grille des Formations --}}
        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            @forelse($courses as $course)

                <article class="flex flex-col bg-white rounded-3xl overflow-hidden border border-black/5 shadow-[0_10px_30px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.08)] transition-all duration-300 group hover:-translate-y-1">

                    {{-- Image de la formation --}}
                    <div class="relative aspect-[16/10] overflow-hidden bg-gray-900">
                        @if($course->image)
                            <img
                                src="{{ asset('storage/' . $course->image) }}"
                                alt="{{ $course->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            >
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-[#1c0048] to-[#320080] flex items-center justify-center text-white/20">
                                <x-lucide-video class="w-16 h-16 text-[#F5B800]/40" />
                            </div>
                        @endif

                        {{-- Badges flottants --}}
                        <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                            @if($course->category)
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black/60 backdrop-blur-md text-[#F5B800] border border-[#F5B800]/30">
                                    {{ $course->category }}
                                </span>
                            @endif
                            @if($course->level)
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black/60 backdrop-blur-md text-white/90 border border-white/20">
                                    {{ $course->level }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Contenu de la carte --}}
                    <div class="p-7 flex-1 flex flex-col justify-between">

                        <div>
                            {{-- Durée & Infos --}}
                            <div class="flex items-center gap-4 text-xs text-black/50 font-medium mb-3">
                                @if($course->duration)
                                    <span class="flex items-center gap-1.5">
                                        <x-lucide-clock class="w-3.5 h-3.5 text-[#C15C03]" />
                                        {{ $course->duration }}
                                    </span>
                                @endif
                                @if($course->price > 0)
                                    <span class="flex items-center gap-1.5 font-bold text-black">
                                        <x-lucide-banknote class="w-3.5 h-3.5 text-[#C15C03]" />
                                        {{ number_format($course->price, 0, ',', ' ') }} FCFA
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-xl font-bold text-[#111111] group-hover:text-[#C15C03] transition-colors duration-200">
                                <a href="{{ route('public.courses.show', $course) }}">
                                    {{ $course->title }}
                                </a>
                            </h3>

                            @if($course->description)
                                <p class="mt-3 text-xs sm:text-sm text-black/60 leading-relaxed line-clamp-3">
                                    {{ $course->description }}
                                </p>
                            @endif
                        </div>

                        {{-- Actions en bas de carte --}}
                        <div class="mt-8 pt-6 border-t border-black/5 flex items-center justify-between gap-3">
                            <a
                                href="{{ route('public.courses.show', $course) }}"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-black hover:text-[#C15C03] transition"
                            >
                                <span>Voir le programme</span>
                                <x-lucide-arrow-up-right class="w-4 h-4" />
                            </a>

                            <a
                                href="{{ route('public.admissions.create') }}"
                                class="px-4 py-2 rounded-full bg-[#111111] text-white hover:bg-[#C15C03] text-xs font-semibold transition shadow-sm"
                            >
                                Admission
                            </a>
                        </div>

                    </div>

                </article>

            @empty

                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-black/5 p-8">
                    <x-lucide-book-open class="w-12 h-12 text-black/20 mx-auto mb-4" />
                    <h3 class="text-lg font-bold">Aucune formation trouvée dans cette catégorie</h3>
                    <p class="text-xs text-black/50 mt-1">Revenez à la liste complète pour voir toutes nos formations.</p>
                    <a href="{{ route('public.courses.index') }}" class="inline-block mt-4 text-xs font-bold text-[#C15C03] hover:underline">
                        Réinitialiser les filtres
                    </a>
                </div>

            @endforelse

        </div>

    </div>

</section>


{{-- =========================================================
     POURQUOI CHOISIR L'EMSI ?
========================================================= --}}
<section class="py-24 bg-white text-[#111111] border-t border-black/5">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="max-w-xl mx-auto text-center mb-16">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#C15C03] mb-2">
                Les Atouts EMSI
            </p>
            <h2 class="text-3xl font-serif text-[#111111]">
                Un apprentissage orienté métier.
            </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">

            <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100">
                <div class="w-10 h-10 rounded-xl bg-[#111] text-[#F5B800] flex items-center justify-center mb-5">
                    <x-lucide-camera class="w-5 h-5" />
                </div>
                <h3 class="font-bold text-lg mb-2">Matériel Professionnel Inclus</h3>
                <p class="text-xs sm:text-sm text-black/60 leading-relaxed">
                    Chaque étudiant a accès aux caméras de cinéma, kits d'éclairage LED, micros perche et logiciels de post-production pendant toute sa formation.
                </p>
            </div>

            <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100">
                <div class="w-10 h-10 rounded-xl bg-[#111] text-[#F5B800] flex items-center justify-center mb-5">
                    <x-lucide-users class="w-5 h-5" />
                </div>
                <h3 class="font-bold text-lg mb-2">Effectifs Réduits</h3>
                <p class="text-xs sm:text-sm text-black/60 leading-relaxed">
                    Petits groupes d'étudiants par promotion pour assurer un encadrement personnalisé et du temps de manipulation maximal sur le matériel.
                </p>
            </div>

            <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100">
                <div class="w-10 h-10 rounded-xl bg-[#111] text-[#F5B800] flex items-center justify-center mb-5">
                    <x-lucide-film class="w-5 h-5" />
                </div>
                <h3 class="font-bold text-lg mb-2">Projets de Fin d'Études</h3>
                <p class="text-xs sm:text-sm text-black/60 leading-relaxed">
                    Réalisation d'un court-métrage, d'un clip ou d'un documentaire projeté en salle devant des professionnels de l'industrie.
                </p>
            </div>

        </div>

    </div>

</section>

@endsection
