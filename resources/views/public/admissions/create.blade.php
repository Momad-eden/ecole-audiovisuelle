@extends('layouts.public')

@section('title', 'Candidater — EMSI')

@section('description')
Déposez votre candidature pour rejoindre l'EMSI,
École de Formation Audiovisuelle au Grand Théâtre National Doudou Ndiaye Rose.
@endsection

@section('content')

<section class="min-h-screen bg-[#F5F3EE] text-[#111111]">

    {{-- =====================================================
         INTRODUCTION
    ====================================================== --}}

    <div class="border-b border-black/10">

        <div class="max-w-[1500px] mx-auto px-6 lg:px-12 py-16 lg:py-24">

            <div class="grid lg:grid-cols-12 gap-10 items-end">

                {{-- Titre --}}

                <div class="lg:col-span-8">

                    <div class="flex items-center gap-4 mb-7">

                        <span
                            class="text-[10px] uppercase tracking-[0.3em] text-black/40">
                            Candidature
                        </span>

                        <span class="w-12 h-px bg-[#F5B800]"></span>

                        <span
                            class="text-[10px] uppercase tracking-[0.3em] text-[#B78900]">
                            EMSI
                        </span>

                    </div>


                    <h1
                        class="font-serif text-[clamp(3.5rem,8vw,8rem)]
                               leading-[0.82] tracking-[-0.06em]">

                        Votre regard

                        <span class="italic text-black/35">
                            commence ici.
                        </span>

                    </h1>

                </div>


                {{-- Introduction --}}

                <div class="lg:col-span-4 lg:pb-2">

                    <p
                        class="max-w-md text-sm lg:text-base
                               leading-7 text-black/55">
                        Vous souhaitez développer votre regard,
                        votre technique et votre créativité dans
                        l'univers audiovisuel ?
                    </p>

                    <p
                        class="mt-5 text-[10px] uppercase
                               tracking-[0.25em] text-black/35">
                        Grand Théâtre National
                        Doudou Ndiaye Rose · Dakar
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         CONTENU
    ====================================================== --}}

    <div class="max-w-[1500px] mx-auto px-6 lg:px-12">

        {{-- =================================================
             ERREURS
        ================================================== --}}

        @if($errors->any())

        <div
            class="mt-10 border border-red-200
                       bg-red-50 rounded-2xl p-6">

            <div class="flex items-start gap-4">

                <div
                    class="flex-shrink-0 w-9 h-9 rounded-full
                               bg-red-100 text-red-600
                               flex items-center justify-center">

                    <x-lucide-alert-circle class="w-5 h-5" />

                </div>


                <div>

                    <p class="font-semibold text-red-800">
                        Vérifiez votre candidature
                    </p>

                    <ul
                        class="mt-2 space-y-1 text-sm
                                   text-red-700">

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


        {{-- =================================================
             FORMULAIRE
        ================================================== --}}

        <form
            method="POST"
            action="{{ route('public.admissions.store') }}"
            class="py-14 lg:py-20">

            @csrf


            {{-- =================================================
                 01 — IDENTITÉ
            ================================================== --}}

            <section class="border-t border-black/10 py-12 lg:py-16">

                <div class="grid lg:grid-cols-12 gap-10 lg:gap-16">

                    {{-- Label section --}}

                    <div class="lg:col-span-3">

                        <div class="lg:sticky lg:top-28">

                            <div class="flex items-center gap-3">

                                <span
                                    class="text-sm font-semibold
                                           text-[#B78900]">
                                    01
                                </span>

                                <span
                                    class="text-[10px]
                                           uppercase tracking-[0.25em]
                                           text-black/40">
                                    Identité
                                </span>

                            </div>


                            <h2
                                class="mt-5 font-serif
                                       text-3xl lg:text-4xl
                                       tracking-[-0.03em]">
                                Qui êtes-vous ?
                            </h2>


                            <p
                                class="mt-4 max-w-xs
                                       text-sm leading-6
                                       text-black/45">
                                Quelques informations pour
                                nous permettre de mieux vous
                                connaître.
                            </p>

                        </div>

                    </div>


                    {{-- Champs --}}

                    <div
                        class="lg:col-span-9
                               grid md:grid-cols-2
                               gap-6">

                        {{-- Prénom --}}

                        <div>

                            <label
                                for="first_name"
                                class="block mb-2 text-xs
                                       font-medium text-black/60">
                                Prénom
                                <span class="text-[#B78900]">*</span>
                            </label>

                            <input
                                id="first_name"
                                name="first_name"
                                type="text"
                                value="{{ old('first_name') }}"
                                required
                                autocomplete="given-name"
                                placeholder="Votre prénom"
                                class="w-full rounded-xl
                                       border border-black/10
                                       bg-white px-4 py-4
                                       text-sm text-black
                                       placeholder:text-black/25
                                       outline-none
                                       focus:border-[#F5B800]
                                       focus:ring-4
                                       focus:ring-[#F5B800]/10
                                       transition">

                        </div>


                        {{-- Nom --}}

                        <div>

                            <label
                                for="last_name"
                                class="block mb-2 text-xs
                                       font-medium text-black/60">
                                Nom
                                <span class="text-[#B78900]">*</span>
                            </label>

                            <input
                                id="last_name"
                                name="last_name"
                                type="text"
                                value="{{ old('last_name') }}"
                                required
                                autocomplete="family-name"
                                placeholder="Votre nom"
                                class="w-full rounded-xl
                                       border border-black/10
                                       bg-white px-4 py-4
                                       text-sm text-black
                                       placeholder:text-black/25
                                       outline-none
                                       focus:border-[#F5B800]
                                       focus:ring-4
                                       focus:ring-[#F5B800]/10
                                       transition">

                        </div>


                        {{-- Date naissance --}}

                        <div>

                            <label
                                for="birth_date"
                                class="block mb-2 text-xs
                                       font-medium text-black/60">
                                Date de naissance
                            </label>

                            <input
                                id="birth_date"
                                name="birth_date"
                                type="date"
                                value="{{ old('birth_date') }}"
                                autocomplete="bday"
                                class="w-full rounded-xl
                                       border border-black/10
                                       bg-white px-4 py-4
                                       text-sm text-black
                                       outline-none
                                       focus:border-[#F5B800]
                                       focus:ring-4
                                       focus:ring-[#F5B800]/10
                                       transition">

                        </div>


                        {{-- Lieu naissance --}}

                        <div>

                            <label
                                for="birth_place"
                                class="block mb-2 text-xs
                                       font-medium text-black/60">
                                Lieu de naissance
                            </label>

                            <input
                                id="birth_place"
                                name="birth_place"
                                type="text"
                                value="{{ old('birth_place') }}"
                                placeholder="Ville / pays"
                                class="w-full rounded-xl
                                       border border-black/10
                                       bg-white px-4 py-4
                                       text-sm text-black
                                       placeholder:text-black/25
                                       outline-none
                                       focus:border-[#F5B800]
                                       focus:ring-4
                                       focus:ring-[#F5B800]/10
                                       transition">

                        </div>


                        {{-- Genre --}}

                        <div>

                            <label
                                for="gender"
                                class="block mb-2 text-xs
                                       font-medium text-black/60">
                                Genre
                            </label>

                            <select
                                id="gender"
                                name="gender"
                                class="w-full rounded-xl
                                       border border-black/10
                                       bg-white px-4 py-4
                                       text-sm text-black
                                       outline-none
                                       focus:border-[#F5B800]
                                       focus:ring-4
                                       focus:ring-[#F5B800]/10
                                       transition">

                                <option value="">
                                    Sélectionner
                                </option>

                                <option
                                    value="M"
                                    @selected(old('gender')==='M' )>
                                    Homme
                                </option>

                                <option
                                    value="F"
                                    @selected(old('gender')==='F' )>
                                    Femme
                                </option>

                            </select>

                        </div>


                        {{-- Nationalité --}}

                        <div>

                            <label
                                for="nationality"
                                class="block mb-2 text-xs
               font-medium text-black/60">
                                Nationalité
                            </label>

                            <select
                                id="nationality"
                                name="nationality"
                                class="w-full rounded-xl
               border border-black/10
               bg-white px-4 py-4
               text-sm text-black
               outline-none
               focus:border-[#F5B800]
               focus:ring-4
               focus:ring-[#F5B800]/10
               transition">

                                <option value="">
                                    Sélectionner votre nationalité
                                </option>

                                <option value="Sénégalaise"
                                    @selected(old('nationality')==='Sénégalaise' )>
                                    Sénégalaise
                                </option>

                                <option value="Malienne"
                                    @selected(old('nationality')==='Malienne' )>
                                    Malienne
                                </option>

                                <option value="Mauritanienne"
                                    @selected(old('nationality')==='Mauritanienne' )>
                                    Mauritanienne
                                </option>

                                <option value="Guinéenne"
                                    @selected(old('nationality')==='Guinéenne' )>
                                    Guinéenne
                                </option>

                                <option value="Ivoirienne"
                                    @selected(old('nationality')==='Ivoirienne' )>
                                    Ivoirienne
                                </option>

                                <option value="Burkinabè"
                                    @selected(old('nationality')==='Burkinabè' )>
                                    Burkinabè
                                </option>

                                <option value="Nigérienne"
                                    @selected(old('nationality')==='Nigérienne' )>
                                    Nigérienne
                                </option>

                                <option value="Togolaise"
                                    @selected(old('nationality')==='Togolaise' )>
                                    Togolaise
                                </option>

                                <option value="Béninoise"
                                    @selected(old('nationality')==='Béninoise' )>
                                    Béninoise
                                </option>

                                <option value="Camerounaise"
                                    @selected(old('nationality')==='Camerounaise' )>
                                    Camerounaise
                                </option>

                                <option value="Gabonaise"
                                    @selected(old('nationality')==='Gabonaise' )>
                                    Gabonaise
                                </option>

                                <option value="Congolaise"
                                    @selected(old('nationality')==='Congolaise' )>
                                    Congolaise
                                </option>

                                <option value="Autre"
                                    @selected(old('nationality')==='Autre' )>
                                    Autre
                                </option>

                            </select>

                        </div>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 02 — COORDONNÉES
            ================================================== --}}

            <section class="border-t border-black/10 py-12 lg:py-16">

                <div class="grid lg:grid-cols-12 gap-10 lg:gap-16">

                    <div class="lg:col-span-3">

                        <div class="lg:sticky lg:top-28">

                            <div class="flex items-center gap-3">

                                <span
                                    class="text-sm font-semibold
                                           text-[#B78900]">
                                    02
                                </span>

                                <span
                                    class="text-[10px]
                                           uppercase tracking-[0.25em]
                                           text-black/40">
                                    Coordonnées
                                </span>

                            </div>


                            <h2
                                class="mt-5 font-serif
                                       text-3xl lg:text-4xl
                                       tracking-[-0.03em]">
                                Où vous joindre ?
                            </h2>

                        </div>

                    </div>


                    <div
                        class="lg:col-span-9
                               grid md:grid-cols-2
                               gap-6">

                        {{-- Téléphone --}}

                        <div>

                            <label
                                for="phone"
                                class="block mb-2 text-xs
                                       font-medium text-black/60">
                                Téléphone
                                <span class="text-[#B78900]">*</span>
                            </label>

                            <input
                                id="phone"
                                name="phone"
                                type="tel"
                                value="{{ old('phone') }}"
                                required
                                autocomplete="tel"
                                placeholder="+221 77 000 00 00"
                                class="w-full rounded-xl
                                       border border-black/10
                                       bg-white px-4 py-4
                                       text-sm text-black
                                       placeholder:text-black/25
                                       outline-none
                                       focus:border-[#F5B800]
                                       focus:ring-4
                                       focus:ring-[#F5B800]/10
                                       transition">

                        </div>


                        {{-- Email --}}

                        <div>

                            <label
                                for="email"
                                class="block mb-2 text-xs
                                       font-medium text-black/60">
                                Adresse e-mail
                            </label>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                placeholder="vous@exemple.com"
                                class="w-full rounded-xl
                                       border border-black/10
                                       bg-white px-4 py-4
                                       text-sm text-black
                                       placeholder:text-black/25
                                       outline-none
                                       focus:border-[#F5B800]
                                       focus:ring-4
                                       focus:ring-[#F5B800]/10
                                       transition">

                        </div>


                        {{-- Adresse --}}

                        <div class="md:col-span-2">

                            <label
                                for="address"
                                class="block mb-2 text-xs
                                       font-medium text-black/60">
                                Adresse
                            </label>

                            <input
                                id="address"
                                name="address"
                                type="text"
                                value="{{ old('address') }}"
                                autocomplete="street-address"
                                placeholder="Votre adresse actuelle"
                                class="w-full rounded-xl
                                       border border-black/10
                                       bg-white px-4 py-4
                                       text-sm text-black
                                       placeholder:text-black/25
                                       outline-none
                                       focus:border-[#F5B800]
                                       focus:ring-4
                                       focus:ring-[#F5B800]/10
                                       transition">

                        </div>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 03 — PARCOURS ACADÉMIQUE
            ================================================== --}}

            <section class="border-t border-black/10 py-12 lg:py-16">

                <div class="grid lg:grid-cols-12 gap-10 lg:gap-16">

                    <div class="lg:col-span-3">

                        <div class="lg:sticky lg:top-28">

                            <div class="flex items-center gap-3">

                                <span
                                    class="text-sm font-semibold
                                           text-[#B78900]">
                                    03
                                </span>

                                <span
                                    class="text-[10px]
                                           uppercase tracking-[0.25em]
                                           text-black/40">
                                    Parcours
                                </span>

                            </div>


                            <h2
                                class="mt-5 font-serif
                                       text-3xl lg:text-4xl
                                       tracking-[-0.03em]">
                                Votre parcours
                            </h2>


                            <p
                                class="mt-4 max-w-xs
                                       text-sm leading-6
                                       text-black/45">
                                Parlez-nous de votre formation
                                et de vos expériences précédentes.
                            </p>

                        </div>

                    </div>


                    <div
                        class="lg:col-span-9
                               grid md:grid-cols-2
                               gap-6">

                        {{-- Dernier diplôme --}}

                        <div>

                            <label
                                for="last_diploma"
                                class="block mb-2 text-xs
               font-medium text-black/60">
                                Dernier diplôme
                            </label>

                            <select
                                id="last_diploma"
                                name="last_diploma"
                                class="w-full rounded-xl
               border border-black/10
               bg-white px-4 py-4
               text-sm text-black
               outline-none
               focus:border-[#F5B800]
               focus:ring-4
               focus:ring-[#F5B800]/10
               transition">

                                <option value="">
                                    Sélectionner votre dernier diplôme
                                </option>

                                <option value="BFEM"
                                    @selected(old('last_diploma')==='BFEM' )>
                                    BFEM
                                </option>

                                <option value="Baccalauréat"
                                    @selected(old('last_diploma')==='Baccalauréat' )>
                                    Baccalauréat
                                </option>

                                <option value="BTS"
                                    @selected(old('last_diploma')==='BTS' )>
                                    BTS
                                </option>

                                <option value="DUT"
                                    @selected(old('last_diploma')==='DUT' )>
                                    DUT
                                </option>

                                <option value="Licence"
                                    @selected(old('last_diploma')==='Licence' )>
                                    Licence
                                </option>

                                <option value="Master"
                                    @selected(old('last_diploma')==='Master' )>
                                    Master
                                </option>

                                <option value="Doctorat"
                                    @selected(old('last_diploma')==='Doctorat' )>
                                    Doctorat
                                </option>

                                <option value="Autre"
                                    @selected(old('last_diploma')==='Autre' )>
                                    Autre
                                </option>

                            </select>

                        </div>


                        {{-- Année --}}

                        <div>

                            <label
                                for="graduation_year"
                                class="block mb-2 text-xs
                                       font-medium text-black/60">
                                Année d'obtention
                            </label>

                            <input
                                id="graduation_year"
                                name="graduation_year"
                                type="number"
                                value="{{ old('graduation_year') }}"
                                min="1950"
                                max="{{ now()->year }}"
                                placeholder="{{ now()->year }}"
                                class="w-full rounded-xl
                                       border border-black/10
                                       bg-white px-4 py-4
                                       text-sm text-black
                                       placeholder:text-black/25
                                       outline-none
                                       focus:border-[#F5B800]
                                       focus:ring-4
                                       focus:ring-[#F5B800]/10
                                       transition">

                        </div>


                        {{-- Établissement --}}

                        <div>

                            <label
                                for="previous_school"
                                class="block mb-2 text-xs
                                       font-medium text-black/60">
                                Établissement précédent
                            </label>

                            <input
                                id="previous_school"
                                name="previous_school"
                                type="text"
                                value="{{ old('previous_school') }}"
                                placeholder="Nom de l'établissement"
                                class="w-full rounded-xl
                                       border border-black/10
                                       bg-white px-4 py-4
                                       text-sm text-black
                                       placeholder:text-black/25
                                       outline-none
                                       focus:border-[#F5B800]
                                       focus:ring-4
                                       focus:ring-[#F5B800]/10
                                       transition">

                        </div>


                        {{-- Domaine --}}

                        <div>

                            <label
                                for="academic_field"
                                class="block mb-2 text-xs
                                       font-medium text-black/60">
                                Domaine / filière
                            </label>

                            <input
                                id="academic_field"
                                name="academic_field"
                                type="text"
                                value="{{ old('academic_field') }}"
                                placeholder="Ex. Communication, Arts..."
                                class="w-full rounded-xl
                                       border border-black/10
                                       bg-white px-4 py-4
                                       text-sm text-black
                                       placeholder:text-black/25
                                       outline-none
                                       focus:border-[#F5B800]
                                       focus:ring-4
                                       focus:ring-[#F5B800]/10
                                       transition">

                        </div>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 04 — PROJET ET FORMATION
            ================================================== --}}

            <section class="border-t border-black/10 py-12 lg:py-16">

                <div class="grid lg:grid-cols-12 gap-10 lg:gap-16">

                    <div class="lg:col-span-3">

                        <div class="lg:sticky lg:top-28">

                            <div class="flex items-center gap-3">

                                <span
                                    class="text-sm font-semibold
                                           text-[#B78900]">
                                    04
                                </span>

                                <span
                                    class="text-[10px]
                                           uppercase tracking-[0.25em]
                                           text-black/40">
                                    Projet
                                </span>

                            </div>


                            <h2
                                class="mt-5 font-serif
                                       text-3xl lg:text-4xl
                                       tracking-[-0.03em]">
                                Où voulez-vous aller ?
                            </h2>

                        </div>

                    </div>


                    <div class="lg:col-span-9">


                        {{-- =========================================================
     FORMATIONS
========================================================== --}}

                        <div>

                            <div class="flex items-end justify-between gap-6 mb-5">

                                <div>

                                    <label
                                        class="block text-xs font-semibold
                       uppercase tracking-[0.18em]
                       text-black/60">
                                        Formation souhaitée
                                        <span class="text-[#B78900]">*</span>
                                    </label>

                                    <p class="mt-2 text-sm text-black/40">
                                        Sélectionnez la formation que vous souhaitez intégrer.
                                    </p>

                                </div>

                                @error('course_id')

                                <span
                                    class="
                    text-xs
                    font-medium
                    text-red-600
                ">
                                    {{ $message }}
                                </span>

                                @enderror

                            </div>


                            @if($courses->count())

                            <div
                                class="
                grid
                grid-cols-1
                md:grid-cols-2
                xl:grid-cols-3
                gap-4
            ">

                                @foreach($courses as $course)

                                <label
                                    class="
                        group
                        relative
                        block
                        cursor-pointer
                    ">

                                    {{-- =================================================
                         VRAI INPUT RADIO
                    ================================================== --}}

                                    <input
                                        type="radio"
                                        name="course_id"
                                        value="{{ $course->id }}"
                                        class="peer sr-only"

                                        @checked(
                                        (string) old('course_id')===(string) $course->id
                                    )

                                    required
                                    >


                                    {{-- =================================================
                         CARTE
                    ================================================== --}}

                                    <div
                                        class="
                            relative
                            h-full
                            overflow-hidden

                            rounded-2xl

                            border
                            border-black/10

                            bg-white

                            p-5

                            transition-all
                            duration-200

                            group-hover:-translate-y-0.5
                            group-hover:border-black/20
                            group-hover:shadow-lg
                            group-hover:shadow-black/5

                            peer-focus-visible:ring-4
                            peer-focus-visible:ring-[#F5B800]/20

                            peer-checked:border-[#F5B800]
                            peer-checked:bg-[#FFFDF5]
                            peer-checked:ring-2
                            peer-checked:ring-[#F5B800]/25
                        ">

                                        {{-- Petite ligne supérieure lorsque sélectionné --}}

                                        <div
                                            class="
                                absolute
                                inset-x-0
                                top-0
                                h-1

                                bg-[#F5B800]

                                opacity-0

                                transition-opacity
                                duration-200

                                peer-checked:opacity-100
                            "></div>


                                        {{-- =================================================
                             EN-TÊTE DE LA CARTE
                        ================================================== --}}

                                        <div
                                            class="
                                flex
                                items-start
                                justify-between
                                gap-4
                            ">

                                            <div class="min-w-0">

                                                {{-- Catégorie --}}

                                                @if($course->category)

                                                <span
                                                    class="
                                            inline-block
                                            mb-2

                                            text-[9px]
                                            font-semibold
                                            uppercase
                                            tracking-[0.18em]

                                            text-[#B78900]
                                        ">
                                                    {{ $course->category }}
                                                </span>

                                                @endif


                                                {{-- Nom de la formation --}}

                                                <h3
                                                    class="
                                        text-base
                                        font-semibold
                                        leading-6

                                        text-[#111111]
                                    ">
                                                    {{ $course->title }}
                                                </h3>

                                            </div>


                                            {{-- =================================================
                                 RADIO VISUEL
                            ================================================== --}}

                                            <span
                                                class="
                                    flex
                                    h-6
                                    w-6
                                    shrink-0

                                    items-center
                                    justify-center

                                    rounded-full

                                    border-2
                                    border-black/15

                                    bg-white

                                    transition-all
                                    duration-200

                                    peer-checked:border-[#F5B800]
                                    peer-checked:bg-[#F5B800]
                                ">

                                                <span
                                                    class="
                                        h-2.5
                                        w-2.5

                                        rounded-full

                                        bg-white

                                        opacity-0
                                        scale-50

                                        transition-all
                                        duration-200

                                        peer-checked:opacity-100
                                        peer-checked:scale-100
                                    "></span>

                                            </span>

                                        </div>


                                        {{-- =================================================
                             DESCRIPTION
                        ================================================== --}}

                                        @if($course->description)

                                        <p
                                            class="
                                    mt-4

                                    text-sm
                                    leading-6

                                    text-black/50

                                    line-clamp-3
                                ">
                                            {{ $course->description }}
                                        </p>

                                        @endif


                                        {{-- =================================================
                             INFORMATIONS
                        ================================================== --}}

                                        <div
                                            class="
                                mt-5

                                flex
                                flex-wrap
                                items-center
                                gap-2
                            ">

                                            {{-- Durée --}}

                                            @if($course->duration)

                                            <span
                                                class="
                                        inline-flex
                                        items-center
                                        gap-1.5

                                        rounded-full

                                        bg-black/[0.035]

                                        px-3
                                        py-1.5

                                        text-[10px]
                                        font-medium
                                        uppercase
                                        tracking-[0.08em]

                                        text-black/50
                                    ">

                                                <x-lucide-clock-3
                                                    class="h-3 w-3" />

                                                {{ $course->duration }}

                                            </span>

                                            @endif


                                            {{-- Niveau --}}

                                            @if($course->level)

                                            <span
                                                class="
                                        inline-flex
                                        items-center
                                        gap-1.5

                                        rounded-full

                                        bg-black/[0.035]

                                        px-3
                                        py-1.5

                                        text-[10px]
                                        font-medium
                                        uppercase
                                        tracking-[0.08em]

                                        text-black/50
                                    ">

                                                <x-lucide-graduation-cap
                                                    class="h-3 w-3" />

                                                {{ $course->level }}

                                            </span>

                                            @endif


                                            {{-- Prix --}}

                                            @if($course->price)

                                            <span
                                                class="
                                        inline-flex
                                        items-center
                                        gap-1.5

                                        rounded-full

                                        bg-[#F5B800]/10

                                        px-3
                                        py-1.5

                                        text-[10px]
                                        font-semibold

                                        text-[#8A6800]
                                    ">

                                                {{ number_format($course->price, 0, ',', ' ') }}
                                                FCFA

                                            </span>

                                            @endif

                                        </div>


                                        {{-- =================================================
                             ÉTAT SÉLECTIONNÉ
                        ================================================== --}}

                                        <div
                                            class="
                                mt-5
                                flex
                                items-center
                                gap-2

                                text-xs
                                font-semibold

                                text-[#9A7500]

                                opacity-0
                                translate-y-1

                                transition-all
                                duration-200

                                peer-checked:opacity-100
                                peer-checked:translate-y-0
                            ">

                                            <x-lucide-circle-check
                                                class="h-4 w-4" />

                                            Formation sélectionnée

                                        </div>

                                    </div>

                                </label>

                                @endforeach

                            </div>


                            @else

                            {{-- =================================================
             AUCUNE FORMATION
        ================================================== --}}

                            <div
                                class="
                rounded-2xl

                border
                border-red-200

                bg-red-50

                p-6
            ">

                                <div class="flex items-start gap-4">

                                    <div
                                        class="
                        flex
                        h-10
                        w-10
                        shrink-0

                        items-center
                        justify-center

                        rounded-full

                        bg-red-100

                        text-red-600
                    ">

                                        <x-lucide-alert-circle
                                            class="h-5 w-5" />

                                    </div>


                                    <div>

                                        <p
                                            class="
                            font-semibold
                            text-red-800
                        ">
                                            Aucune formation disponible
                                        </p>

                                        <p
                                            class="
                            mt-1
                            text-sm
                            leading-6
                            text-red-700
                        ">
                                            Aucune formation n'est actuellement ouverte
                                            aux candidatures.
                                        </p>

                                    </div>

                                </div>

                            </div>

                            @endif

                        </div>


                        {{-- MOTIVATION --}}

                        <div class="mt-10">

                            <label
                                for="message"
                                class="block mb-2 text-xs
                                       font-medium text-black/60">
                                Votre motivation
                            </label>

                            <textarea
                                id="message"
                                name="message"
                                rows="7"
                                maxlength="5000"
                                placeholder="Pourquoi souhaitez-vous rejoindre EMSI ? Parlez-nous de votre projet, de ce qui vous attire dans l'audiovisuel et de ce que vous souhaitez apprendre..."
                                class="w-full rounded-2xl
                                       border border-black/10
                                       bg-white px-5 py-5
                                       text-sm leading-7
                                       text-black
                                       placeholder:text-black/25
                                       resize-y
                                       outline-none
                                       focus:border-[#F5B800]
                                       focus:ring-4
                                       focus:ring-[#F5B800]/10
                                       transition">{{ old('message') }}</textarea>

                            <p
                                class="mt-2 text-xs text-black/35">
                                Facultatif · 5000 caractères maximum
                            </p>

                        </div>


                        {{-- ENVOI --}}

                        <div
                            class="mt-12 pt-8
                                   border-t border-black/10
                                   flex flex-col
                                   sm:flex-row
                                   sm:items-center
                                   sm:justify-between
                                   gap-6">

                            <p
                                class="max-w-lg text-xs
                                       leading-6 text-black/40">
                                Les informations transmises seront utilisées
                                uniquement pour l'étude et le traitement
                                de votre candidature.
                            </p>


                            <button
                                type="submit"
                                class="group inline-flex
                                       items-center justify-center
                                       gap-4 rounded-full
                                       bg-[#111111]
                                       px-7 py-4
                                       text-sm font-semibold
                                       text-white
                                       hover:bg-[#F5B800]
                                       hover:text-black
                                       transition">

                                Envoyer ma candidature

                                <span
                                    class="flex w-8 h-8
                                           items-center justify-center
                                           rounded-full
                                           bg-[#F5B800]
                                           text-black
                                           transition
                                           group-hover:bg-black
                                           group-hover:text-white">

                                    <x-lucide-arrow-up-right
                                        class="w-4 h-4" />

                                </span>

                            </button>

                        </div>

                    </div>

                </div>

            </section>

        </form>

    </div>

</section>

@endsection