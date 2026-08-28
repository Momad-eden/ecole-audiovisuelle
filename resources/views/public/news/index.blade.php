@extends('layouts.public')

@section('title', 'Journal & Actualités — EMSI Dakar')
@section('description', 'Suivez toute l’actualité de l’École de Formation Audiovisuelle : masterclasses, tournages étudiants, partenariats et vie de l’école à Dakar.')

@section('content')

{{-- =========================================================
     HERO SECTION — ACTUALITÉS
========================================================= --}}
<section class="relative pt-16 pb-20 lg:pt-24 lg:pb-28 bg-[#080808] text-white overflow-hidden border-b border-white/10">

    <div class="absolute top-0 left-1/3 w-96 h-96 bg-[#320080]/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-3 text-xs uppercase tracking-widest text-[#F5B800] mb-6 font-bold">
            <a href="{{ route('public.home') }}" class="text-white/40 hover:text-white transition">Accueil</a>
            <span class="text-white/20">/</span>
            <span>Actualités</span>
        </div>

        <div class="max-w-3xl">
            <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-[#F5B800] mb-3">
                Le Journal de l'École
            </p>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-normal tracking-tight text-white leading-tight">
                Actualités, masterclasses & vie du campus.
            </h1>

            <p class="mt-4 text-base text-white/60 leading-relaxed font-light">
                Restez informés des événements, projections, masterclasses professionnelles et réussites des étudiants de l'EMSI.
            </p>
        </div>

        {{-- Barre de recherche --}}
        <div class="mt-10 max-w-md">
            <form method="GET" action="{{ route('public.news.index') }}" class="relative">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Rechercher un article..."
                    class="w-full pl-12 pr-4 py-3.5 rounded-full bg-white/10 border border-white/15 text-white placeholder-white/40 text-sm focus:outline-none focus:border-[#F5B800] focus:ring-1 focus:ring-[#F5B800] transition"
                >
                <x-lucide-search class="w-5 h-5 text-white/40 absolute left-4 top-1/2 -translate-y-1/2" />
                @if(request('search'))
                    <a href="{{ route('public.news.index') }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-white/40 hover:text-white">
                        Effacer
                    </a>
                @endif
            </form>
        </div>

    </div>

</section>


{{-- =========================================================
     LISTE DES ARTICLES
========================================================= --}}
<section class="py-20 bg-[#f8f7f4] text-[#111111]">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        {{-- Article à la une (si premier chargement sans recherche) --}}
        @if($featuredNews && !request('search'))
        <div class="mb-16">
            <article class="grid lg:grid-cols-12 gap-8 bg-white rounded-3xl overflow-hidden border border-black/5 shadow-xl group">

                <div class="lg:col-span-7 relative aspect-[16/10] lg:aspect-auto overflow-hidden bg-black">
                    @if($featuredNews->image)
                        <img
                            src="{{ asset('storage/' . $featuredNews->image) }}"
                            alt="{{ $featuredNews->title }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                        >
                    @else
                        <div class="w-full h-full bg-[#1b0045] flex items-center justify-center text-white/30">
                            <x-lucide-newspaper class="w-16 h-16 text-[#F5B800]" />
                        </div>
                    @endif

                    <div class="absolute top-4 left-4">
                        <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-[#F5B800] text-black shadow-lg">
                            À la une
                        </span>
                    </div>
                </div>

                <div class="lg:col-span-5 p-8 lg:p-12 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-xs text-black/50 font-medium mb-3">
                            <x-lucide-calendar class="w-3.5 h-3.5 text-[#C15C03]" />
                            <time datetime="{{ $featuredNews->published_at?->toDateString() }}">
                                {{ $featuredNews->published_at ? $featuredNews->published_at->translatedFormat('d F Y') : 'Récemment' }}
                            </time>
                        </div>

                        <h2 class="text-2xl lg:text-3xl font-bold font-serif text-[#111111] group-hover:text-[#C15C03] transition-colors leading-tight">
                            <a href="{{ route('public.news.show', $featuredNews) }}">
                                {{ $featuredNews->title }}
                            </a>
                        </h2>

                        @if($featuredNews->excerpt)
                            <p class="mt-4 text-sm text-black/65 leading-relaxed font-light">
                                {{ $featuredNews->excerpt }}
                            </p>
                        @endif
                    </div>

                    <div class="mt-8 pt-6 border-t border-black/5 flex items-center justify-between">
                        <a
                            href="{{ route('public.news.show', $featuredNews) }}"
                            class="inline-flex items-center gap-2 text-xs font-bold text-black group-hover:text-[#C15C03] transition"
                        >
                            <span>Lire l'article complet</span>
                            <x-lucide-arrow-right class="w-4 h-4" />
                        </a>
                    </div>
                </div>

            </article>
        </div>
        @endif


        {{-- Grille des Actualités --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            @forelse($news as $article)

                @if($featuredNews && $article->id === $featuredNews->id && !request('search'))
                    @continue
                @endif

                <article class="flex flex-col bg-white rounded-3xl overflow-hidden border border-black/5 shadow-[0_10px_30px_rgba(0,0,0,0.04)] hover:shadow-lg transition-all duration-300 group hover:-translate-y-1">

                    {{-- Vignette --}}
                    <div class="relative aspect-[16/10] overflow-hidden bg-gray-900">
                        @if($article->image)
                            <img
                                src="{{ asset('storage/' . $article->image) }}"
                                alt="{{ $article->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            >
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-[#1b0045] to-[#320080] flex items-center justify-center text-white/20">
                                <x-lucide-newspaper class="w-12 h-12 text-[#F5B800]/40" />
                            </div>
                        @endif

                        <div class="absolute bottom-3 left-3">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-black/70 backdrop-blur-md text-white border border-white/15">
                                {{ $article->published_at ? $article->published_at->translatedFormat('d M Y') : 'Actualité' }}
                            </span>
                        </div>
                    </div>

                    {{-- Contenu --}}
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-[#111111] group-hover:text-[#C15C03] transition-colors leading-snug line-clamp-2">
                                <a href="{{ route('public.news.show', $article) }}">
                                    {{ $article->title }}
                                </a>
                            </h3>

                            @if($article->excerpt)
                                <p class="mt-2.5 text-xs sm:text-sm text-black/60 leading-relaxed line-clamp-3 font-light">
                                    {{ $article->excerpt }}
                                </p>
                            @endif
                        </div>

                        <div class="mt-6 pt-4 border-t border-black/5 flex items-center justify-between">
                            <a
                                href="{{ route('public.news.show', $article) }}"
                                class="inline-flex items-center gap-1 text-xs font-bold text-black hover:text-[#C15C03] transition"
                            >
                                <span>Lire la suite</span>
                                <x-lucide-arrow-up-right class="w-4 h-4" />
                            </a>
                        </div>
                    </div>

                </article>

            @empty

                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-black/5 p-8">
                    <x-lucide-newspaper class="w-12 h-12 text-black/20 mx-auto mb-4" />
                    <h3 class="text-lg font-bold">Aucun article trouvé</h3>
                    <p class="text-xs text-black/50 mt-1">Revenez très vite pour découvrir nos futures actualités.</p>
                </div>

            @endforelse

        </div>

        {{-- Pagination --}}
        <div class="mt-16">
            {{ $news->links() }}
        </div>

    </div>

</section>

@endsection
