@extends('layouts.admin')

@section('title', 'Comptabilité & Grand Livre — EMSI Administration')

@section('header_title', 'Comptabilité & Bilan Financier')
@section('header_subtitle', 'Grand livre des écritures, ventilation des flux et suivi du recouvrement des scolarités.')

@section('content')

<div class="space-y-8">

    {{-- =========================================================
         1. EN-TÊTE & ACTIONS PRINCIPALES
    ========================================================== --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <x-lucide-calculator class="w-8 h-8 text-primary" />
                <span>Comptabilité & Bilan Financier</span>
            </h1>
            <p class="text-gray-500 mt-1">
                Grand livre général des écritures, journal chronologique avec solde progressif et balance des scolarités.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a
                href="{{ route('payments.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs transition"
            >
                <x-lucide-landmark class="w-4 h-4" />
                <span>Journal de Caisse</span>
            </a>

            <a
                href="{{ route('accounting.export', request()->all()) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm"
                title="Télécharger les écritures au format CSV pour Excel"
            >
                <x-lucide-download class="w-4 h-4" />
                <span>Exporter CSV (Excel)</span>
            </a>

            <button
                onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm"
            >
                <x-lucide-printer class="w-4 h-4" />
                <span>Imprimer le bilan</span>
            </button>
        </div>
    </div>


    {{-- =========================================================
         2. SÉLECTEUR DE PÉRIODE & FILTRES
    ========================================================== --}}
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm no-print">
        <form method="GET" action="{{ route('accounting.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1.5">Exercice (Année)</label>
                    <select
                        name="year"
                        class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 focus:border-primary outline-none"
                    >
                        <option value="all">Toutes les années</option>
                        @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                            <option value="{{ $y }}" @selected($year == $y)>Année {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1.5">Mois</label>
                    <select
                        name="month"
                        class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 focus:border-primary outline-none"
                    >
                        <option value="all">Toute l'année</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected($month == $m)>
                                {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1.5">Date Début (optionnelle)</label>
                    <input
                        type="date"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="w-full px-3 py-1.5 text-xs rounded-xl border border-gray-200 focus:border-primary outline-none"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1.5">Date Fin (optionnelle)</label>
                    <input
                        type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="w-full px-3 py-1.5 text-xs rounded-xl border border-gray-200 focus:border-primary outline-none"
                    >
                </div>

            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 font-medium">Raccourcis :</span>
                    <a
                        href="{{ route('accounting.index', ['year' => date('Y'), 'month' => date('m')]) }}"
                        class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition"
                    >
                        Ce mois
                    </a>
                    <a
                        href="{{ route('accounting.index', ['year' => date('Y'), 'month' => 'all']) }}"
                        class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition"
                    >
                        Cette année ({{ date('Y') }})
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    @if(request()->hasAny(['year', 'month', 'date_from', 'date_to', 'student_search', 'student_course_id', 'student_status']))
                        <a
                            href="{{ route('accounting.index') }}"
                            class="px-3.5 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold transition"
                        >
                            Réinitialiser
                        </a>
                    @endif

                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-bold transition shadow-sm"
                    >
                        <x-lucide-filter class="w-3.5 h-3.5" />
                        <span>Actualiser le Grand Livre</span>
                    </button>
                </div>
            </div>
        </form>
    </div>


    {{-- =========================================================
         3. KPI FINANCIERS DE LA PÉRIODE SÉLECTIONNÉE
    ========================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        {{-- Entrées période --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Recettes Encaissées</p>
            <h2 class="mt-2 text-2xl lg:text-3xl font-extrabold text-emerald-600 font-mono">
                +{{ number_format($periodInflows, 0, ',', ' ') }} <span class="text-xs font-normal text-gray-500">FCFA</span>
            </h2>
            <p class="mt-2 text-xs text-gray-400">Encaissements sur la période sélectionnée</p>
        </div>

        {{-- Sorties période --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Dépenses Décaissées</p>
            <h2 class="mt-2 text-2xl lg:text-3xl font-extrabold text-red-600 font-mono">
                -{{ number_format($periodOutflows, 0, ',', ' ') }} <span class="text-xs font-normal text-gray-500">FCFA</span>
            </h2>
            <p class="mt-2 text-xs text-gray-400">Décaissements sur la période sélectionnée</p>
        </div>

        {{-- Résultat Net --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Résultat Net d'Exploitation</p>
            <h2 class="mt-2 text-2xl lg:text-3xl font-extrabold font-mono {{ $periodNet >= 0 ? 'text-primary' : 'text-red-600' }}">
                {{ ($periodNet >= 0 ? '+' : '') . number_format($periodNet, 0, ',', ' ') }} <span class="text-xs font-normal text-gray-500">FCFA</span>
            </h2>
            <p class="mt-2 text-xs text-gray-400">Marge brute de trésorerie sur la sélection</p>
        </div>

        {{-- Taux de Recouvrement --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Taux de Recouvrement Scolarités</p>
            <h2 class="mt-2 text-2xl lg:text-3xl font-extrabold text-gray-900 font-mono">
                {{ $recoveryRate }}%
            </h2>
            <div class="w-full bg-gray-100 rounded-full h-2 mt-3 overflow-hidden">
                <div class="bg-primary h-2 rounded-full" style="width: {{ min(100, $recoveryRate) }}%"></div>
            </div>
        </div>

    </div>


    {{-- =========================================================
         4. VENTILATION ANALYTIQUE : RECETTES VS DÉPENSES
    ========================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Ventilation Recettes --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <x-lucide-arrow-down-left class="w-5 h-5 text-emerald-600" />
                    <span>Ventilation des Recettes</span>
                </h3>
                <span class="font-mono font-bold text-sm text-emerald-600">
                    {{ number_format($periodInflows, 0, ',', ' ') }} FCFA
                </span>
            </div>

            <div class="space-y-3">
                @foreach($inflowsByCategory as $key => $item)
                    @php
                        $pct = $periodInflows > 0 ? round(($item['total'] / $periodInflows) * 100, 1) : 0;
                    @endphp
                    <div class="p-3 rounded-xl bg-gray-50 text-xs">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-medium text-gray-700">{{ $item['label'] }}</span>
                            <span class="font-mono font-bold text-gray-900">
                                {{ number_format($item['total'], 0, ',', ' ') }} FCFA
                                <span class="text-gray-400 font-normal">({{ $pct }}%)</span>
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Ventilation Dépenses --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <x-lucide-arrow-up-right class="w-5 h-5 text-red-600" />
                    <span>Ventilation des Dépenses</span>
                </h3>
                <span class="font-mono font-bold text-sm text-red-600">
                    {{ number_format($periodOutflows, 0, ',', ' ') }} FCFA
                </span>
            </div>

            <div class="space-y-3">
                @foreach($outflowsByCategory as $key => $item)
                    @php
                        $pct = $periodOutflows > 0 ? round(($item['total'] / $periodOutflows) * 100, 1) : 0;
                    @endphp
                    <div class="p-3 rounded-xl bg-gray-50 text-xs">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-medium text-gray-700">{{ $item['label'] }}</span>
                            <span class="font-mono font-bold text-gray-900">
                                {{ number_format($item['total'], 0, ',', ' ') }} FCFA
                                <span class="text-gray-400 font-normal">({{ $pct }}%)</span>
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>


    {{-- =========================================================
         5. GRAND LIVRE DES ÉCRITURES COMPTABLES (SOLDE PROGRESSIF)
    ========================================================== --}}
    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <x-lucide-book-open class="w-5 h-5 text-primary" />
                    <span>Grand Livre Chronologique des Écritures</span>
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $transactions->count() }} écriture(s) enregistrée(s) avec calcul du solde progressif
                </p>
            </div>

            <a
                href="{{ route('accounting.export', request()->all()) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs transition"
            >
                <x-lucide-download class="w-3.5 h-3.5" />
                <span>Export CSV</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b bg-gray-50/80 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5">Date</th>
                        <th class="px-5 py-3.5">N° Pièce / Réf</th>
                        <th class="px-5 py-3.5">Libellé & Catégorie</th>
                        <th class="px-5 py-3.5">Bénéficiaire / Tiers</th>
                        <th class="px-5 py-3.5">Règlement</th>
                        <th class="px-5 py-3.5 text-right">Recette (Crédit)</th>
                        <th class="px-5 py-3.5 text-right">Dépense (Débit)</th>
                        <th class="px-5 py-3.5 text-right">Solde Progressif</th>
                        <th class="px-5 py-3.5 text-center no-print">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50/60 transition">
                            {{-- Date --}}
                            <td class="px-5 py-3.5 whitespace-nowrap font-mono text-gray-600">
                                {{ $tx->payment_date ? $tx->payment_date->format('d/m/Y') : $tx->created_at->format('d/m/Y') }}
                            </td>

                            {{-- N° Reçu --}}
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <a
                                    href="{{ route('payments.show', $tx) }}"
                                    class="font-mono font-bold text-gray-900 hover:text-primary transition"
                                >
                                    {{ $tx->receipt_number ?? 'REC-' . $tx->id }}
                                </a>
                                @if($tx->reference)
                                    <span class="block text-[10px] text-gray-400 font-mono">Réf: {{ $tx->reference }}</span>
                                @endif
                            </td>

                            {{-- Libellé & Catégorie --}}
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-gray-900 block truncate max-w-xs">
                                    {{ $tx->title ?: $tx->category_label }}
                                </span>
                                <span class="text-[11px] text-gray-400">
                                    {{ $tx->category_label }}
                                </span>
                            </td>

                            {{-- Tiers / Étudiant --}}
                            <td class="px-5 py-3.5">
                                @if($tx->student)
                                    <a
                                        href="{{ route('students.show', $tx->student) }}"
                                        class="font-semibold text-gray-900 hover:text-primary transition block"
                                    >
                                        {{ $tx->student->full_name }}
                                    </a>
                                    <span class="text-[10px] text-gray-400">{{ $tx->student->course?->title ?? 'Formation' }}</span>
                                @else
                                    <span class="text-gray-500 italic">Opération générale</span>
                                @endif
                            </td>

                            {{-- Règlement --}}
                            <td class="px-5 py-3.5 whitespace-nowrap text-gray-600">
                                {{ $tx->payment_method_label }}
                            </td>

                            {{-- Recette --}}
                            <td class="px-5 py-3.5 font-mono text-right font-bold text-emerald-600">
                                {{ $tx->isInflow() ? number_format($tx->amount, 0, ',', ' ') . ' F' : '—' }}
                            </td>

                            {{-- Dépense --}}
                            <td class="px-5 py-3.5 font-mono text-right font-bold text-red-600">
                                {{ $tx->isOutflow() ? number_format($tx->amount, 0, ',', ' ') . ' F' : '—' }}
                            </td>

                            {{-- Solde Progressif --}}
                            <td class="px-5 py-3.5 font-mono text-right font-extrabold {{ $tx->running_balance >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                {{ number_format($tx->running_balance, 0, ',', ' ') }} F
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3.5 text-center whitespace-nowrap no-print">
                                <div class="inline-flex items-center gap-1">
                                    <a
                                        href="{{ route('payments.show', $tx) }}"
                                        class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                                        title="Voir les détails"
                                    >
                                        <x-lucide-eye class="w-3.5 h-3.5" />
                                    </a>
                                    <a
                                        href="{{ route('payments.receipt', $tx) }}"
                                        target="_blank"
                                        class="p-1 rounded-lg text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 transition"
                                        title="Imprimer le reçu"
                                    >
                                        <x-lucide-printer class="w-3.5 h-3.5" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-8 text-center text-gray-400">
                                Aucune transaction enregistrée pour la période sélectionnée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- =========================================================
         6. TABLEAU DE SUIVI DU RECOUVREMENT DES SCOLARITÉS
    ========================================================== --}}
    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <x-lucide-users class="w-5 h-5 text-primary" />
                        <span>Balance Analytique des Scolarités & Créances</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Attendu total : <strong class="text-gray-900 font-mono">{{ number_format($totalExpected, 0, ',', ' ') }} FCFA</strong> | 
                        Encaissé : <strong class="text-emerald-600 font-mono">{{ number_format($totalCollected, 0, ',', ' ') }} FCFA</strong> | 
                        Reste à recouvrer : <strong class="text-amber-600 font-mono">{{ number_format($totalUnpaid, 0, ',', ' ') }} FCFA</strong>
                    </p>
                </div>
            </div>

            {{-- Filtres spécifiques aux étudiants --}}
            <form method="GET" action="{{ route('accounting.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-gray-100 no-print">
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="month" value="{{ $month }}">

                <div>
                    <input
                        type="text"
                        name="student_search"
                        value="{{ request('student_search') }}"
                        placeholder="Rechercher étudiant (nom, matricule)..."
                        class="w-full px-3 py-1.5 text-xs rounded-xl border border-gray-200 focus:border-primary outline-none"
                    >
                </div>

                <div>
                    <select
                        name="student_course_id"
                        class="w-full px-3 py-1.5 text-xs rounded-xl border border-gray-200 focus:border-primary outline-none"
                    >
                        <option value="all">Toutes les formations</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" @selected(request('student_course_id') == $c->id)>{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <select
                        name="student_status"
                        class="w-full px-3 py-1.5 text-xs rounded-xl border border-gray-200 focus:border-primary outline-none"
                    >
                        <option value="">Tous les règlements</option>
                        <option value="paid" @selected(request('student_status') === 'paid')>🟢 Soldé (100%)</option>
                        <option value="partial" @selected(request('student_status') === 'partial')>🟠 Paiement partiel</option>
                        <option value="unpaid" @selected(request('student_status') === 'unpaid')>🔴 Non réglé (0%)</option>
                    </select>

                    <button
                        type="submit"
                        class="px-3 py-1.5 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold transition shrink-0"
                    >
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b bg-gray-50/80 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Étudiant & Matricule</th>
                        <th class="px-6 py-3.5">Filière</th>
                        <th class="px-6 py-3.5 text-right">Coût Formation</th>
                        <th class="px-6 py-3.5 text-right">Total Réglé</th>
                        <th class="px-6 py-3.5 text-right">Reste Dû</th>
                        <th class="px-6 py-3.5 text-center">Progression</th>
                        <th class="px-6 py-3.5 text-center">Statut</th>
                        <th class="px-6 py-3.5 text-center no-print">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($filteredStudents as $row)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('students.show', $row['student']) }}" class="font-bold text-gray-900 hover:text-primary transition block">
                                    {{ $row['student']->full_name }}
                                </a>
                                <span class="font-mono text-gray-400 text-[11px]">{{ $row['student']->matricule ?? 'Sans matricule' }}</span>
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ $row['course'] }}
                            </td>

                            <td class="px-6 py-4 font-mono text-right text-gray-900 font-medium">
                                {{ number_format($row['price'], 0, ',', ' ') }} F
                            </td>

                            <td class="px-6 py-4 font-mono text-right font-bold text-emerald-600">
                                {{ number_format($row['total_paid'], 0, ',', ' ') }} F
                            </td>

                            <td class="px-6 py-4 font-mono text-right font-extrabold {{ $row['remaining'] > 0 ? 'text-amber-600' : 'text-gray-400' }}">
                                {{ number_format($row['remaining'], 0, ',', ' ') }} F
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="font-mono font-semibold text-gray-700 text-xs">{{ $row['rate'] }}%</span>
                                <div class="w-16 mx-auto bg-gray-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                    <div class="h-1.5 rounded-full {{ $row['rate'] >= 100 ? 'bg-emerald-500' : ($row['rate'] > 0 ? 'bg-amber-500' : 'bg-red-400') }}" style="width: {{ min(100, $row['rate']) }}%"></div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($row['status'] === 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                        Soldé
                                    </span>
                                @elseif($row['status'] === 'partial')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800">
                                        Partiel
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800">
                                        Non réglé
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center no-print">
                                <a
                                    href="{{ route('payments.create', ['type' => 'inflow', 'student_id' => $row['student']->id]) }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[11px] transition"
                                >
                                    <x-lucide-plus class="w-3 h-3" />
                                    <span>Encaisser</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                Aucun dossier étudiant correspondant aux critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- =========================================================
         7. BLOC SIGNATURES OFFICIELLES (VISIBLE À L'IMPRESSION)
    ========================================================== --}}
    <div class="hidden print:grid grid-cols-2 gap-12 pt-12 mt-12 border-t-2 border-gray-900 text-xs">
        <div>
            <p class="font-bold text-gray-900 uppercase">Le Responsable Administratif & Financier</p>
            <p class="text-gray-500 mt-0.5">Date et signature :</p>
            <div class="h-20 border-b border-dashed border-gray-400 mt-2"></div>
        </div>

        <div class="text-right">
            <p class="font-bold text-gray-900 uppercase">La Direction Générale — EMSI</p>
            <p class="text-gray-500 mt-0.5">Approbation et visa :</p>
            <div class="h-20 border-b border-dashed border-gray-400 mt-2"></div>
        </div>
    </div>

</div>

@endsection

