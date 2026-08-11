@extends('layouts.admin')

@section('content')

<div class="max-w-3xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                Modifier l'utilisateur
            </h1>

            <p class="text-muted mt-1">
                Modifier les informations et les accès de {{ $user->name }}.
            </p>
        </div>

        <x-ui.button
            variant="outline"
            href="{{ route('users.index') }}">

            Retour

        </x-ui.button>

    </div>

    @if($errors->any())

        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-5">

            <ul class="list-disc list-inside space-y-1">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="{{ route('users.update', $user) }}"
        class="space-y-8">

        @csrf
        @method('PUT')

        <x-ui.card
            title="Informations du compte"
            subtitle="Identité et rôle de l'utilisateur">

            <div class="space-y-6">

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Nom complet
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Adresse email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Rôle
                    </label>

                    <select
                        name="role"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                        @foreach($roles as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(old('role', $user->role) === $value)>

                                {{ $label }}

                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

        </x-ui.card>


        <x-ui.card
            title="Modifier le mot de passe"
            subtitle="Laissez vide pour conserver le mot de passe actuel">

            <div class="space-y-6">

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Nouveau mot de passe
                    </label>

                    <input
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    <p class="text-xs text-gray-500 mt-2">
                        Minimum 8 caractères.
                    </p>

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Confirmer le nouveau mot de passe
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

            </div>

        </x-ui.card>


        <div class="flex justify-end gap-4">

            <x-ui.button
                variant="outline"
                href="{{ route('users.index') }}">

                Annuler

            </x-ui.button>

            <x-ui.button type="submit">

                Enregistrer les modifications

            </x-ui.button>

        </div>

    </form>

</div>

@endsection