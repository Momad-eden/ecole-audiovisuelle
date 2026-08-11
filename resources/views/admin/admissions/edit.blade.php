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

                <a
                    href="{{ route('admissions.show', $admission) }}"
                    class="text-sm text-gray-500 hover:text-gray-900 transition"
                >
                    Dossier
                </a>

                <span class="text-gray-300">/</span>

                <span class="text-sm text-gray-500">
                    Modifier
                </span>

            </div>

            <h1 class="text-3xl font-bold text-gray-900">
                Modifier la candidature
            </h1>

            <p class="text-gray-500 mt-1">
                {{ $admission->first_name }}
                {{ $admission->last_name }}
            </p>

        </div>


        <a
            href="{{ route('admissions.show', $admission) }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-gray-200 bg-white text-gray-700 font-medium hover:bg-gray-50 transition"
        >
            <x-lucide-arrow-left class="w-4 h-4" />

            Retour au dossier
        </a>

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
        action="{{ route('admissions.update', $admission) }}"
        class="space-y-8"
    >

        @csrf
        @method('PUT')


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
                        value="{{ old('first_name', $admission->first_name) }}"
                        required
                        maxlength="100"
                        autocomplete="given-name"
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
                        value="{{ old('last_name', $admission->last_name) }}"
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
                        value="{{ old('birth_date', optional($admission->birth_date)->format('Y-m-d')) }}"
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
                        value="{{ old('birth_place', $admission->birth_place) }}"
                        maxlength="150"
                        autocomplete="off"
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
                            @selected(old('gender', $admission->gender) === 'M')
                        >
                            Masculin
                        </option>

                        <option
                            value="F"
                            @selected(old('gender', $admission->gender) === 'F')
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
                        value="{{ old('nationality', $admission->nationality) }}"
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
                        value="{{ old('phone', $admission->phone) }}"
                        required
                        maxlength="30"
                        autocomplete="tel"
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
                        value="{{ old('email', $admission->email) }}"
                        maxlength="255"
                        autocomplete="email"
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
                        value="{{ old('address', $admission->address) }}"
                        maxlength="255"
                        autocomplete="street-address"
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
                                @selected(old('last_diploma', $admission->last_diploma) === $diploma)
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
                        value="{{ old('graduation_year', $admission->graduation_year) }}"
                        min="1950"
                        max="{{ now()->year }}"
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
                        value="{{ old('previous_school', $admission->previous_school) }}"
                        maxlength="255"
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
                        value="{{ old('academic_field', $admission->academic_field) }}"
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
            subtitle="Formation choisie par le candidat"
        >

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

                    @foreach($courses as $course)

                        <option
                            value="{{ $course->id }}"
                            @selected(old('course_id', $admission->course_id) == $course->id)
                        >
                            {{ $course->title }}
                        </option>

                    @endforeach

                </select>

            </div>

        </x-ui.card>


        {{-- =================================================
             05 — STATUT
        ================================================== --}}

        <x-ui.card
            title="05 — Statut"
            subtitle="État actuel du dossier"
        >

            <div>

                <label
                    for="status"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Statut de la candidature

                    <span class="text-red-500">*</span>
                </label>

                <select
                    id="status"
                    name="status"
                    required
                    class="w-full md:w-1/2 rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                >

                    <option
                        value="pending"
                        @selected(old('status', $admission->status) === 'pending')
                    >
                        En attente
                    </option>

                    <option
                        value="approved"
                        @selected(old('status', $admission->status) === 'approved')
                    >
                        Acceptée
                    </option>

                    <option
                        value="rejected"
                        @selected(old('status', $admission->status) === 'rejected')
                    >
                        Refusée
                    </option>

                </select>

            </div>

        </x-ui.card>


        {{-- =================================================
             06 — MOTIVATION
        ================================================== --}}

        <x-ui.card
            title="06 — Motivation"
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
                    placeholder="Motivation ou informations complémentaires..."
                    class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary resize-y"
                >{{ old('message', $admission->message) }}</textarea>

                <p class="text-xs text-gray-500 mt-2">
                    Vous pouvez compléter cette information ultérieurement.
                </p>

            </div>

        </x-ui.card>


        {{-- =================================================
             ACTIONS
        ================================================== --}}

        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pb-8">

            <a
                href="{{ route('admissions.show', $admission) }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-gray-200 bg-white text-gray-700 font-medium hover:bg-gray-50 transition"
            >

                <x-lucide-x class="w-4 h-4" />

                Annuler

            </a>


            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary text-white font-semibold hover:opacity-90 transition"
            >

                <x-lucide-save class="w-5 h-5" />

                Enregistrer les modifications

            </button>

        </div>

    </form>

</div>

@endsection