@extends('layouts.admin')

@section('title', 'Fiche Étudiant — ' . $student->full_name)

@section('header_title', 'Fiche Étudiant')
@section('header_subtitle', $student->full_name . ' • ' . ($student->student_number ?? 'Matricule non défini'))

@section('content')

<div class="max-w-6xl mx-auto space-y-8">

    {{-- =========================================================
         1. EN-TÊTE DE LA FICHE & ACTIONS
    ========================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1.5">
                <a href="{{ route('students.index') }}" class="hover:text-primary transition">Étudiants</a>
                <span>/</span>
                <span class="text-gray-800 font-semibold">{{ $student->full_name }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold font-sans text-gray-900 flex items-center gap-3">
                <span>{{ $student->full_name }}</span>
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-primary/10 text-primary border border-primary/20">
                    {{ $student->student_number }}
                </span>
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <x-ui.button href="{{ route('students.index') }}" variant="outline">
                <x-lucide-arrow-left class="w-4 h-4 mr-1" />
                <span>Retour</span>
            </x-ui.button>

            <a
                href="{{ route('payments.create', ['student_id' => $student->id, 'type' => 'inflow']) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-md shadow-emerald-600/20"
            >
                <x-lucide-plus class="w-4 h-4" />
                <span>Encaisser un versement</span>
            </a>

            <x-ui.button href="{{ route('students.edit', $student) }}">
                <x-lucide-edit class="w-4 h-4 mr-1" />
                <span>Modifier</span>
            </x-ui.button>
        </div>
    </div>

    {{-- Messages flash --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3">
            <x-lucide-check-circle-2 class="w-5 h-5 text-emerald-600 shrink-0" />
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif


    {{-- =========================================================
         2. ÉTAT FINANCIER & SCOLARITÉ (SITUATION COMPTABLE)
    ========================================================== --}}
    @php
        $tuitionPrice = (float) ($student->course?->price ?? 0);
        $totalPaid = $student->total_paid;
        $remainingDue = $student->remaining_due;
        $paymentPercentage = $student->payment_percentage;
        $paymentStatus = $student->payment_status;
    @endphp

    <div class="rounded-3xl bg-white border border-gray-200/80 p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-gray-100">
            <div>
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <x-lucide-calculator class="w-5 h-5 text-primary" />
                    <span>Situation Financière & Frais de Scolarité</span>
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Formation : <strong class="text-gray-800">{{ $student->course?->title ?? 'Non assignée' }}</strong>
                </p>
            </div>

            <div class="flex items-center gap-3">
                @if($paymentStatus === 'paid')
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <x-lucide-check-circle class="w-4 h-4" />
                        <span>Scolarité Soldée (100%)</span>
                    </span>
                @elseif($paymentStatus === 'partial')
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                        <x-lucide-clock class="w-4 h-4" />
                        <span>Paiement Partiel ({{ $paymentPercentage }}%)</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                        <x-lucide-alert-circle class="w-4 h-4" />
                        <span>Non réglé (0%)</span>
                    </span>
                @endif
            </div>
        </div>

        {{-- Compteurs financiers --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6">
            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Coût Total Formation</span>
                <span class="text-2xl font-extrabold font-mono text-gray-900 mt-1 block">
                    {{ number_format($tuitionPrice, 0, ',', ' ') }} <span class="text-xs font-sans font-medium text-gray-500">FCFA</span>
                </span>
            </div>

            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100">
                <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider block">Montant Déjà Versé</span>
                <span class="text-2xl font-extrabold font-mono text-emerald-800 mt-1 block">
                    {{ number_format($totalPaid, 0, ',', ' ') }} <span class="text-xs font-sans font-medium text-emerald-600">FCFA</span>
                </span>
            </div>

            <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-100">
                <span class="text-xs font-semibold text-amber-800 uppercase tracking-wider block">Reste à Payer</span>
                <span class="text-2xl font-extrabold font-mono {{ $remainingDue > 0 ? 'text-amber-900' : 'text-emerald-700' }} mt-1 block">
                    {{ number_format($remainingDue, 0, ',', ' ') }} <span class="text-xs font-sans font-medium text-gray-500">FCFA</span>
                </span>
            </div>
        </div>

        {{-- Jauge de progression --}}
        <div class="mt-6 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between text-xs font-semibold text-gray-600 mb-2">
                <span>Progression du règlement</span>
                <span class="font-mono font-bold text-gray-900">{{ $paymentPercentage }}% réglé</span>
            </div>
            <div class="w-full h-3 rounded-full bg-gray-100 overflow-hidden">
                <div
                    class="h-full rounded-full transition-all duration-500 {{ $paymentStatus === 'paid' ? 'bg-emerald-500' : 'bg-primary' }}"
                    style="width: {{ $paymentPercentage }}%"
                ></div>
            </div>
        </div>
    </div>


    {{-- =========================================================
         3. INFORMATIONS PERSONNELLES & ACADÉMIQUES
    ========================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Photo & Statut --}}
        <x-ui.card title="Profil de l'étudiant">
            <div class="flex flex-col items-center text-center">
                @if($student->photo)
                    <img
                        src="{{ asset('storage/'.$student->photo) }}"
                        alt="{{ $student->full_name }}"
                        class="w-36 h-36 rounded-2xl object-cover border-4 border-white shadow-xl mb-4"
                    >
                @else
                    <div class="w-36 h-36 rounded-2xl bg-gradient-to-br from-[#310181] to-[#C15C03] text-white flex items-center justify-center text-4xl font-extrabold shadow-xl mb-4">
                        {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                    </div>
                @endif

                <h3 class="font-bold text-lg text-gray-900">{{ $student->full_name }}</h3>
                <p class="text-xs font-mono text-gray-500">{{ $student->student_number }}</p>

                <div class="mt-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $student->status === 'Inscrit' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $student->status ?: 'Inscrit' }}
                    </span>
                </div>

                @if($student->admission)
                    <div class="mt-4 pt-4 border-t border-gray-100 w-full text-left">
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block">Origine admission</span>
                        <a
                            href="{{ route('admissions.show', $student->admission) }}"
                            class="text-xs font-bold text-primary hover:underline mt-1 inline-flex items-center gap-1"
                        >
                            <x-lucide-file-text class="w-3.5 h-3.5" />
                            <span>Consulter le dossier d'admission</span>
                        </a>
                    </div>
                @endif
            </div>
        </x-ui.card>

        {{-- Coordonnées & État Civil --}}
        <div class="lg:col-span-2">
            <x-ui.card title="Informations Détaillées" subtitle="État civil, coordonnées et programme">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Formation assignée</span>
                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $student->course?->title ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Date d'inscription</span>
                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $student->registration_date ? \Carbon\Carbon::parse($student->registration_date)->format('d/m/Y') : ($student->created_at ? $student->created_at->format('d/m/Y') : '—') }}
                        </p>
                    </div>

                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Téléphone</span>
                        <p class="font-semibold text-gray-900 mt-1">
                            <a href="tel:{{ $student->phone }}" class="text-primary hover:underline font-mono">
                                {{ $student->phone }}
                            </a>
                        </p>
                    </div>

                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Email</span>
                        <p class="font-semibold text-gray-900 mt-1">
                            @if($student->email)
                                <a href="mailto:{{ $student->email }}" class="text-primary hover:underline">
                                    {{ $student->email }}
                                </a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Date & Lieu de naissance</span>
                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') : '—' }}
                            @if($student->birth_place) à {{ $student->birth_place }} @endif
                        </p>
                    </div>

                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Sexe / Nationalité</span>
                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $student->gender === 'M' ? 'Masculin' : ($student->gender === 'F' ? 'Féminin' : '—') }}
                            @if($student->nationality) • {{ $student->nationality }} @endif
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Adresse de résidence</span>
                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $student->address ?: 'Non renseignée' }}
                        </p>
                    </div>

                    @if($student->notes)
                        <div class="sm:col-span-2 pt-2 border-t border-gray-100">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Observations / Remarques</span>
                            <p class="text-gray-700 mt-1 leading-relaxed text-xs">
                                {{ $student->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </x-ui.card>
        </div>

    </div>


    {{-- =========================================================
         4. HISTORIQUE DES PAIEMENTS & REÇUS
    ========================================================== --}}
    <x-ui.card
        title="Historique des Règlements & Reçus de Caisse"
        subtitle="Tous les versements et encaissements liés à cet étudiant"
    >
        @if($student->payments->count())
            <div class="overflow-x-auto -mx-6">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/75 border-b border-gray-200 text-xs text-gray-600 uppercase font-semibold">
                        <tr>
                            <th class="text-left px-6 py-3">N° Reçu</th>
                            <th class="text-left px-4 py-3">Date</th>
                            <th class="text-left px-4 py-3">Catégorie / Motif</th>
                            <th class="text-left px-4 py-3">Moyen de paiement</th>
                            <th class="text-right px-4 py-3">Montant</th>
                            <th class="text-center px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($student->payments as $payment)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-3.5 font-mono font-bold text-gray-900 text-xs">
                                    {{ $payment->receipt_number ?? 'REC-' . $payment->id }}
                                </td>

                                <td class="px-4 py-3.5 text-xs text-gray-600 font-mono">
                                    {{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '—' }}
                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="font-semibold text-gray-900 text-xs">{{ $payment->category_label }}</span>
                                    @if($payment->title)
                                        <span class="block text-[11px] text-gray-500">{{ $payment->title }}</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5 text-xs text-gray-700">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-gray-100 font-medium">
                                        {{ $payment->payment_method_label }}
                                    </span>
                                    @if($payment->reference)
                                        <span class="block text-[10px] text-gray-400 font-mono mt-0.5">Réf: {{ $payment->reference }}</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5 text-right font-mono font-bold text-emerald-700">
                                    +{{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                </td>

                                <td class="px-6 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a
                                            href="{{ route('payments.receipt', $payment) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition"
                                            title="Imprimer le reçu officiel"
                                        >
                                            <x-lucide-printer class="w-3.5 h-3.5" />
                                            <span>Reçu</span>
                                        </a>

                                        <a
                                            href="{{ route('payments.show', $payment) }}"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition"
                                            title="Détails de l'écriture"
                                        >
                                            <x-lucide-eye class="w-3.5 h-3.5" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-gray-400">
                <x-lucide-credit-card class="w-10 h-10 mx-auto mb-3 text-gray-300" />
                <p class="text-sm font-medium text-gray-600">Aucun versement enregistré pour cet étudiant.</p>
                <p class="text-xs text-gray-400 mt-1">Cliquez ci-dessous pour enregistrer le premier versement de scolarité ou frais d'inscription.</p>
                <div class="mt-4">
                    <a
                        href="{{ route('payments.create', ['student_id' => $student->id, 'type' => 'inflow']) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm"
                    >
                        <x-lucide-plus class="w-4 h-4" />
                        <span>Enregistrer le premier versement</span>
                    </a>
                </div>
            </div>
        @endif
    </x-ui.card>

</div>

@endsection