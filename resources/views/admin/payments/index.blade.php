@extends('layouts.admin')

@section('content')

<div class="space-y-8">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                Paiements
            </h1>

            <p class="text-muted mt-1">
                Gérez les paiements et frais de scolarité des étudiants.
            </p>
        </div>

        <x-ui.button href="{{ route('payments.create') }}">
            + Nouveau paiement
        </x-ui.button>

    </div>


    {{-- Message de succès --}}
    @if(session('success'))

        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl">
            {{ session('success') }}
        </div>

    @endif


    {{-- Statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <x-ui.stats-card
            title="Total encaissé"
            :value="number_format($totalAmount, 0, ' ', ' ') . ' FCFA'">

            <x-slot:icon>
                <x-lucide-wallet class="w-7 h-7 text-primary"/>
            </x-slot:icon>

        </x-ui.stats-card>


        <x-ui.stats-card
            title="Aujourd'hui"
            :value="number_format($todayAmount, 0, ' ', ' ') . ' FCFA'">

            <x-slot:icon>
                <x-lucide-calendar-days class="w-7 h-7 text-primary"/>
            </x-slot:icon>

        </x-ui.stats-card>


        <x-ui.stats-card
            title="Ce mois"
            :value="number_format($monthAmount, 0, ' ', ' ') . ' FCFA'">

            <x-slot:icon>
                <x-lucide-chart-column class="w-7 h-7 text-primary"/>
            </x-slot:icon>

        </x-ui.stats-card>

    </div>


    {{-- Liste des paiements --}}
    <x-ui.card
        title="Historique des paiements"
        subtitle="Derniers paiements enregistrés">

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                    <tr class="border-b bg-gray-50">

                        <th class="text-left px-6 py-4">
                            Étudiant
                        </th>

                        <th class="text-left px-6 py-4">
                            Formation
                        </th>

                        <th class="text-left px-6 py-4">
                            Montant
                        </th>

                        <th class="text-left px-6 py-4">
                            Mode
                        </th>

                        <th class="text-left px-6 py-4">
                            Date
                        </th>

                        <th class="text-center px-6 py-4">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($payments as $payment)

                        <tr class="border-b hover:bg-gray-50 transition">

                            {{-- Étudiant --}}
                            <td class="px-6 py-4">

                                <div class="font-semibold">
                                    {{ $payment->student?->first_name }}
                                    {{ $payment->student?->last_name }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    {{ $payment->student?->student_number ?? '—' }}
                                </div>

                            </td>


                            {{-- Formation --}}
                            <td class="px-6 py-4">

                                {{ $payment->student?->course?->title ?? '—' }}

                            </td>


                            {{-- Montant --}}
                            <td class="px-6 py-4 font-semibold">

                                {{ number_format($payment->amount, 0, ' ', ' ') }}
                                FCFA

                            </td>


                            {{-- Mode --}}
                            <td class="px-6 py-4">

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
                                        Banque
                                        @break

                                    @default
                                        Autre

                                @endswitch

                            </td>


                            {{-- Date --}}
                            <td class="px-6 py-4 text-sm text-gray-500">

                                {{ $payment->payment_date->format('d/m/Y') }}

                            </td>


                            {{-- Actions --}}
                            <td class="px-6 py-4">

                                <div class="flex justify-center gap-2">

                                    <a
                                        href="{{ route('payments.show', $payment) }}"
                                        class="p-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200"
                                        title="Voir">

                                        <x-lucide-eye class="w-4 h-4"/>

                                    </a>

                                    <a
                                        href="{{ route('payments.edit', $payment) }}"
                                        class="p-2 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200"
                                        title="Modifier">

                                        <x-lucide-pencil class="w-4 h-4"/>

                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('payments.destroy', $payment) }}"
                                        onsubmit="return confirm('Supprimer ce paiement ?');">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="p-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200"
                                            title="Supprimer">

                                            <x-lucide-trash-2 class="w-4 h-4"/>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-12 text-gray-500">

                                <div class="flex flex-col items-center gap-3">

                                    <x-lucide-wallet
                                        class="w-10 h-10 text-gray-300"/>

                                    <p>
                                        Aucun paiement enregistré.
                                    </p>

                                    <a
                                        href="{{ route('payments.create') }}"
                                        class="text-primary font-medium hover:underline">

                                        Enregistrer le premier paiement

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

            <div class="border-t px-6 py-4">

                {{ $payments->links() }}

            </div>

        @endif

    </x-ui.card>

</div>

@endsection