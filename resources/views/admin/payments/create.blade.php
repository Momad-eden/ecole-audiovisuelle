@extends('layouts.admin')

@section('content')

<div class="max-w-5xl mx-auto space-y-8">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                Nouveau paiement
            </h1>

            <p class="text-muted mt-1">
                Enregistrer un paiement effectué par un étudiant.
            </p>
        </div>

        <x-ui.button
            variant="outline"
            href="{{ route('payments.index') }}">

            Retour

        </x-ui.button>

    </div>


    {{-- Erreurs --}}
    @if ($errors->any())

        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-5">

            <p class="font-semibold mb-2">
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
        action="{{ route('payments.store') }}"
        class="space-y-8">

        @csrf


        {{-- Informations du paiement --}}
        <x-ui.card
            title="Informations du paiement"
            subtitle="Enregistrer les détails du règlement">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Étudiant --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-medium mb-2">
                        Étudiant
                    </label>

                    <select
                        name="student_id"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                        <option value="">
                            Sélectionner un étudiant
                        </option>

                        @foreach($students as $student)

                            <option
                                value="{{ $student->id }}"
                                @selected(old('student_id') == $student->id)>

                                {{ $student->first_name }}
                                {{ $student->last_name }}

                                — {{ $student->student_number ?? 'Sans matricule' }}

                                @if($student->course)
                                    — {{ $student->course->title }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                    @error('student_id')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Montant --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Montant (FCFA)
                    </label>

                    <input
                        type="number"
                        name="amount"
                        value="{{ old('amount') }}"
                        min="1"
                        step="1"
                        required
                        placeholder="50000"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    @error('amount')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Date --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Date du paiement
                    </label>

                    <input
                        type="date"
                        name="payment_date"
                        value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    @error('payment_date')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Mode de paiement --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Mode de paiement
                    </label>

                    <select
                        name="payment_method"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                        <option value="">
                            Sélectionner un mode
                        </option>

                        <option
                            value="cash"
                            @selected(old('payment_method') === 'cash')>

                            Espèces

                        </option>

                        <option
                            value="wave"
                            @selected(old('payment_method') === 'wave')>

                            Wave

                        </option>

                        <option
                            value="orange_money"
                            @selected(old('payment_method') === 'orange_money')>

                            Orange Money

                        </option>

                        <option
                            value="bank"
                            @selected(old('payment_method') === 'bank')>

                            Virement bancaire

                        </option>

                        <option
                            value="other"
                            @selected(old('payment_method') === 'other')>

                            Autre

                        </option>

                    </select>

                    @error('payment_method')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Référence --}}
                <div>

                    <label class="block text-sm font-medium mb-2">
                        Référence
                    </label>

                    <input
                        type="text"
                        name="reference"
                        value="{{ old('reference') }}"
                        placeholder="Ex : WAV-2026-00125"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    <p class="text-xs text-gray-500 mt-2">
                        Facultatif. Utile pour les paiements électroniques.
                    </p>

                    @error('reference')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Notes --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-medium mb-2">
                        Notes
                    </label>

                    <textarea
                        name="notes"
                        rows="4"
                        placeholder="Informations complémentaires..."
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('notes') }}</textarea>

                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </x-ui.card>


        {{-- Boutons --}}
        <div class="flex justify-end gap-4">

            <x-ui.button
                variant="outline"
                href="{{ route('payments.index') }}">

                Annuler

            </x-ui.button>

            <x-ui.button type="submit">

                Enregistrer le paiement

            </x-ui.button>

        </div>

    </form>

</div>

@endsection