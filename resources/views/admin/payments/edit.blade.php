@extends('layouts.admin')

@section('content')

<div class="max-w-5xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold">
                Modifier le paiement
            </h1>

            <p class="text-muted mt-1">
                Modifier les informations du règlement.
            </p>
        </div>

        <a
            href="{{ route('payments.show', $payment) }}"
            class="px-5 py-3 rounded-xl border hover:bg-gray-100">

            Retour

        </a>

    </div>

    @if ($errors->any())

        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-5">

            <ul class="list-disc list-inside">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="{{ route('payments.update', $payment) }}"
        class="space-y-8">

        @csrf
        @method('PUT')

        <x-ui.card
            title="Informations du paiement"
            subtitle="Modifier le règlement">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="md:col-span-2">

                    <label class="block text-sm font-medium mb-2">
                        Étudiant
                    </label>

                    <select
                        name="student_id"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                        @foreach($students as $student)

                            <option
                                value="{{ $student->id }}"
                                @selected(old('student_id', $payment->student_id) == $student->id)>

                                {{ $student->first_name }}
                                {{ $student->last_name }}

                                — {{ $student->student_number ?? 'Sans matricule' }}

                                @if($student->course)
                                    — {{ $student->course->title }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Montant (FCFA)
                    </label>

                    <input
                        type="number"
                        name="amount"
                        value="{{ old('amount', $payment->amount) }}"
                        min="1"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Date du paiement
                    </label>

                    <input
                        type="date"
                        name="payment_date"
                        value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Mode de paiement
                    </label>

                    <select
                        name="payment_method"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                        <option value="cash" @selected(old('payment_method', $payment->payment_method) === 'cash')>
                            Espèces
                        </option>

                        <option value="wave" @selected(old('payment_method', $payment->payment_method) === 'wave')>
                            Wave
                        </option>

                        <option value="orange_money" @selected(old('payment_method', $payment->payment_method) === 'orange_money')>
                            Orange Money
                        </option>

                        <option value="bank" @selected(old('payment_method', $payment->payment_method) === 'bank')>
                            Virement bancaire
                        </option>

                        <option value="other" @selected(old('payment_method', $payment->payment_method) === 'other')>
                            Autre
                        </option>

                    </select>

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Référence
                    </label>

                    <input
                        type="text"
                        name="reference"
                        value="{{ old('reference', $payment->reference) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div class="md:col-span-2">

                    <label class="block text-sm font-medium mb-2">
                        Notes
                    </label>

                    <textarea
                        name="notes"
                        rows="4"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('notes', $payment->notes) }}</textarea>

                </div>

            </div>

        </x-ui.card>

        <div class="flex justify-end gap-4">

            <a
                href="{{ route('payments.show', $payment) }}"
                class="px-5 py-3 rounded-xl border hover:bg-gray-100">

                Annuler

            </a>

            <x-ui.button type="submit">

                Enregistrer les modifications

            </x-ui.button>

        </div>

    </form>

</div>

@endsection