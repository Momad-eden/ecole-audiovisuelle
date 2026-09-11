@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto space-y-8" x-data="{
    operationType: '{{ old('type', $payment->type ?? 'inflow') }}',
    category: '{{ old('category', $payment->category ?? 'scolarite') }}',
    selectedStudentId: '{{ old('student_id', $payment->student_id ?? '') }}'
}">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <x-lucide-pencil class="w-8 h-8 text-amber-600" />
                <span>Modifier l'écriture {{ $payment->receipt_number ?? '#' . $payment->id }}</span>
            </h1>
            <p class="text-gray-500 mt-1">
                Ajuster les informations comptables ou corriger une saisie.
            </p>
        </div>

        <a
            href="{{ route('payments.show', $payment) }}"
            class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm transition"
        >
            Retour aux détails
        </a>
    </div>


    {{-- Erreurs --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl p-5 shadow-sm">
            <p class="font-semibold mb-2 flex items-center gap-2">
                <x-lucide-alert-circle class="w-5 h-5" />
                Veuillez corriger les erreurs suivantes :
            </p>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form
        method="POST"
        action="{{ route('payments.update', $payment) }}"
        class="space-y-8"
    >
        @csrf
        @method('PUT')

        {{-- 1. Nature de l'opération --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
                Nature de l'opération
            </label>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label
                    class="relative flex items-center p-4 rounded-2xl border-2 cursor-pointer transition"
                    :class="operationType === 'inflow' ? 'border-emerald-600 bg-emerald-50/50 shadow-sm' : 'border-gray-200 hover:border-gray-300 bg-white'"
                >
                    <input
                        type="radio"
                        name="type"
                        value="inflow"
                        x-model="operationType"
                        class="sr-only"
                    >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center font-bold"
                            :class="operationType === 'inflow' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-400'"
                        >
                            <x-lucide-arrow-down-left class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Encaissement (Recette)</p>
                            <p class="text-xs text-gray-500">Scolarité, frais d'inscription, prestations...</p>
                        </div>
                    </div>
                </label>

                <label
                    class="relative flex items-center p-4 rounded-2xl border-2 cursor-pointer transition"
                    :class="operationType === 'outflow' ? 'border-red-600 bg-red-50/50 shadow-sm' : 'border-gray-200 hover:border-gray-300 bg-white'"
                >
                    <input
                        type="radio"
                        name="type"
                        value="outflow"
                        x-model="operationType"
                        class="sr-only"
                    >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center font-bold"
                            :class="operationType === 'outflow' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-400'"
                        >
                            <x-lucide-arrow-up-right class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Décaissement (Dépense)</p>
                            <p class="text-xs text-gray-500">Achat matériel, intervenants, régie, logistique...</p>
                        </div>
                    </div>
                </label>
            </div>
        </div>


        {{-- 2. Détails de l'écriture --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm space-y-6">

            <div class="border-b border-gray-100 pb-4">
                <h3 class="text-lg font-bold text-gray-900">Détails modifiables</h3>
                <p class="text-xs text-gray-500 mt-0.5">Corrigez les montants ou les informations comptables.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Catégorie --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">
                        Catégorie comptable <span class="text-red-500">*</span>
                    </label>

                    <template x-if="operationType === 'inflow'">
                        <select
                            name="category"
                            x-model="category"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 text-sm outline-none"
                        >
                            @foreach(\App\Enums\TransactionCategory::inflowOptions() as $key => $label)
                                <option value="{{ $key }}" @selected(old('category', $payment->category) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </template>

                    <template x-if="operationType === 'outflow'">
                        <select
                            name="category"
                            x-model="category"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-red-600 focus:ring-1 focus:ring-red-600 text-sm outline-none"
                        >
                            @foreach(\App\Enums\TransactionCategory::outflowOptions() as $key => $label)
                                <option value="{{ $key }}" @selected(old('category', $payment->category) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </template>
                </div>


                {{-- Étudiant (si Encaissement) --}}
                <div
                    x-show="operationType === 'inflow'"
                    class="md:col-span-2"
                >
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">
                        Étudiant concerné
                    </label>

                    <select
                        name="student_id"
                        x-model="selectedStudentId"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary text-sm outline-none"
                    >
                        <option value="">Sélectionner un étudiant (ou laisser vide)</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" @selected(old('student_id', $payment->student_id) == $student->id)>
                                {{ $student->full_name }} ({{ $student->matricule ?? 'Sans matricule' }}) — {{ $student->course?->title ?? 'Sans formation' }}
                            </option>
                        @endforeach
                    </select>
                </div>


                {{-- Libellé / Motif --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">
                        Libellé / Intitulé
                    </label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title', $payment->title) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary text-sm outline-none"
                    >
                </div>


                {{-- Montant --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">
                        Montant (FCFA) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            name="amount"
                            min="1"
                            step="1"
                            value="{{ old('amount', (int)$payment->amount) }}"
                            required
                            class="w-full pl-4 pr-16 py-2.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary text-base font-bold font-mono outline-none"
                        >
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">FCFA</span>
                    </div>
                </div>


                {{-- Mode de règlement --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">
                        Moyen de règlement <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="payment_method"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary text-sm outline-none"
                    >
                        @foreach(\App\Enums\PaymentMethod::options() as $key => $label)
                            <option value="{{ $key }}" @selected(old('payment_method', $payment->payment_method) === $key)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>


                {{-- Date d'opération --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">
                        Date d'opération <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="payment_date"
                        value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary text-sm outline-none"
                    >
                </div>


                {{-- Référence externe --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">
                        Référence externe / N° transaction
                    </label>
                    <input
                        type="text"
                        name="reference"
                        value="{{ old('reference', $payment->reference) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary text-sm outline-none"
                    >
                </div>


                {{-- Notes --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1.5">
                        Notes & Observations
                    </label>
                    <textarea
                        name="notes"
                        rows="3"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary text-sm outline-none"
                    >{{ old('notes', $payment->notes) }}</textarea>
                </div>

            </div>

        </div>


        {{-- Boutons --}}
        <div class="flex items-center justify-end gap-3">
            <a
                href="{{ route('payments.show', $payment) }}"
                class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm transition"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-bold text-sm transition shadow-md shadow-primary/20"
            >
                <x-lucide-check class="w-4 h-4" />
                <span>Enregistrer les modifications</span>
            </button>
        </div>

    </form>

</div>

@endsection