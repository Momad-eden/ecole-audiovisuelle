@extends('layouts.public')

@section('title', 'Candidature & Admission — EMSI Dakar')

@section('description', 'Déposez votre candidature en ligne pour rejoindre les formations audiovisuelles de l’EMSI au Grand Théâtre National Doudou Ndiaye Rose à Dakar.')

@section('content')

{{-- =========================================================
     HERO SECTION — EN-TÊTE CANDIDATURE
========================================================= --}}
<section class="relative bg-[#080808] text-white pt-12 pb-16 lg:pt-16 lg:pb-20 overflow-hidden border-b border-white/10">

    {{-- Lueur d'ambiance --}}
    <div class="absolute -top-24 right-1/4 w-96 h-96 bg-[#320080]/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-10 w-80 h-80 bg-[#F5B800]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-10 relative z-10">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-white/50 mb-6">
            <a href="{{ route('public.home') }}" class="hover:text-white transition-colors">Accueil</a>
            <span class="text-white/20">/</span>
            <span class="text-[#F5B800]">Candidature & Admission</span>
        </div>

        <div class="max-w-3xl">

            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#F5B800]/10 text-[#F5B800] border border-[#F5B800]/30 mb-4">
                <span class="w-2 h-2 rounded-full bg-[#F5B800] animate-pulse"></span>
                Session 2026 · Inscriptions Ouvertes
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-normal tracking-tight text-white leading-tight mb-4">
                Déposez votre candidature à l'EMSI
            </h1>

            <p class="text-base sm:text-lg text-white/70 leading-relaxed font-light">
                Rejoignez l'École des Métiers du Son et de l'Image au cœur du Grand Théâtre National de Dakar. Remplissez le formulaire ci-dessous en quelques minutes pour initier votre parcours.
            </p>

            {{-- Avantages rapides --}}
            <div class="mt-8 flex flex-wrap items-center gap-4 text-xs font-medium text-white/80">
                <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/[0.06] border border-white/10">
                    <x-lucide-clock class="w-3.5 h-3.5 text-[#F5B800]" />
                    Réponse sous 48h
                </span>
                <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/[0.06] border border-white/10">
                    <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-[#F5B800]" />
                    Sans frais de dossier préalables
                </span>
                <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/[0.06] border border-white/10">
                    <x-lucide-award class="w-3.5 h-3.5 text-[#F5B800]" />
                    Entretien d'orientation individuel
                </span>
            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     CORPS DU FORMULAIRE D'ADMISSION
========================================================= --}}
<section class="bg-[#F8F7F4] text-[#111111] py-12 lg:py-20" x-data="{
    selectedCourseId: '{{ old('course_id', request('course', '')) }}',
    selectedCourseTitle: ''
}">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        {{-- Alerte d'erreurs éventuelles --}}
        @if($errors->any())
            <div class="mb-10 p-6 rounded-2xl bg-red-50 border border-red-200 text-red-900 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-9 h-9 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                        <x-lucide-alert-circle class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="font-bold text-sm sm:text-base text-red-800">
                            Veuillez corriger les informations suivantes :
                        </h3>
                        <ul class="mt-2 space-y-1 text-xs sm:text-sm text-red-700 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-12 gap-10 lg:gap-14 items-start">

            {{-- Colonne Principale : Formulaire (8 colonnes) --}}
            <div class="lg:col-span-8">

                <form
                    method="POST"
                    action="{{ route('public.admissions.store') }}"
                    class="space-y-10"
                    id="admission-form"
                >
                    @csrf

                    {{-- =====================================================
                         ÉTAPE 1 : IDENTITÉ DU CANDIDAT
                    ====================================================== --}}
                    <div class="bg-white rounded-3xl p-7 sm:p-9 border border-black/5 shadow-[0_10px_30px_rgba(0,0,0,0.03)]">

                        <div class="flex items-center gap-3 pb-6 border-b border-black/10 mb-6">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-[#111111] text-[#F5B800] font-bold text-xs">
                                01
                            </span>
                            <div>
                                <h2 class="text-lg sm:text-xl font-bold text-[#111111]">
                                    Identité personnelle
                                </h2>
                                <p class="text-xs text-black/50">
                                    Vos informations pour constituer votre fiche candidat
                                </p>
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-5">

                            {{-- Prénom --}}
                            <div>
                                <label for="first_name" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                    Prénom <span class="text-[#C15C03]">*</span>
                                </label>
                                <input
                                    id="first_name"
                                    name="first_name"
                                    type="text"
                                    value="{{ old('first_name') }}"
                                    required
                                    autocomplete="given-name"
                                    placeholder="Ex. Moussa, Aïssatou"
                                    class="w-full rounded-xl border border-black/15 bg-white px-4 py-3.5 text-sm text-black placeholder:text-black/30 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none"
                                >
                            </div>

                            {{-- Nom --}}
                            <div>
                                <label for="last_name" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                    Nom de famille <span class="text-[#C15C03]">*</span>
                                </label>
                                <input
                                    id="last_name"
                                    name="last_name"
                                    type="text"
                                    value="{{ old('last_name') }}"
                                    required
                                    autocomplete="family-name"
                                    placeholder="Ex. Diallo, Diop"
                                    class="w-full rounded-xl border border-black/15 bg-white px-4 py-3.5 text-sm text-black placeholder:text-black/30 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none"
                                >
                            </div>

                            {{-- Date de naissance --}}
                            <div>
                                <label for="birth_date" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                    Date de naissance
                                </label>
                                <input
                                    id="birth_date"
                                    name="birth_date"
                                    type="date"
                                    value="{{ old('birth_date') }}"
                                    autocomplete="bday"
                                    class="w-full rounded-xl border border-black/15 bg-white px-4 py-3.5 text-sm text-black placeholder:text-black/30 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none"
                                >
                            </div>

                            {{-- Lieu de naissance --}}
                            <div>
                                <label for="birth_place" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                    Lieu de naissance
                                </label>
                                <input
                                    id="birth_place"
                                    name="birth_place"
                                    type="text"
                                    value="{{ old('birth_place') }}"
                                    placeholder="Ex. Dakar, Thiès, Saint-Louis"
                                    class="w-full rounded-xl border border-black/15 bg-white px-4 py-3.5 text-sm text-black placeholder:text-black/30 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none"
                                >
                            </div>

                            {{-- Genre --}}
                            <div>
                                <label for="gender" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                    Genre
                                </label>
                                <select
                                    id="gender"
                                    name="gender"
                                    class="w-full rounded-xl border border-black/15 bg-white px-4 py-3.5 text-sm text-black focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none"
                                >
                                    <option value="">Sélectionner</option>
                                    <option value="M" @selected(old('gender') === 'M')>Homme</option>
                                    <option value="F" @selected(old('gender') === 'F')>Femme</option>
                                </select>
                            </div>

                            {{-- Nationalité --}}
                            <div>
                                <label for="nationality" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                    Nationalité
                                </label>
                                <select
                                    id="nationality"
                                    name="nationality"
                                    class="w-full rounded-xl border border-black/15 bg-white px-4 py-3.5 text-sm text-black focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none"
                                >
                                    <option value="">Sélectionner votre nationalité</option>
                                    <option value="Sénégalaise" @selected(old('nationality', 'Sénégalaise') === 'Sénégalaise')>Sénégalaise</option>
                                    <option value="Malienne" @selected(old('nationality') === 'Malienne')>Malienne</option>
                                    <option value="Mauritanienne" @selected(old('nationality') === 'Mauritanienne')>Mauritanienne</option>
                                    <option value="Guinéenne" @selected(old('nationality') === 'Guinéenne')>Guinéenne</option>
                                    <option value="Ivoirienne" @selected(old('nationality') === 'Ivoirienne')>Ivoirienne</option>
                                    <option value="Burkinabè" @selected(old('nationality') === 'Burkinabè')>Burkinabè</option>
                                    <option value="Nigérienne" @selected(old('nationality') === 'Nigérienne')>Nigérienne</option>
                                    <option value="Togolaise" @selected(old('nationality') === 'Togolaise')>Togolaise</option>
                                    <option value="Béninoise" @selected(old('nationality') === 'Béninoise')>Béninoise</option>
                                    <option value="Camerounaise" @selected(old('nationality') === 'Camerounaise')>Camerounaise</option>
                                    <option value="Gabonaise" @selected(old('nationality') === 'Gabonaise')>Gabonaise</option>
                                    <option value="Congolaise" @selected(old('nationality') === 'Congolaise')>Congolaise</option>
                                    <option value="Autre" @selected(old('nationality') === 'Autre')>Autre nationalité</option>
                                </select>
                            </div>

                        </div>

                    </div>


                    {{-- =====================================================
                         ÉTAPE 2 : COORDONNÉES DE CONTACT
                    ====================================================== --}}
                    <div class="bg-white rounded-3xl p-7 sm:p-9 border border-black/5 shadow-[0_10px_30px_rgba(0,0,0,0.03)]">

                        <div class="flex items-center gap-3 pb-6 border-b border-black/10 mb-6">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-[#111111] text-[#F5B800] font-bold text-xs">
                                02
                            </span>
                            <div>
                                <h2 class="text-lg sm:text-xl font-bold text-[#111111]">
                                    Coordonnées de contact
                                </h2>
                                <p class="text-xs text-black/50">
                                    Pour vous transmettre les convocations et informations pratiques
                                </p>
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-5">

                            {{-- Téléphone --}}
                            <div>
                                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                    Téléphone (WhatsApp) <span class="text-[#C15C03]">*</span>
                                </label>
                                <input
                                    id="phone"
                                    name="phone"
                                    type="tel"
                                    value="{{ old('phone') }}"
                                    required
                                    autocomplete="tel"
                                    placeholder="+221 77 000 00 00"
                                    class="w-full rounded-xl border border-black/15 bg-white px-4 py-3.5 text-sm text-black placeholder:text-black/30 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none"
                                >
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                    Adresse e-mail
                                </label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    autocomplete="email"
                                    placeholder="vous@exemple.com"
                                    class="w-full rounded-xl border border-black/15 bg-white px-4 py-3.5 text-sm text-black placeholder:text-black/30 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none"
                                >
                            </div>

                            {{-- Adresse --}}
                            <div class="sm:col-span-2">
                                <label for="address" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                    Adresse de résidence
                                </label>
                                <input
                                    id="address"
                                    name="address"
                                    type="text"
                                    value="{{ old('address') }}"
                                    autocomplete="street-address"
                                    placeholder="Quartier, Ville (ex. Médina, Dakar)"
                                    class="w-full rounded-xl border border-black/15 bg-white px-4 py-3.5 text-sm text-black placeholder:text-black/30 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none"
                                >
                            </div>

                        </div>

                    </div>


                    {{-- =====================================================
                         ÉTAPE 3 : PARCOURS & ÉTUDES
                    ====================================================== --}}
                    <div class="bg-white rounded-3xl p-7 sm:p-9 border border-black/5 shadow-[0_10px_30px_rgba(0,0,0,0.03)]">

                        <div class="flex items-center gap-3 pb-6 border-b border-black/10 mb-6">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-[#111111] text-[#F5B800] font-bold text-xs">
                                03
                            </span>
                            <div>
                                <h2 class="text-lg sm:text-xl font-bold text-[#111111]">
                                    Parcours & Études antérieures
                                </h2>
                                <p class="text-xs text-black/50">
                                    Afin de mieux situer votre niveau d'études actuel
                                </p>
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-5">

                            {{-- Dernier diplôme --}}
                            <div>
                                <label for="last_diploma" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                    Dernier diplôme obtenu
                                </label>
                                <select
                                    id="last_diploma"
                                    name="last_diploma"
                                    class="w-full rounded-xl border border-black/15 bg-white px-4 py-3.5 text-sm text-black focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none"
                                >
                                    <option value="">Sélectionner</option>
                                    <option value="BFEM" @selected(old('last_diploma') === 'BFEM')>BFEM / Collège</option>
                                    <option value="Baccalauréat" @selected(old('last_diploma') === 'Baccalauréat')>Baccalauréat</option>
                                    <option value="BTS" @selected(old('last_diploma') === 'BTS')>BTS / DUT (Bac+2)</option>
                                    <option value="Licence" @selected(old('last_diploma') === 'Licence')>Licence (Bac+3)</option>
                                    <option value="Master" @selected(old('last_diploma') === 'Master')>Master (Bac+5)</option>
                                    <option value="Autre" @selected(old('last_diploma') === 'Autre')>Autre parcours / autodidacte</option>
                                </select>
                            </div>

                            {{-- Année d'obtention --}}
                            <div>
                                <label for="graduation_year" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                    Année d'obtention
                                </label>
                                <input
                                    id="graduation_year"
                                    name="graduation_year"
                                    type="number"
                                    value="{{ old('graduation_year') }}"
                                    min="1960"
                                    max="{{ now()->year }}"
                                    placeholder="Ex. {{ now()->year - 1 }}"
                                    class="w-full rounded-xl border border-black/15 bg-white px-4 py-3.5 text-sm text-black placeholder:text-black/30 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none"
                                >
                            </div>

                            {{-- Établissement précédent --}}
                            <div>
                                <label for="previous_school" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                    Établissement d'origine
                                </label>
                                <input
                                    id="previous_school"
                                    name="previous_school"
                                    type="text"
                                    value="{{ old('previous_school') }}"
                                    placeholder="Lycée ou université"
                                    class="w-full rounded-xl border border-black/15 bg-white px-4 py-3.5 text-sm text-black placeholder:text-black/30 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none"
                                >
                            </div>

                            {{-- Domaine / Série --}}
                            <div>
                                <label for="academic_field" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                    Filière ou Série
                                </label>
                                <input
                                    id="academic_field"
                                    name="academic_field"
                                    type="text"
                                    value="{{ old('academic_field') }}"
                                    placeholder="Ex. L2, S2, Arts, Com..."
                                    class="w-full rounded-xl border border-black/15 bg-white px-4 py-3.5 text-sm text-black placeholder:text-black/30 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none"
                                >
                            </div>

                        </div>

                    </div>


                    {{-- =====================================================
                         ÉTAPE 4 : CHOIX DE LA FORMATION & MOTIVATION
                    ====================================================== --}}
                    <div class="bg-white rounded-3xl p-7 sm:p-9 border border-black/5 shadow-[0_10px_30px_rgba(0,0,0,0.03)]">

                        <div class="flex items-center gap-3 pb-6 border-b border-black/10 mb-6">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-[#111111] text-[#F5B800] font-bold text-xs">
                                04
                            </span>
                            <div>
                                <h2 class="text-lg sm:text-xl font-bold text-[#111111]">
                                    Formation souhaitée & Motivation
                                </h2>
                                <p class="text-xs text-black/50">
                                    Sélectionnez le cursus que vous visez pour votre admission
                                </p>
                            </div>
                        </div>

                        {{-- Grille des Formations --}}
                        <div class="mb-8">

                            <label class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-3">
                                Sélectionnez votre filière <span class="text-[#C15C03]">*</span>
                            </label>

                            @if($courses->count())
                                <div class="grid sm:grid-cols-2 gap-4">
                                    @foreach($courses as $c)
                                        @php
                                            $isChecked = (string) old('course_id', request('course')) === (string) $c->id;
                                        @endphp
                                        <label
                                            class="relative flex flex-col justify-between p-5 rounded-2xl border cursor-pointer transition-all duration-200"
                                            :class="selectedCourseId == '{{ $c->id }}' ? 'border-[#F5B800] bg-[#FFFDF5] ring-2 ring-[#F5B800]/30 shadow-md' : 'border-black/10 bg-white hover:border-black/25 hover:bg-gray-50/60'"
                                            @click="selectedCourseId = '{{ $c->id }}'; selectedCourseTitle = '{{ addslashes($c->title) }}'"
                                        >
                                            <input
                                                type="radio"
                                                name="course_id"
                                                value="{{ $c->id }}"
                                                class="sr-only"
                                                @checked($isChecked)
                                                required
                                            >

                                            <div>
                                                <div class="flex items-center justify-between gap-2 mb-2">
                                                    @if($c->category)
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#F5B800]/10 text-[#8A6800]">
                                                            {{ $c->category }}
                                                        </span>
                                                    @endif

                                                    <div
                                                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                                        :class="selectedCourseId == '{{ $c->id }}' ? 'border-[#F5B800] bg-[#F5B800]' : 'border-black/20 bg-white'"
                                                    >
                                                        <span
                                                            class="w-2 h-2 rounded-full bg-black transition-opacity"
                                                            :class="selectedCourseId == '{{ $c->id }}' ? 'opacity-100' : 'opacity-0'"
                                                        ></span>
                                                    </div>
                                                </div>

                                                <h3 class="text-base font-bold text-[#111111] leading-snug">
                                                    {{ $c->title }}
                                                </h3>

                                                @if($c->description)
                                                    <p class="mt-2 text-xs text-black/55 line-clamp-2 leading-relaxed">
                                                        {{ $c->description }}
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="mt-4 pt-3 border-t border-black/5 flex items-center justify-between text-[11px] text-black/60 font-medium">
                                                @if($c->duration)
                                                    <span class="flex items-center gap-1">
                                                        <x-lucide-clock class="w-3 h-3 text-[#C15C03]" />
                                                        {{ $c->duration }}
                                                    </span>
                                                @endif

                                                @if(!is_null($c->price))
                                                    <span class="font-bold text-black">
                                                        {{ $c->price > 0 ? number_format($c->price, 0, ',', ' ') . ' FCFA' : 'Sur demande' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-6 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
                                    Aucune formation active n'est actuellement disponible.
                                </div>
                            @endif

                        </div>

                        {{-- Choix du Volet du Projet --}}
                        <div class="mb-8 pb-6 border-b border-black/10">
                            <label class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                Volet du Programme visé
                            </label>
                            <p class="text-xs text-black/50 mb-3">
                                Précisez si vous visez le camp intensif pré-événementiel ou le cycle diplômant BTS par la VAE
                            </p>

                            <div class="grid sm:grid-cols-2 gap-3.5">
                                <label class="relative flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition-all {{ (old('volet') === 'Volet 1 — Perfectionnement intensif (3 mois)' || (!old('volet') && !in_array(request('volet'), ['vae', '2', 'Volet 2 — Certification BTS-VAE (9 mois)']))) ? 'border-[#F5B800] bg-[#FFFDF5] ring-2 ring-[#F5B800]/30' : 'border-black/10 bg-white hover:border-black/20' }}">
                                    <input
                                        type="radio"
                                        name="volet"
                                        value="Volet 1 — Perfectionnement intensif (3 mois)"
                                        @checked((old('volet') === 'Volet 1 — Perfectionnement intensif (3 mois)' || !old('volet')) && !in_array(request('volet'), ['vae', '2', 'Volet 2 — Certification BTS-VAE (9 mois)']))
                                        class="mt-1 text-[#C15C03] focus:ring-[#F5B800]"
                                    >
                                    <div>
                                        <p class="text-xs font-bold text-[#111111]">Volet 1 — Perfectionnement intensif</p>
                                        <p class="text-[11px] text-black/60 mt-0.5">3 mois · 12 semaines (~360h) · 90% Pratique · Grand Théâtre</p>
                                    </div>
                                </label>

                                <label class="relative flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition-all {{ (old('volet') === 'Volet 2 — Certification BTS-VAE (9 mois)' || in_array(request('volet'), ['vae', '2', 'Volet 2 — Certification BTS-VAE (9 mois)'])) ? 'border-[#F5B800] bg-[#FFFDF5] ring-2 ring-[#F5B800]/30' : 'border-black/10 bg-white hover:border-black/20' }}">
                                    <input
                                        type="radio"
                                        name="volet"
                                        value="Volet 2 — Certification BTS-VAE (9 mois)"
                                        @checked(old('volet') === 'Volet 2 — Certification BTS-VAE (9 mois)' || in_array(request('volet'), ['vae', '2', 'Volet 2 — Certification BTS-VAE (9 mois)']))
                                        class="mt-1 text-[#C15C03] focus:ring-[#F5B800]"
                                    >
                                    <div>
                                        <p class="text-xs font-bold text-[#111111]">Volet 2 — Cycle BTS-VAE</p>
                                        <p class="text-[11px] text-black/60 mt-0.5">9 mois (1 080h) · Alternance · Titre BTS Bac+2 d'État</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Motivation facultative --}}
                        <div>
                            <label for="message" class="block text-xs font-bold uppercase tracking-wider text-black/70 mb-2">
                                Votre motivation ou projet (facultatif)
                            </label>
                            <textarea
                                id="message"
                                name="message"
                                rows="5"
                                maxlength="5000"
                                placeholder="Parlez-nous de vos motivations, de ce qui vous passionne dans l'audiovisuel et de vos ambitions professionnelles..."
                                class="w-full rounded-2xl border border-black/15 bg-white p-4 text-sm leading-relaxed text-black placeholder:text-black/30 focus:border-[#F5B800] focus:ring-2 focus:ring-[#F5B800]/20 transition outline-none resize-y"
                            >{{ old('message') }}</textarea>
                            <p class="mt-1 text-[11px] text-black/40">
                                Facultatif · Maximum 5000 caractères
                            </p>
                        </div>

                    </div>


                    {{-- Bouton d'envoi final --}}
                    <div class="pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">

                        <p class="text-xs text-black/50 leading-relaxed max-w-md">
                            En validant votre candidature, vous acceptez d'être contacté par la commission pédagogique de l'EMSI pour votre entretien d'admission.
                        </p>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-3 px-8 py-4 rounded-full bg-[#111111] text-[#F5B800] hover:bg-[#C15C03] hover:text-white font-bold text-sm uppercase tracking-wider transition-all duration-300 shadow-xl cursor-pointer"
                        >
                            <span>Transmettre ma candidature</span>
                            <x-lucide-arrow-up-right class="w-4 h-4" />
                        </button>

                    </div>

                </form>

            </div>


            {{-- Colonne Latérale : Synthèse & Conseils (4 colonnes) --}}
            <div class="lg:col-span-4">

                <aside class="sticky top-28 space-y-6">

                    {{-- Carte Récapitulative du Parcours d'Admission --}}
                    <div class="bg-white rounded-3xl p-7 border border-black/10 shadow-[0_15px_40px_rgba(0,0,0,0.05)]">

                        <h3 class="text-base font-bold text-[#111111] mb-4 flex items-center gap-2">
                            <x-lucide-sparkles class="w-4 h-4 text-[#C15C03]" />
                            Comment se passe la suite ?
                        </h3>

                        <div class="space-y-4 text-xs sm:text-sm">

                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-emerald-500/10 text-emerald-600 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    1
                                </span>
                                <div>
                                    <p class="font-bold text-[#111111]">Réception de votre dossier</p>
                                    <p class="text-black/60 text-xs mt-0.5">Accusé de réception immédiat par notre secrétariat.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-[#F5B800]/15 text-[#B78900] font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    2
                                </span>
                                <div>
                                    <p class="font-bold text-[#111111]">Entretien individuel</p>
                                    <p class="text-black/60 text-xs mt-0.5">Échange de 20 min avec un responsable pédagogique.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-[#111111] text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    3
                                </span>
                                <div>
                                    <p class="font-bold text-[#111111]">Inscription & Rentrée</p>
                                    <p class="text-black/60 text-xs mt-0.5">Finalisation du dossier et accueil au Grand Théâtre.</p>
                                </div>
                            </div>

                        </div>

                        <div class="mt-6 pt-5 border-t border-black/5 text-[11px] text-black/50 leading-relaxed">
                            💡 Aucun prérequis complexe n'est exigé : nous évaluons avant tout votre motivation et votre sensibilité créative.
                        </div>

                    </div>

                    {{-- Carte d'Assistance Téléphonique --}}
                    <div class="bg-white rounded-3xl p-6 border border-black/5 shadow-sm text-center">
                        <div class="w-10 h-10 rounded-full bg-[#F5B800]/10 text-[#B78900] flex items-center justify-center mx-auto mb-3">
                            <x-lucide-phone class="w-4 h-4" />
                        </div>
                        <h4 class="font-bold text-sm text-[#111111] mb-1">Une question sur votre dossier ?</h4>
                        <p class="text-xs text-black/60 leading-relaxed mb-4">
                            Notre équipe des admissions est joignable du lundi au vendredi.
                        </p>
                        <a
                            href="tel:+221338000000"
                            class="inline-flex items-center gap-2 text-xs font-bold text-[#C15C03] hover:underline"
                        >
                            <span>Appeler le service admissions</span>
                            <x-lucide-arrow-right class="w-3.5 h-3.5" />
                        </a>
                    </div>

                </aside>

            </div>

        </div>

    </div>

</section>

@endsection