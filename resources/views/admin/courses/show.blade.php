@extends('layouts.admin')

@section('title', $course->title . ' — Filière Métier EMSI')

@section('content')
<div class="space-y-8">

    {{-- =====================================================
         NAVIGATION & ACTIONS SUPÉRIEURES
    ====================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-primary mb-1">
                <a href="{{ route('courses.index') }}" class="hover:underline flex items-center gap-1">
                    <x-lucide-arrow-left class="w-3.5 h-3.5" />
                    Formations
                </a>
                <span>/</span>
                <span class="text-gray-500 truncate max-w-xs">{{ $course->title }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center gap-3">
                {{ $course->title }}
                @if($course->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Active
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                        Inactive
                    </span>
                @endif
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a
                href="{{ route('public.courses.show', $course->slug) }}"
                target="_blank"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition shadow-sm"
                title="Consulter la fiche publique sur le site"
            >
                <x-lucide-external-link class="w-4 h-4 text-gray-500" />
                Voir en public
            </a>

            <a
                href="{{ route('students.create', ['course_id' => $course->id]) }}"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition shadow-sm"
            >
                <x-lucide-user-plus class="w-4 h-4" />
                Inscrire un étudiant
            </a>

            <a
                href="{{ route('courses.edit', $course) }}"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-primary text-white text-xs font-semibold hover:bg-primary/90 transition shadow-sm"
            >
                <x-lucide-pencil class="w-4 h-4" />
                Modifier
            </a>
        </div>
    </div>

    {{-- Messages de notification --}}
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 flex items-center gap-3">
            <x-lucide-check-circle class="w-5 h-5 text-emerald-600 flex-shrink-0" />
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800 flex items-center gap-3">
            <x-lucide-alert-circle class="w-5 h-5 text-rose-600 flex-shrink-0" />
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    {{-- =====================================================
         BANNIÈRE RÉCAPITULATIVE DE LA FORMATION
    ====================================================== --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
            
            {{-- Illustration --}}
            <div class="lg:col-span-4 xl:col-span-3">
                <div class="relative aspect-video rounded-xl overflow-hidden bg-gray-900 border border-gray-200 shadow-inner">
                    @if($course->image)
                        <img
                            src="{{ asset('storage/' . ltrim($course->image, '/')) }}"
                            alt="{{ $course->title }}"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 gap-2">
                            <x-lucide-graduation-cap class="w-10 h-10 stroke-1" />
                            <span class="text-xs">Aucune image</span>
                        </div>
                    @endif
                    <div class="absolute bottom-2 left-2 px-2.5 py-1 rounded-md bg-black/70 backdrop-blur-sm text-white text-[10px] font-mono">
                        {{ $course->duration ?? 'Durée non définie' }}
                    </div>
                </div>
            </div>

            {{-- Métadonnées rapides --}}
            <div class="lg:col-span-8 xl:col-span-9 space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    @if($course->category)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-bold bg-[#310181]/10 text-[#310181] border border-[#310181]/20">
                            <x-lucide-tag class="w-3.5 h-3.5" />
                            {{ $course->category }}
                        </span>
                    @endif

                    @if($course->level)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-900 border border-amber-200">
                            <x-lucide-award class="w-3.5 h-3.5 text-amber-600" />
                            {{ $course->level }}
                        </span>
                    @endif

                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-mono font-bold bg-gray-100 text-gray-700">
                        <x-lucide-link class="w-3 h-3 text-gray-400" />
                        /formations/{{ $course->slug }}
                    </span>
                </div>

                @if($course->description)
                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">
                        {{ $course->description }}
                    </p>
                @else
                    <p class="text-sm text-gray-400 italic">
                        Aucune description détaillée n'a été saisie pour le moment.
                    </p>
                @endif

                <div class="pt-2 border-t border-gray-100 flex flex-wrap items-center gap-6 text-xs text-gray-500">
                    <div class="flex items-center gap-1.5">
                        <x-lucide-calendar class="w-4 h-4 text-gray-400" />
                        Créée le {{ $course->created_at->format('d/m/Y') }}
                    </div>
                    <div class="flex items-center gap-1.5">
                        <x-lucide-refresh-cw class="w-4 h-4 text-gray-400" />
                        Dernière mise à jour le {{ $course->updated_at->format('d/m/Y à H:i') }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- =====================================================
         4 CARTES KPIS ANALYTIQUES
    ====================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        {{-- Effectifs & Remplissage --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <span>Effectif & Quota</span>
                <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <x-lucide-users class="w-4 h-4" />
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-gray-900">{{ $enrolledStudentsCount }}</span>
                    <span class="text-sm text-gray-500 font-medium">/ {{ $capacity > 0 ? $capacity : '∞' }} inscrits</span>
                </div>
                <div class="mt-2.5">
                    <div class="flex justify-between text-[11px] text-gray-500 mb-1">
                        <span>Taux de remplissage</span>
                        <span class="font-bold text-gray-800">{{ $fillingRate }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-gray-100 overflow-hidden">
                        <div
                            class="h-full rounded-full transition-all duration-500 {{ $fillingRate >= 100 ? 'bg-rose-500' : ($fillingRate >= 75 ? 'bg-amber-500' : 'bg-blue-600') }}"
                            style="width: {{ $fillingRate }}%"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Candidatures reçues --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <span>Candidatures</span>
                <span class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                    <x-lucide-file-text class="w-4 h-4" />
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-gray-900">{{ $admissionsTotal }}</span>
                    <span class="text-sm text-gray-500 font-medium">postulants</span>
                </div>
                <div class="mt-2.5 flex items-center gap-2 text-[11px]">
                    <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-800 font-semibold font-mono">
                        V1: {{ $admissionsVolet1 }}
                    </span>
                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-semibold font-mono">
                        V2 VAE: {{ $admissionsVolet2 }}
                    </span>
                    @if($admissionsPending > 0)
                        <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-bold ml-auto">
                            {{ $admissionsPending }} en attente
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tarif & Chiffre d'Affaires Prévisionnel --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <span>Scolarité Unitaire</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <x-lucide-wallet class="w-4 h-4" />
                </span>
            </div>
            <div>
                <div class="text-2xl font-mono font-bold text-gray-900">
                    {{ number_format($course->price, 0, ',', ' ') }} <span class="text-xs font-sans text-gray-500 font-normal">FCFA</span>
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    Total attendu : <strong class="font-mono text-gray-800">{{ number_format($expectedRevenue, 0, ',', ' ') }} FCFA</strong>
                </p>
            </div>
        </div>

        {{-- Recouvrement Financier --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-3">
            <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <span>Recouvrement Réel</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <x-lucide-badge-check class="w-4 h-4" />
                </span>
            </div>
            <div>
                <div class="text-2xl font-mono font-bold text-emerald-700">
                    {{ number_format($collectedRevenue, 0, ',', ' ') }} <span class="text-xs font-sans text-emerald-600 font-normal">FCFA</span>
                </div>
                <div class="mt-2.5">
                    <div class="flex justify-between text-[11px] text-gray-500 mb-1">
                        <span>Reste dû : {{ number_format($remainingRevenue, 0, ',', ' ') }} FCFA</span>
                        <span class="font-bold text-emerald-700">{{ $financialRecoveryRate }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-gray-100 overflow-hidden">
                        <div
                            class="h-full rounded-full bg-emerald-500 transition-all duration-500"
                            style="width: {{ $financialRecoveryRate }}%"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- =====================================================
         DEUX GRANDES SECTIONS : APPRENANTS & CANDIDATURES
    ====================================================== --}}
    <div class="space-y-8" x-data="{ activeTab: 'students' }">
        
        {{-- Sélecteur d'onglets --}}
        <div class="border-b border-gray-200 flex items-center gap-6">
            <button
                type="button"
                @click="activeTab = 'students'"
                class="pb-3 text-sm font-bold border-b-2 transition-all flex items-center gap-2"
                :class="activeTab === 'students' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-900'"
            >
                <x-lucide-users class="w-4 h-4" />
                Apprenants Inscrits ({{ $enrolledStudentsCount }})
            </button>

            <button
                type="button"
                @click="activeTab = 'admissions'"
                class="pb-3 text-sm font-bold border-b-2 transition-all flex items-center gap-2"
                :class="activeTab === 'admissions' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-900'"
            >
                <x-lucide-file-text class="w-4 h-4" />
                Dossiers de Candidature ({{ $admissionsTotal }})
                @if($admissionsPending > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white">
                        {{ $admissionsPending }}
                    </span>
                @endif
            </button>
        </div>

        {{-- =================================================
             ONGLET 1 : LISTE DES ÉTUDIANTS INSCRITS
        ================================================== --}}
        <div x-show="activeTab === 'students'" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">
                        Liste des apprenants inscrits dans cette filière
                    </h3>
                    <p class="text-xs text-gray-500">
                        Suivi pédagogique et situation financière individuelle de la promotion.
                    </p>
                </div>
                <a
                    href="{{ route('students.create', ['course_id' => $course->id]) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-gray-900 text-white text-xs font-semibold hover:bg-black transition"
                >
                    <x-lucide-plus class="w-3.5 h-3.5" />
                    Ajouter un apprenant
                </a>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Matricule & Étudiant</th>
                                <th class="px-4 py-3.5">Contact</th>
                                <th class="px-4 py-3.5">Date d'inscription</th>
                                <th class="px-4 py-3.5 text-right">Scolarité Versée</th>
                                <th class="px-4 py-3.5 text-center">État Recouvrement</th>
                                <th class="px-5 py-3.5 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($course->students as $student)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-[#310181]/10 text-[#310181] flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('students.show', $student) }}" class="font-bold text-gray-900 hover:text-primary transition hover:underline block text-sm">
                                                    {{ $student->full_name }}
                                                </a>
                                                <span class="text-[11px] font-mono text-gray-400">
                                                    {{ $student->matricule ?? 'Sans matricule' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3.5 text-xs text-gray-600">
                                        <div>{{ $student->phone ?? '—' }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $student->email ?? '—' }}</div>
                                    </td>

                                    <td class="px-4 py-3.5 text-xs text-gray-600 font-medium">
                                        {{ $student->registration_date ? \Carbon\Carbon::parse($student->registration_date)->format('d/m/Y') : $student->created_at->format('d/m/Y') }}
                                    </td>

                                    <td class="px-4 py-3.5 text-right font-mono font-bold text-xs">
                                        <span class="text-emerald-700">{{ number_format($student->total_paid, 0, ',', ' ') }} FCFA</span>
                                        @if($student->remaining_due > 0)
                                            <div class="text-[10px] text-rose-600 font-normal">
                                                Reste : {{ number_format($student->remaining_due, 0, ',', ' ') }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3.5 text-center">
                                        @if($student->payment_status === 'paid')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                Soldé 100%
                                            </span>
                                        @elseif($student->payment_status === 'partial')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                                Partiel ({{ $student->payment_percentage }}%)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                                                Non réglé
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a
                                                href="{{ route('students.show', $student) }}"
                                                class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition"
                                                title="Fiche apprenant 360°"
                                            >
                                                <x-lucide-eye class="w-4 h-4" />
                                            </a>

                                            <a
                                                href="{{ route('payments.create', ['student_id' => $student->id]) }}"
                                                class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition"
                                                title="Encaisser un versement"
                                            >
                                                <x-lucide-plus-circle class="w-4 h-4" />
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-500">
                                        <div class="max-w-sm mx-auto space-y-3">
                                            <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto">
                                                <x-lucide-user-x class="w-6 h-6 stroke-1" />
                                            </div>
                                            <p class="font-medium text-gray-700 text-sm">
                                                Aucun apprenant n'est encore inscrit dans cette filière.
                                            </p>
                                            <a
                                                href="{{ route('students.create', ['course_id' => $course->id]) }}"
                                                class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:underline"
                                            >
                                                + Inscrire le premier apprenant
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- =================================================
             ONGLET 2 : LISTE DES CANDIDATURES
        ================================================== --}}
        <div x-show="activeTab === 'admissions'" class="space-y-4" style="display: none;">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">
                        Candidatures reçues pour cette formation
                    </h3>
                    <p class="text-xs text-gray-500">
                        Postulants en attente d'évaluation ou déjà validés.
                    </p>
                </div>
                <a
                    href="{{ route('admissions.index', ['course_id' => $course->id]) }}"
                    class="text-xs font-semibold text-primary hover:underline"
                >
                    Voir dans le module Admissions &rarr;
                </a>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Candidat</th>
                                <th class="px-4 py-3.5">Volet Choisi</th>
                                <th class="px-4 py-3.5">Contact</th>
                                <th class="px-4 py-3.5">Date de dépôt</th>
                                <th class="px-4 py-3.5 text-center">Statut du dossier</th>
                                <th class="px-5 py-3.5 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($course->admissions as $admission)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-gray-900 text-sm">
                                            {{ $admission->first_name }} {{ $admission->last_name }}
                                        </div>
                                        <div class="text-[11px] text-gray-400">
                                            {{ $admission->city ?? 'Dakar' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3.5">
                                        @if($admission->volet === 'volet2')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                                Volet 2 — BTS-VAE (9 mois)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-900 border border-purple-200">
                                                Volet 1 — Pratique (3 mois)
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3.5 text-xs text-gray-600">
                                        <div>{{ $admission->phone }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $admission->email }}</div>
                                    </td>

                                    <td class="px-4 py-3.5 text-xs text-gray-600 font-medium">
                                        {{ $admission->created_at->format('d/m/Y') }}
                                    </td>

                                    <td class="px-4 py-3.5 text-center">
                                        @if($admission->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                Validée
                                            </span>
                                        @elseif($admission->status === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                                                Rejetée
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 animate-pulse">
                                                En attente
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-3.5 text-center">
                                        <a
                                            href="{{ route('admissions.show', $admission) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-primary hover:text-white text-gray-700 text-xs font-semibold transition"
                                        >
                                            <x-lucide-eye class="w-3.5 h-3.5" />
                                            Examiner
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-500">
                                        <div class="max-w-sm mx-auto space-y-2">
                                            <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto">
                                                <x-lucide-inbox class="w-6 h-6 stroke-1" />
                                            </div>
                                            <p class="font-medium text-gray-700 text-sm">
                                                Aucune candidature reçue pour le moment pour cette filière.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
