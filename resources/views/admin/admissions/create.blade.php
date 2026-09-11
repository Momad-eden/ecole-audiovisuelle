@extends('layouts.admin')

@section('content')

<div class="max-w-6xl mx-auto space-y-8">

    {{-- =====================================================
         EN-TÊTE
    ====================================================== --}}

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">

        <div>

            <div class="flex items-center gap-3 mb-2">

                <a
                    href="{{ route('admissions.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-900 transition"
                >
                    Admissions
                </a>

                <span class="text-gray-300">/</span>

                <span class="text-sm text-gray-500">
                    Nouvelle candidature
                </span>

            </div>

            <h1 class="text-3xl font-bold text-gray-900">
                Nouvelle candidature
            </h1>

            <p class="text-gray-500 mt-1">
                Enregistrer un nouveau dossier de candidature.
            </p>

        </div>


        <a
            href="{{ route('admissions.index') }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-gray-200 bg-white text-gray-700 font-medium hover:bg-gray-50 transition"
        >

            <x-lucide-arrow-left class="w-4 h-4" />

            Retour aux candidatures

        </a>

    </div>


    {{-- =====================================================
         INFORMATION
    ====================================================== --}}

    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">

        <div class="flex gap-4">

            <div class="flex-shrink-0">

                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">

                    <x-lucide-info class="w-5 h-5 text-blue-600" />

                </div>

            </div>

            <div>

                <h2 class="font-semibold text-blue-900">
                    À propos du dossier
                </h2>

                <p class="text-sm text-blue-700 mt-1 leading-relaxed">
                    Seules les informations essentielles sont obligatoires.
                    Les autres informations peuvent être complétées
                    ultérieurement depuis la fiche du candidat.
                </p>

            </div>

        </div>

    </div>


    {{-- =====================================================
         ERREURS
    ====================================================== --}}

    @if($errors->any())

        <div class="bg-red-50 border border-red-200 rounded-xl p-5">

            <div class="flex gap-3">

                <x-lucide-circle-alert
                    class="w-5 h-5 text-red-600 flex-shrink-0"
                />

                <div>

                    <p class="font-semibold text-red-800">
                        Certaines informations doivent être vérifiées.
                    </p>

                    <ul class="mt-2 space-y-1 text-sm text-red-700">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- =====================================================
         FORMULAIRE
    ====================================================== --}}

    <form
        method="POST"
        action="{{ route('admissions.store') }}"
        class="space-y-8"
    >

        @csrf


        {{-- =================================================
             01 — IDENTITÉ
        ================================================== --}}

        <x-ui.card
            title="01 — Identité"
            subtitle="Informations personnelles du candidat"
        >

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                {{-- Prénom --}}

                <div>

                    <label
                        for="first_name"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Prénom

                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="first_name"
                        type="text"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        required
                        maxlength="100"
                        autocomplete="given-name"
                        autofocus
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >

                </div>


                {{-- Nom --}}

                <div>

                    <label
                        for="last_name"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Nom

                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="last_name"
                        type="text"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        required
                        maxlength="100"
                        autocomplete="family-name"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >

                </div>


                {{-- Date de naissance --}}

                <div>

                    <label
                        for="birth_date"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Date de naissance

                        <span class="text-xs text-gray-400 font-normal">
                            — facultatif
                        </span>
                    </label>

                    <input
                        id="birth_date"
                        type="date"
                        name="birth_date"
                        value="{{ old('birth_date') }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >

                </div>


                {{-- Lieu de naissance --}}

                <div>

                    <label
                        for="birth_place"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Lieu de naissance

                        <span class="text-xs text-gray-400 font-normal">
                            — facultatif
                        </span>
                    </label>

                    <input
                        id="birth_place"
                        type="text"
                        name="birth_place"
                        value="{{ old('birth_place') }}"
                        maxlength="150"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >

                </div>


                {{-- Genre --}}

                <div>

                    <label
                        for="gender"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Genre

                        <span class="text-xs text-gray-400 font-normal">
                            — facultatif
                        </span>
                    </label>

                    <select
                        id="gender"
                        name="gender"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >

                        <option value="">
                            Non renseigné
                        </option>

                        <option
                            value="M"
                            @selected(old('gender') === 'M')
                        >
                            Masculin
                        </option>

                        <option
                            value="F"
                            @selected(old('gender') === 'F')
                        >
                            Féminin
                        </option>

                    </select>

                </div>


                {{-- Nationalité --}}

                <div>

                    <label
                        for="nationality"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Nationalité

                        <span class="text-xs text-gray-400 font-normal">
                            — facultatif
                        </span>
                    </label>

                    <input
                        id="nationality"
                        type="text"
                        name="nationality"
                        value="{{ old('nationality') }}"
                        maxlength="100"
                        autocomplete="country-name"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >

                </div>

            </div>

        </x-ui.card>


        {{-- =================================================
             02 — COORDONNÉES
        ================================================== --}}

        <x-ui.card
            title="02 — Coordonnées"
            subtitle="Informations permettant de contacter le candidat"
        >

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                {{-- Téléphone --}}

                <div>

                    <label
                        for="phone"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Téléphone

                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="phone"
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                        required
                        maxlength="30"
                        autocomplete="tel"
                        placeholder="Ex. 77 000 00 00"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >

                </div>


                {{-- Email --}}

                <div>

                    <label
                        for="email"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Email

                        <span class="text-xs text-gray-400 font-normal">
                            — facultatif
                        </span>
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        maxlength="255"
                        autocomplete="email"
                        placeholder="exemple@email.com"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >

                </div>


                {{-- Adresse --}}

                <div class="md:col-span-2">

                    <label
                        for="address"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Adresse

                        <span class="text-xs text-gray-400 font-normal">
                            — facultatif
                        </span>
                    </label>

                    <input
                        id="address"
                        type="text"
                        name="address"
                        value="{{ old('address') }}"
                        maxlength="255"
                        autocomplete="street-address"
                        placeholder="Ville, quartier, adresse..."
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >

                </div>

            </div>

        </x-ui.card>


        {{-- =================================================
             03 — PARCOURS ACADÉMIQUE
        ================================================== --}}

        <x-ui.card
            title="03 — Parcours académique"
            subtitle="Informations sur la formation antérieure du candidat"
        >

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                {{-- Dernier diplôme --}}

                <div>

                    <label
                        for="last_diploma"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Dernier diplôme

                        <span class="text-xs text-gray-400 font-normal">
                            — facultatif
                        </span>
                    </label>

                    <select
                        id="last_diploma"
                        name="last_diploma"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >

                        <option value="">
                            Non renseigné
                        </option>

                        @foreach([
                            'BFEM',
                            'Baccalauréat',
                            'BTS',
                            'DUT',
                            'Licence',
                            'Master',
                            'Doctorat',
                            'Autre'
                        ] as $diploma)

                            <option
                                value="{{ $diploma }}"
                                @selected(old('last_diploma') === $diploma)
                            >
                                {{ $diploma }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Année d'obtention --}}

                <div>

                    <label
                        for="graduation_year"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Année d'obtention

                        <span class="text-xs text-gray-400 font-normal">
                            — facultatif
                        </span>
                    </label>

                    <input
                        id="graduation_year"
                        type="number"
                        name="graduation_year"
                        value="{{ old('graduation_year') }}"
                        min="1950"
                        max="{{ now()->year }}"
                        placeholder="{{ now()->year }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >

                </div>


                {{-- Établissement précédent --}}

                <div>

                    <label
                        for="previous_school"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Établissement précédent

                        <span class="text-xs text-gray-400 font-normal">
                            — facultatif
                        </span>
                    </label>

                    <input
                        id="previous_school"
                        type="text"
                        name="previous_school"
                        value="{{ old('previous_school') }}"
                        maxlength="255"
                        placeholder="Nom de l'établissement"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >

                </div>


                {{-- Filière / domaine --}}

                <div>

                    <label
                        for="academic_field"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Filière / domaine

                        <span class="text-xs text-gray-400 font-normal">
                            — facultatif
                        </span>
                    </label>

                    <input
                        id="academic_field"
                        type="text"
                        name="academic_field"
                        value="{{ old('academic_field') }}"
                        maxlength="150"
                        placeholder="Ex. Informatique, Lettres, Arts..."
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >

                </div>

            </div>

        </x-ui.card>


        {{-- =================================================
             04 — FORMATION
        ================================================== --}}

        <x-ui.card
            title="04 — Formation demandée"
            subtitle="Formation pour laquelle le candidat souhaite postuler"
        >

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label
                        for="course_id"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Formation
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="course_id"
                        name="course_id"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >
                        <option value="">
                            Sélectionner une formation
                        </option>

                        @forelse($courses as $course)
                            <option
                                value="{{ $course->id }}"
                                @selected(old('course_id') == $course->id)
                            >
                                {{ $course->title }}
                            </option>
                        @empty
                            <option value="" disabled>
                                Aucune formation active disponible
                            </option>
                        @endforelse
                    </select>

                    @if($courses->isEmpty())
                        <p class="mt-2 text-sm text-red-600">
                            Aucune formation active n'est actuellement disponible.
                        </p>
                    @endif
                </div>

                <div>
                    <label
                        for="volet"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Volet du Projet Officiel
                    </label>

                    <select
                        id="volet"
                        name="volet"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                    >
                        <option value="">Sélectionner le volet (Optionnel)</option>
                        <option value="Volet 1 — 3 mois intensif (Perfectionnement)" @selected(old('volet') === 'Volet 1 — 3 mois intensif (Perfectionnement)')>
                            Volet 1 — 3 mois intensif (Perfectionnement & Festivals)
                        </option>
                        <option value="Volet 2 — 9 mois (BTS d'État par la VAE)" @selected(old('volet') === "Volet 2 — 9 mois (BTS d'État par la VAE)")>
                            Volet 2 — 9 mois (Certification BTS d'État par la VAE)
                        </option>
                    </select>
                </div>

            </div>

        </x-ui.card>


        {{-- =================================================
             05 — MOTIVATION
        ================================================== --}}

        <x-ui.card
            title="05 — Motivation"
            subtitle="Projet et motivations du candidat"
        >

            <div>

                <label
                    for="message"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Motivation

                    <span class="text-xs text-gray-400 font-normal">
                        — facultatif
                    </span>
                </label>

                <textarea
                    id="message"
                    name="message"
                    rows="8"
                    maxlength="5000"
                    placeholder="Motivation, projet professionnel ou informations complémentaires..."
                    class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary resize-y"
                >{{ old('message') }}</textarea>

                <p class="text-xs text-gray-500 mt-2">
                    Cette information peut être complétée ultérieurement.
                </p>

            </div>

        </x-ui.card>


        {{-- =================================================
             STATUT
             Le statut n'est volontairement PAS affiché.
             Le contrôleur crée automatiquement la candidature
             avec le statut "pending".
        ================================================== --}}


        {{-- =================================================
             ACTIONS
        ================================================== --}}

        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pb-8">

            <a
                href="{{ route('admissions.index') }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-gray-200 bg-white text-gray-700 font-medium hover:bg-gray-50 transition"
            >

                <x-lucide-x class="w-4 h-4" />

                Annuler

            </a>


            <button
                type="submit"
                @disabled($courses->isEmpty())
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary text-white font-semibold hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed transition"
            >

                <x-lucide-save class="w-5 h-5" />

                Enregistrer la candidature

            </button>

        </div>

    </form>

</div>

@endsection