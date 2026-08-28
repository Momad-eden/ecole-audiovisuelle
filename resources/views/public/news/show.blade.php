@extends('layouts.public')

@section('title', $news->title . ' — Journal EMSI')
@section('description', \Illuminate\Support\Str::limit($news->excerpt ?? strip_tags($news->content), 150))

@section('content')

{{-- =========================================================
     EN-TÊTE DE L'ARTICLE
========================================================= --}}
<article class="bg-white text-[#111111] pt-12 pb-24">

    <div class="max-w-4xl mx-auto px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-xs uppercase tracking-wider text-black/40 mb-8 font-medium">
            <a href="{{ route('public.home') }}" class="hover:text-black transition">Accueil</a>
            <span>/</span>
            <a href="{{ route('public.news.index') }}" class="hover:text-black transition">Actualités</a>
            <span>/</span>
            <span class="text-black line-clamp-1 max-w-xs">{{ $news->title }}</span>
        </div>

        {{-- Date et tag --}}
        <div class="flex items-center gap-3 mb-6">
            <span class="px-3.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-[#F5B800]/20 text-[#C15C03]">
                Actualité
            </span>
            @if($news->published_at)
                <span class="text-xs text-black/50 font-medium">
                    Publié le {{ $news->published_at->translatedFormat('d F Y') }}
                </span>
            @endif
        </div>

        {{-- Titre principal --}}
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal tracking-tight text-[#111111] leading-tight mb-8">
            {{ $news->title }}
        </h1>

        {{-- Chapeau / Excerpt --}}
        @if($news->excerpt)
            <div class="text-lg sm:text-xl text-black/70 leading-relaxed font-light mb-10 pl-4 border-l-2 border-[#C15C03]">
                {{ $news->excerpt }}
            </div>
        @endif

        {{-- Image à la une --}}
        @if($news->image)
            <div class="my-10 rounded-3xl overflow-hidden shadow-2xl bg-black">
                <img
                    src="{{ asset('storage/' . $news->image) }}"
                    alt="{{ $news->title }}"
                    class="w-full h-auto max-h-[550px] object-cover"
                >
            </div>
        @endif

        {{-- Corps de l'article --}}
        <div class="prose prose-lg max-w-none text-black/80 font-light leading-relaxed space-y-6 pt-4 text-base sm:text-lg">
            {!! nl2br(e($news->content)) !!}
        </div>

        {{-- Pied d'article & Partage --}}
        <div class="mt-16 pt-8 border-t border-black/10 flex flex-wrap items-center justify-between gap-4">
            <a
                href="{{ route('public.news.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-gray-100 hover:bg-[#111] hover:text-white text-xs font-bold transition-all duration-200"
            >
                <x-lucide-arrow-left class="w-4 h-4" />
                <span>Retour au journal</span>
            </a>

            <div class="flex items-center gap-3 text-xs text-black/50 font-medium">
                <span>Partager :</span>
                <a
                    href="https://wa.me/?text={{ urlencode($news->title . ' ' . url()->current()) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="p-2 rounded-full bg-gray-100 hover:bg-[#25D366] hover:text-white transition"
                    aria-label="Partager sur WhatsApp"
                >
                    <x-lucide-message-circle class="w-4 h-4" />
                </a>
            </div>
        </div>

    </div>

</article>


{{-- =========================================================
     AUTRES ACTUALITÉS RÉCENTES
========================================================= --}}
@if($recentNews->count())
<section class="py-20 bg-[#f8f7f4] border-t border-black/5">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#C15C03] mb-2">
                    À lire aussi
                </p>
                <h2 class="text-2xl sm:text-3xl font-serif text-[#111111]">
                    Dernières actualités
                </h2>
            </div>
            <a href="{{ route('public.news.index') }}" class="text-xs font-bold text-black hover:text-[#C15C03] transition hidden sm:inline-flex items-center gap-1">
                <span>Tout voir</span>
                <x-lucide-arrow-right class="w-4 h-4" />
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($recentNews as $item)
                <article class="bg-white rounded-3xl overflow-hidden border border-black/5 shadow-sm hover:shadow-md transition group">
                    <div class="aspect-[16/10] bg-gray-900 overflow-hidden">
                        @if($item->image)
                            <img
                                src="{{ asset('storage/' . $item->image) }}"
                                alt="{{ $item->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                            >
                        @else
                            <div class="w-full h-full bg-[#1b0045] flex items-center justify-center text-white/20">
                                <x-lucide-newspaper class="w-10 h-10 text-[#F5B800]" />
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <p class="text-[11px] text-black/40 mb-2">
                            {{ $item->published_at ? $item->published_at->translatedFormat('d M Y') : 'Actualité' }}
                        </p>
                        <h3 class="font-bold text-base text-[#111111] group-hover:text-[#C15C03] transition line-clamp-2">
                            <a href="{{ route('public.news.show', $item) }}">
                                {{ $item->title }}
                            </a>
                        </h3>
                    </div>
                </article>
            @endforeach
        </div>

    </div>

</section>
@endif

@endsection
