@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto space-y-8">

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-gray-900 font-mono">
                    {{ $payment->receipt_number ?? 'Écriture #' . $payment->id }}
                </h1>

                @if($payment->isInflow())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                        <x-lucide-arrow-down-left class="w-3.5 h-3.5 text-emerald-600" />
                        Encaissement
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-red-100 text-red-800">
                        <x-lucide-arrow-up-right class="w-3.5 h-3.5 text-red-600" />
                        Décaissement
                    </span>
                @endif
            </div>

            <p class="text-gray-500 mt-1">
                Enregistrée le {{ $payment->payment_date->format('d/m/Y') }}
                @if($payment->creator)
                    par <span class="font-medium text-gray-700">{{ $payment->creator->name }}</span>
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Bouton Imprimer Reçu --}}
            <a
                href="{{ route('payments.receipt', $payment) }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition shadow-sm"
            >
                <x-lucide-printer class="w-4 h-4" />
                <span>Imprimer le reçu</span>
            </a>

            <a
                href="{{ route('payments.edit', $payment) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm transition"
            >
                <x-lucide-pencil class="w-4 h-4" />
                <span>Modifier</span>
            </a>

            <a
                href="{{ route('payments.index') }}"
                class="px-4 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold transition"
            >
                Retour au journal
            </a>
        </div>
    </div>


    {{-- Message de succès --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <x-lucide-check-circle-2 class="w-5 h-5 text-emerald-600 shrink-0" />
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        </div>
    @endif


    {{-- Carte Principale de la Transaction --}}
    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">

        {{-- Bandeau Montant & Type --}}
        <div class="p-6 sm:p-8 bg-gradient-to-r {{ $payment->isInflow() ? 'from-emerald-50 to-emerald-100/40 border-b border-emerald-100' : 'from-red-50 to-red-100/40 border-b border-red-100' }}">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider {{ $payment->isInflow() ? 'text-emerald-700' : 'text-red-700' }}">
                        Montant de l'opération
                    </p>
                    <h2 class="text-3xl sm:text-4xl font-extrabold font-mono {{ $payment->isInflow() ? 'text-emerald-700' : 'text-red-700' }} mt-1">
                        {{ $payment->isInflow() ? '+' : '-' }}{{ number_format($payment->amount, 0, ',', ' ') }} <span class="text-lg font-bold">FCFA</span>
                    </h2>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-3.5 py-1.5 rounded-xl bg-white/80 backdrop-blur-sm border border-gray-200 text-xs font-bold text-gray-700 shadow-sm">
                        {{ $payment->category_label }}
                    </span>
                    <span class="px-3.5 py-1.5 rounded-xl bg-white/80 backdrop-blur-sm border border-gray-200 text-xs font-bold text-gray-700 shadow-sm">
                        {{ $payment->payment_method_label }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Détails complets --}}
        <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Libellé / Motif --}}
            <div class="md:col-span-2">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Libellé / Intitulé</p>
                <p class="text-base font-bold text-gray-900 mt-1">
                    {{ $payment->title ?: 'Sans intitulé' }}
                </p>
            </div>

            {{-- Date d'opération --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Date d'opération</p>
                <p class="text-sm font-semibold text-gray-900 mt-1 flex items-center gap-2">
                    <x-lucide-calendar class="w-4 h-4 text-gray-400" />
                    {{ $payment->payment_date->format('d/m/Y') }}
                </p>
            </div>

            {{-- Référence externe --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Référence externe / N° Transaction</p>
                <p class="text-sm font-mono font-semibold text-gray-900 mt-1">
                    {{ $payment->reference ?: '— Aucune' }}
                </p>
            </div>

            {{-- Date d'enregistrement dans le système --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Saisie dans le système</p>
                <p class="text-xs text-gray-600 mt-1">
                    {{ $payment->created_at->format('d/m/Y à H:i:s') }}
                </p>
            </div>

            {{-- Agent de saisie --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Agent de caisse / Auteur</p>
                <p class="text-xs text-gray-600 mt-1">
                    {{ $payment->creator?->name ?? 'Système' }} ({{ $payment->creator?->email ?? '—' }})
                </p>
            </div>

            {{-- Notes & Remarques --}}
            @if($payment->notes)
                <div class="md:col-span-2 pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Notes & Observations</p>
                    <p class="text-sm text-gray-700 mt-1 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100">
                        {{ $payment->notes }}
                    </p>
                </div>
            @endif

        </div>

    </div>


    {{-- Situation de l'Étudiant (si lié) --}}
    @if($payment->student && $studentStats)
        <div class="rounded-2xl border border-gray-100 bg-white p-6 sm:p-8 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <x-lucide-user-check class="w-5 h-5 text-primary" />
                    Situation de l'étudiant
                </span>
                <a
                    href="{{ route('students.show', $payment->student) }}"
                    class="text-xs font-semibold text-primary hover:underline"
                >
                    Voir le dossier étudiant →
                </a>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Nom & Prénom</p>
                    <p class="font-bold text-base text-gray-900 mt-1">{{ $payment->student->full_name }}</p>
                    <p class="text-xs text-gray-500 font-mono">{{ $payment->student->matricule ?? 'Sans matricule' }}</p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Formation suivie</p>
                    <p class="font-bold text-base text-gray-900 mt-1">{{ $payment->student->course?->title ?? 'Non assignée' }}</p>
                    <p class="text-xs text-gray-500">Coût : {{ number_format($studentStats['course_price'], 0, ',', ' ') }} FCFA</p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Reste à payer</p>
                    <p class="font-extrabold text-xl font-mono mt-1 {{ $studentStats['remaining_due'] > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                        {{ number_format($studentStats['remaining_due'], 0, ',', ' ') }} FCFA
                    </p>
                    <p class="text-xs text-gray-500">Total versé : {{ number_format($studentStats['total_paid'], 0, ',', ' ') }} FCFA</p>
                </div>
            </div>

            {{-- Historique des versements de cet étudiant --}}
            <div class="border-t border-gray-100 pt-6">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Tous les versements de cet étudiant</p>
                <div class="space-y-2">
                    @foreach($payment->student->payments->where('type', 'inflow')->sortBy('payment_date') as $p)
                        <div class="flex items-center justify-between p-3 rounded-xl {{ $p->id === $payment->id ? 'bg-emerald-50 border border-emerald-200' : 'bg-gray-50' }} text-xs">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold text-gray-800">{{ $p->receipt_number ?? 'REC-' . $p->id }}</span>
                                <span class="text-gray-500">{{ $p->payment_date->format('d/m/Y') }}</span>
                                <span class="text-gray-500">({{ $p->payment_method_label }})</span>
                            </div>
                            <span class="font-mono font-bold text-emerald-600">
                                +{{ number_format($p->amount, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif


    {{-- Zone de Danger / Suppression --}}
    <div class="flex justify-end pt-4">
        <form
            method="POST"
            action="{{ route('payments.destroy', $payment) }}"
            onsubmit="return confirm('Attention : supprimer cette écriture modifiera le solde de caisse et le bilan comptable. Continuer ?');"
        >
            @csrf
            @method('DELETE')
            <button
                type="submit"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 font-semibold text-xs transition"
            >
                <x-lucide-trash-2 class="w-4 h-4" />
                <span>Supprimer cette écriture de caisse</span>
            </button>
        </form>
    </div>

</div>

@endsection