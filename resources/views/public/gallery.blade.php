@extends('layouts.public')

@section('title', 'Galerie & Réalisations — EMSI Dakar')
@section('description', 'Explorez les réalisations audiovisuelles, reportages photographiques, tournages de films et coulisses des étudiants de l’EMSI au Grand Théâtre National de Dakar.')

@section('content')

@php
    $galleryItemsJson = $galleries->map(function ($item) {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'description' => $item->description,
            'type' => $item->type,
            'file_path' => $item->file_path ? asset('storage/' . $item->file_path) : null,
            'youtube_url' => $item->youtube_url,
            'youtube_embed' => $item->youtube_embed_url,
            'image_url' => $item->image_url,
        ];
    })->values();
@endphp

<div
    class="bg-[#0a0a0a] text-white min-h-screen"
    x-data="{
        activeFilter: 'all',
        activeModal: null,
        items: {{ Js::from($galleryItemsJson) }},
        openModal(index) {
            this.activeModal = index;
            document.body.style.overflow = 'hidden';
        },
        closeModal() {
            this.activeModal = null;
            document.body.style.overflow = '';
        },
        nextModal() {
            if (this.activeModal !== null && this.items.length > 0) {
                this.activeModal = (this.activeModal + 1) % this.items.length;
            }
        },
        prevModal() {
            if (this.activeModal !== null && this.items.length > 0) {
                this.activeModal = (this.activeModal - 1 + this.items.length) % this.items.length;
            }
        }
    }"
    @keydown.escape.window="closeModal()"
    @keydown.arrow-right.window="if(activeModal !== null) nextModal()"
    @keydown.arrow-left.window="if(activeModal !== null) prevModal()"
>

    {{-- =========================================================
         HERO SECTION — GALERIE
    ========================================================== --}}
    <section class="relative pt-16 pb-20 lg:pt-24 lg:pb-28 border-b border-white/10 overflow-hidden">

        {{-- Lueur d'ambiance --}}
        <div class="absolute top-0 right-1/4 w-[500px] h-[500px] bg-[#320080]/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-[#F5B800]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-3 text-xs uppercase tracking-widest text-[#F5B800] mb-6 font-bold">
                <a href="{{ route('public.home') }}" class="text-white/40 hover:text-white transition">Accueil</a>
                <span class="text-white/20">/</span>
                <span>Galerie</span>
            </div>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                <div class="max-w-2xl">
                    <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-[#F5B800] mb-3">
                        <span class="w-2 h-2 rounded-full bg-[#F5B800] animate-pulse"></span>
                        Regards, Tournages & Coulisses
                    </p>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-normal tracking-tight text-white leading-tight">
                        L'art de l'image et du son en action.
                    </h1>

                    <p class="mt-4 text-base text-white/60 leading-relaxed font-light">
                        Découvrez les projets vidéo, créations étudiantes, séances de cadrage et moments forts capturés au Grand Théâtre National de Dakar.
                    </p>
                </div>

                {{-- Stats rapides --}}
                <div class="flex items-center gap-4">
                    <div class="px-5 py-3 rounded-2xl bg-white/[0.04] border border-white/10 text-center">
                        <p class="text-2xl font-serif font-bold text-[#F5B800]">{{ $photoCount }}</p>
                        <p class="text-[10px] uppercase tracking-wider text-white/40 font-semibold">Photos</p>
                    </div>
                    <div class="px-5 py-3 rounded-2xl bg-white/[0.04] border border-white/10 text-center">
                        <p class="text-2xl font-serif font-bold text-white">{{ $videoCount }}</p>
                        <p class="text-[10px] uppercase tracking-wider text-white/40 font-semibold">Vidéos</p>
                    </div>
                </div>
            </div>

        </div>

    </section>


    {{-- =========================================================
         FILTRES & GRILLE DE MÉDIAS
    ========================================================== --}}
    <section class="py-16 lg:py-24">

        <div class="max-w-7xl mx-auto px-6 lg:px-10">

            {{-- Barre de Filtres --}}
            @if($galleries->count() > 0)
            <div class="flex flex-wrap items-center justify-between gap-4 mb-12">

                <div class="inline-flex p-1.5 rounded-2xl bg-[#141414] border border-white/10">

                    {{-- Tous --}}
                    <button
                        type="button"
                        @click="activeFilter = 'all'"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold tracking-wider transition-all duration-300"
                        :class="activeFilter === 'all'
                            ? 'bg-[#F5B800] text-black shadow-[0_2px_12px_rgba(245,184,0,0.3)] font-bold'
                            : 'text-white/60 hover:text-white hover:bg-white/5'"
                    >
                        <x-lucide-layout-grid class="w-3.5 h-3.5" />
                        <span>Tous les médias</span>
                        <span
                            class="px-2 py-0.5 rounded-full text-[10px]"
                            :class="activeFilter === 'all' ? 'bg-black/20 text-black font-bold' : 'bg-white/10 text-white/60'"
                        >
                            {{ $galleries->count() }}
                        </span>
                    </button>

                    {{-- Photos --}}
                    @if($photoCount > 0)
                    <button
                        type="button"
                        @click="activeFilter = 'image'"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold tracking-wider transition-all duration-300"
                        :class="activeFilter === 'image'
                            ? 'bg-[#F5B800] text-black shadow-[0_2px_12px_rgba(245,184,0,0.3)] font-bold'
                            : 'text-white/60 hover:text-white hover:bg-white/5'"
                    >
                        <x-lucide-camera class="w-3.5 h-3.5" />
                        <span>Photos ({{ $photoCount }})</span>
                    </button>
                    @endif

                    {{-- Vidéos --}}
                    @if($videoCount > 0)
                    <button
                        type="button"
                        @click="activeFilter = 'video'"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold tracking-wider transition-all duration-300"
                        :class="activeFilter === 'video'
                            ? 'bg-[#F5B800] text-black shadow-[0_2px_12px_rgba(245,184,0,0.3)] font-bold'
                            : 'text-white/60 hover:text-white hover:bg-white/5'"
                    >
                        <x-lucide-video class="w-3.5 h-3.5" />
                        <span>Vidéos ({{ $videoCount }})</span>
                    </button>
                    @endif

                </div>

                <p class="text-xs text-white/40 hidden sm:flex items-center gap-2">
                    <x-lucide-sparkles class="w-4 h-4 text-[#F5B800]" />
                    <span>Cliquez sur un média pour lancer la lecture grand écran</span>
                </p>

            </div>
            @endif


            {{-- Grille des éléments --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

                @forelse($galleries as $index => $gallery)

                    <x-public.gallery-item
                        :gallery="$gallery"
                        :index="$index"
                    />

                @empty

                    <div class="col-span-full py-20 text-center rounded-3xl border border-white/10 bg-[#121212] p-8">
                        <x-lucide-image class="w-12 h-12 text-white/20 mx-auto mb-4" />
                        <h3 class="text-lg font-bold text-white">Galerie en cours d'alimentation</h3>
                        <p class="text-xs text-white/40 mt-1 max-w-sm mx-auto">
                            Revenez très bientôt pour admirer les premières créations de nos étudiants.
                        </p>
                    </div>

                @endforelse

            </div>

        </div>

    </section>


    {{-- =========================================================
         MODAL LIGHTBOX FULLSCREEN (Alpine.js)
    ========================================================== --}}
    <div
        x-show="activeModal !== null"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 md:p-8 bg-black/95 backdrop-blur-2xl"
        @click.self="closeModal()"
    >

        {{-- Header du modal --}}
        <div class="absolute top-6 left-6 right-6 flex items-center justify-between z-30 pointer-events-none">

            <div class="pointer-events-auto inline-flex items-center gap-3 px-4 py-2 rounded-full bg-black/60 backdrop-blur-md border border-white/15 text-xs text-white/80">
                <span class="w-2 h-2 rounded-full bg-[#F5B800]"></span>
                <span x-text="items[activeModal]?.type === 'video' ? 'Vidéo' : 'Photographie'" class="font-semibold uppercase tracking-wider text-[10px]"></span>
                <span class="text-white/30">|</span>
                <span x-text="(activeModal !== null ? activeModal + 1 : 0) + ' / ' + items.length" class="font-mono text-white/60"></span>
            </div>

            <button
                type="button"
                @click="closeModal()"
                class="pointer-events-auto p-3 rounded-full bg-white/10 hover:bg-[#F5B800] text-white hover:text-black border border-white/20 hover:border-[#F5B800] transition-all duration-200 shadow-2xl focus:outline-none"
                aria-label="Fermer"
            >
                <x-lucide-x class="w-5 h-5" />
            </button>

        </div>


        {{-- Bouton Précédent --}}
        <button
            type="button"
            x-show="items.length > 1"
            @click.stop="prevModal()"
            class="absolute left-4 md:left-8 z-20 p-3.5 rounded-full bg-black/50 hover:bg-[#F5B800] text-white hover:text-black border border-white/20 hover:border-[#F5B800] backdrop-blur-md transition-all duration-200 shadow-2xl focus:outline-none"
            aria-label="Média précédent"
        >
            <x-lucide-chevron-left class="w-6 h-6" />
        </button>


        {{-- Contenu Actif --}}
        <div
            x-show="activeModal !== null"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="relative max-w-5xl w-full max-h-[85vh] flex flex-col items-center justify-center z-10"
        >

            {{-- Si Vidéo YouTube --}}
            <template x-if="items[activeModal]?.type === 'video' && items[activeModal]?.youtube_embed">
                <div class="w-full aspect-video rounded-2xl overflow-hidden shadow-2xl bg-black border border-white/10">
                    <iframe
                        :src="items[activeModal]?.youtube_embed"
                        class="w-full h-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                    ></iframe>
                </div>
            </template>

            {{-- Si Image --}}
            <template x-if="items[activeModal]?.type === 'image' && items[activeModal]?.file_path">
                <div class="relative max-h-[70vh] rounded-2xl overflow-hidden shadow-2xl border border-white/10 bg-black flex items-center justify-center">
                    <img
                        :src="items[activeModal]?.file_path"
                        :alt="items[activeModal]?.title"
                        class="max-h-[70vh] w-auto object-contain rounded-2xl"
                    >
                </div>
            </template>

            {{-- Titre & Description --}}
            <div class="mt-4 w-full text-center px-4">
                <h3
                    x-text="items[activeModal]?.title"
                    class="text-lg md:text-xl font-bold text-white text-shadow"
                ></h3>
                <p
                    x-show="items[activeModal]?.description"
                    x-text="items[activeModal]?.description"
                    class="mt-1 text-xs md:text-sm text-white/60 max-w-2xl mx-auto leading-relaxed"
                ></p>
            </div>

        </div>


        {{-- Bouton Suivant --}}
        <button
            type="button"
            x-show="items.length > 1"
            @click.stop="nextModal()"
            class="absolute right-4 md:right-8 z-20 p-3.5 rounded-full bg-black/50 hover:bg-[#F5B800] text-white hover:text-black border border-white/20 hover:border-[#F5B800] backdrop-blur-md transition-all duration-200 shadow-2xl focus:outline-none"
            aria-label="Média suivant"
        >
            <x-lucide-chevron-right class="w-6 h-6" />
        </button>

    </div>

</div>

@endsection
