@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold">
                Détail du paiement
            </h1>

            <p class="text-muted mt-1">
                Informations du règlement.
            </p>
        </div>

        <div class="flex gap-3">

            <a
                href="{{ route('payments.edit', $payment) }}"
                class="px-4 py-2 rounded-xl bg-blue-100 text-blue-700 hover:bg-blue-200">

                Modifier

            </a>

            <a
                href="{{ route('payments.index') }}"
                class="px-4 py-2 rounded-xl border hover:bg-gray-100">

                Retour

            </a>

        </div>

    </div>

    @if(session('success'))

        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl">
            {{ session('success') }}
        </div>

    @endif

    <x-ui.card
        title="Informations du paiement">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <p class="text-sm text-muted">Étudiant</p>

                <p class="font-semibold text-lg mt-1">
                    {{ $payment->student?->first_name }}
                    {{ $payment->student?->last_name }}
                </p>
            </div>

            <div>
                <p class="text-sm text-muted">Matricule</p>

                <p class="font-semibold mt-1">
                    {{ $payment->student?->student_number ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-muted">Formation</p>

                <p class="font-semibold mt-1">
                    {{ $payment->student?->course?->title ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-muted">Montant</p>

                <p class="text-2xl font-bold text-primary mt-1">
                    {{ number_format($payment->amount, 0, ' ', ' ') }} FCFA
                </p>
            </div>

            <div>
                <p class="text-sm text-muted">Mode de paiement</p>

                <p class="font-semibold mt-1">

                    @switch($payment->payment_method)

                        @case('cash')
                            Espèces
                            @break

                        @case('wave')
                            Wave
                            @break

                        @case('orange_money')
                            Orange Money
                            @break

                        @case('bank')
                            Virement bancaire
                            @break

                        @default
                            Autre

                    @endswitch

                </p>
            </div>

            <div>
                <p class="text-sm text-muted">Date</p>

                <p class="font-semibold mt-1">
                    {{ $payment->payment_date->format('d/m/Y') }}
                </p>
            </div>

            <div>
                <p class="text-sm text-muted">Référence</p>

                <p class="font-semibold mt-1">
                    {{ $payment->reference ?: '—' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-muted">Enregistré le</p>

                <p class="font-semibold mt-1">
                    {{ $payment->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>

            <div class="md:col-span-2">

                <p class="text-sm text-muted">
                    Notes
                </p>

                <p class="mt-1">
                    {{ $payment->notes ?: 'Aucune note.' }}
                </p>

            </div>

        </div>

    </x-ui.card>

    <div class="flex justify-end">

        <form
            method="POST"
            action="{{ route('payments.destroy', $payment) }}"
            onsubmit="return confirm('Supprimer définitivement ce paiement ?');">

            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="px-5 py-3 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 font-semibold">

                Supprimer le paiement

            </button>

        </form>

    </div>

</div>

@endsection