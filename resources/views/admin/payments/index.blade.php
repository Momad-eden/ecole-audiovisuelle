@extends('layouts.admin')

@section('content')

<div class="space-y-8">

    {{-- En-tête de Caisse --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <x-lucide-landmark class="w-8 h-8 text-primary" />
                Caisse & Trésorerie
            </h1>
            <p class="text-gray-500 mt-1">
                Journal des encaissements, décaissements, gestion des flux et suivi en direct du solde de l'école.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a
                href="{{ route('accounting.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm transition"
            >
                <x-lucide-calculator class="w-4 h-4 text-gray-600" />
                <span>Grand Livre Comptable</span>
            </a>

            <a
                href="{{ route('payments.create', ['type' => 'outflow']) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold text-sm transition shadow-sm"
            >
                <x-lucide-arrow-up-right class="w-4 h-4" />
                <span>- Décaissement (Dépense)</span>
            </a>

            <a
                href="{{ route('payments.create', ['type' => 'inflow']) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition shadow-sm"
            >
                <x-lucide-plus class="w-4 h-4" />
                <span>+ Encaissement (Recette)</span>
            </a>
        </div>

    </div>


    {{-- Messages flash --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <x-lucide-check-circle-2 class="w-5 h-5 text-emerald-600 shrink-0" />
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        </div>
    @endif


    {{-- Statistiques Clés de Caisse --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Solde Actuel --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Solde Réel en Caisse</p>
                    <h2 class="mt-2 text-2xl lg:text-3xl font-extrabold {{ $currentBalance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ number_format($currentBalance, 0, ',', ' ') }} <span class="text-sm font-normal text-gray-500">FCFA</span>
                    </h2>
                    <p class="mt-2 text-xs text-gray-400">Entrées globales − Sorties globales</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $currentBalance >= 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                    <x-lucide-landmark class="w-6 h-6" />
                </div>
            </div>
        </div>

        {{-- Recettes Globales --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Encaissements</p>
                    <h2 class="mt-2 text-2xl lg:text-3xl font-extrabold text-gray-900">
                        {{ number_format($totalInflows, 0, ',', ' ') }} <span class="text-sm font-normal text-gray-500">FCFA</span>
                    </h2>
                    <p class="mt-2 text-xs text-emerald-600 font-medium flex items-center gap-1">
                        <x-lucide-arrow-down-left class="w-3.5 h-3.5" />
                        Aujourd'hui : +{{ number_format($todayInflows, 0, ',', ' ') }} F
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <x-lucide-arrow-down-left class="w-6 h-6" />
                </div>
            </div>
        </div>

        {{-- Dépenses Globales --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Décaissements</p>
                    <h2 class="mt-2 text-2xl lg:text-3xl font-extrabold text-gray-900">
                        {{ number_format($totalOutflows, 0, ',', ' ') }} <span class="text-sm font-normal text-gray-500">FCFA</span>
                    </h2>
                    <p class="mt-2 text-xs text-red-600 font-medium flex items-center gap-1">
                        <x-lucide-arrow-up-right class="w-3.5 h-3.5" />
                        Aujourd'hui : -{{ number_format($todayOutflows, 0, ',', ' ') }} F
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                    <x-lucide-arrow-up-right class="w-6 h-6" />
                </div>
            </div>
        </div>

        {{-- Flux Net du Mois --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Flux Net du Mois</p>
                    <h2 class="mt-2 text-2xl lg:text-3xl font-extrabold text-primary">
                        {{ ($monthNet >= 0 ? '+' : '') . number_format($monthNet, 0, ',', ' ') }} <span class="text-sm font-normal text-gray-500">FCFA</span>
                    </h2>
                    <p class="mt-2 text-xs text-gray-500">
                        Entrées : {{ number_format($monthInflows, 0, ',', ' ') }} F | Sorties : {{ number_format($monthOutflows, 0, ',', ' ') }} F
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-primary">
                    <x-lucide-calendar-days class="w-6 h-6" />
                </div>
            </div>
        </div>

    </div>


    {{-- Filtres et Outils de Recherche --}}
    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <form method="GET" action="{{ route('payments.index') }}" class="space-y-4">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

                {{-- Recherche textuelle --}}
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Recherche</label>
                    <div class="relative">
                        <x-lucide-search class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="N° reçu, réf, libellé, nom étudiant..."
                            class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                        >
                    </div>
                </div>

                {{-- Type de flux --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Type d'opération</label>
                    <select
                        name="type"
                        class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                    >
                        <option value="">Toutes les opérations</option>
                        <option value="inflow" {{ request('type') === 'inflow' ? 'selected' : '' }}>🟢 Encaissements (Recettes)</option>
                        <option value="outflow" {{ request('type') === 'outflow' ? 'selected' : '' }}>🔴 Décaissements (Dépenses)</option>
                    </select>
                </div>

                {{-- Catégorie --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Catégorie</label>
                    <select
                        name="category"
                        class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                    >
                        <option value="all">Toutes catégories</option>
                        <optgroup label="Recettes">
                            @foreach(\App\Enums\TransactionCategory::inflowOptions() as $k => $label)
                                <option value="{{ $k }}" {{ request('category') === $k ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Dépenses">
                            @foreach(\App\Enums\TransactionCategory::outflowOptions() as $k => $label)
                                <option value="{{ $k }}" {{ request('category') === $k ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                {{-- Mode de paiement --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Moyen de paiement</label>
                    <select
                        name="payment_method"
                        class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                    >
                        <option value="all">Tous les modes</option>
                        @foreach(\App\Enums\PaymentMethod::options() as $k => $label)
                            <option value="{{ $k }}" {{ request('payment_method') === $k ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-gray-100">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-medium text-gray-500">Période :</span>
                    <input
                        type="date"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-primary outline-none"
                    >
                    <span class="text-xs text-gray-400">à</span>
                    <input
                        type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 focus:border-primary outline-none"
                    >
                </div>

                <div class="flex items-center gap-2">
                    @if(request()->hasAny(['search', 'type', 'category', 'payment_method', 'date_from', 'date_to']))
                        <a
                            href="{{ route('payments.index') }}"
                            class="px-3.5 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold transition"
                        >
                            Réinitialiser
                        </a>
                    @endif
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-4 py-1.5 rounded-lg bg-primary hover:bg-primary/90 text-white text-xs font-bold transition shadow-sm"
                    >
                        <x-lucide-filter class="w-3.5 h-3.5" />
                        <span>Filtrer les écritures</span>
                    </button>
                </div>
            </div>

        </form>
    </div>


    {{-- Journal des Écritures de Caisse --}}
    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">

        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Journal des Écritures</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ $payments->total() }} transaction(s) enregistrée(s)</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b bg-gray-50/80 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Date</th>
                        <th class="px-6 py-3.5">N° Reçu / Réf</th>
                        <th class="px-6 py-3.5">Opération & Catégorie</th>
                        <th class="px-6 py-3.5">Bénéficiaire / Étudiant</th>
                        <th class="px-6 py-3.5">Règlement</th>
                        <th class="px-6 py-3.5 text-right">Montant</th>
                        <th class="px-6 py-3.5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50/60 transition group">

                            {{-- Date --}}
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                <span class="font-semibold text-gray-900 block">{{ $payment->payment_date->format('d/m/Y') }}</span>
                                <span class="text-[11px] text-gray-400">{{ $payment->created_at->format('H:i') }}</span>
                            </td>

                            {{-- N° Reçu / Réf --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a
                                    href="{{ route('payments.show', $payment) }}"
                                    class="font-mono text-xs font-bold text-gray-900 hover:text-primary transition"
                                >
                                    {{ $payment->receipt_number ?? 'REC-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}
                                </a>
                                @if($payment->reference)
                                    <span class="block text-[11px] text-gray-400 font-mono mt-0.5">Réf: {{ $payment->reference }}</span>
                                @endif
                            </td>

                            {{-- Type & Catégorie --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($payment->isInflow())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700">
                                            <x-lucide-arrow-down-left class="w-3 h-3" />
                                            Recette
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-700">
                                            <x-lucide-arrow-up-right class="w-3 h-3" />
                                            Dépense
                                        </span>
                                    @endif

                                    <span class="text-xs text-gray-500 font-medium">
                                        {{ $payment->category_label }}
                                    </span>
                                </div>

                                @if($payment->title)
                                    <p class="text-xs text-gray-700 font-medium mt-1 truncate max-w-xs">
                                        {{ $payment->title }}
                                    </p>
                                @endif
                            </td>

                            {{-- Bénéficiaire / Étudiant --}}
                            <td class="px-6 py-4 text-xs">
                                @if($payment->student)
                                    <a
                                        href="{{ route('students.show', $payment->student) }}"
                                        class="font-semibold text-gray-900 hover:text-primary transition block"
                                    >
                                        {{ $payment->student->full_name }}
                                    </a>
                                    <span class="text-[11px] text-gray-400">
                                        {{ $payment->student->course?->title ?? 'Filière non assignée' }}
                                    </span>
                                @else
                                    <span class="text-gray-600 font-medium italic">
                                        {{ $payment->title ?? 'Opération interne' }}
                                    </span>
                                @endif
                            </td>

                            {{-- Mode de règlement --}}
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 font-medium text-gray-700 text-xs">
                                    {{ $payment->payment_method_label }}
                                </span>
                            </td>

                            {{-- Montant --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-bold font-mono {{ $payment->isInflow() ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $payment->isInflow() ? '+' : '-' }}{{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- Détails --}}
                                    <a
                                        href="{{ route('payments.show', $payment) }}"
                                        class="p-1.5 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition"
                                        title="Voir les détails"
                                    >
                                        <x-lucide-eye class="w-4 h-4" />
                                    </a>

                                    {{-- Reçu officiel imprimable --}}
                                    <a
                                        href="{{ route('payments.receipt', $payment) }}"
                                        target="_blank"
                                        class="p-1.5 rounded-lg text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 transition"
                                        title="Imprimer le reçu officiel"
                                    >
                                        <x-lucide-printer class="w-4 h-4" />
                                    </a>

                                    {{-- Éditer --}}
                                    <a
                                        href="{{ route('payments.edit', $payment) }}"
                                        class="p-1.5 rounded-lg text-amber-600 hover:text-amber-700 hover:bg-amber-50 transition"
                                        title="Modifier"
                                    >
                                        <x-lucide-pencil class="w-4 h-4" />
                                    </a>

                                    {{-- Supprimer --}}
                                    <form
                                        action="{{ route('payments.destroy', $payment) }}"
                                        method="POST"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette écriture de caisse ?');"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="p-1.5 rounded-lg text-red-600 hover:text-red-700 hover:bg-red-50 transition"
                                            title="Supprimer"
                                        >
                                            <x-lucide-trash-2 class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <x-lucide-inbox class="mx-auto h-12 w-12 text-gray-300 mb-3" />
                                <p class="text-base font-semibold text-gray-700">Aucune opération enregistrée</p>
                                <p class="text-xs text-gray-400 mt-1">Commencez par enregistrer un encaissement ou un décaissement.</p>
                                <div class="mt-5 flex items-center justify-center gap-3">
                                    <a
                                        href="{{ route('payments.create', ['type' => 'inflow']) }}"
                                        class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold text-xs shadow-sm hover:bg-emerald-700"
                                    >
                                        + Nouvel Encaissement
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($payments->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
        @endif

    </div>


    {{-- Répartition par Moyen de Règlement --}}
    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
            <x-lucide-wallet class="w-5 h-5 text-primary" />
            Répartition des Encaissements par Moyen de Règlement
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($paymentMethodsStats as $methodKey => $data)
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <p class="text-xs text-gray-500 font-medium truncate">{{ $data['label'] }}</p>
                    <p class="text-base font-bold font-mono text-gray-900 mt-1">
                        {{ number_format($data['inflow'], 0, ',', ' ') }} <span class="text-[10px] text-gray-400 font-normal">F</span>
                    </p>
                    <p class="text-[11px] text-red-500 font-mono mt-0.5">
                        Sorties: {{ number_format($data['outflow'], 0, ',', ' ') }} F
                    </p>
                </div>
            @endforeach
        </div>
    </div>

</div>

@endsection