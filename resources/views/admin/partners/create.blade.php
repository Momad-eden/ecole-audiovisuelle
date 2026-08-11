@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold">
                Nouveau partenaire
            </h1>

            <p class="text-muted mt-1">
                Ajouter un partenaire de l'école.
            </p>
        </div>

        <x-ui.button
            variant="outline"
            href="{{ route('partners.index') }}">

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
        action="{{ route('partners.store') }}"
        enctype="multipart/form-data"
        class="space-y-8">

        @csrf

        <x-ui.card
            title="Informations du partenaire"
            subtitle="Informations qui seront visibles sur le site public">

            <div class="space-y-6">

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Nom du partenaire
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        placeholder="Ex : Ministère de la Culture"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    @error('name')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Site web
                    </label>

                    <input
                        type="url"
                        name="website"
                        value="{{ old('website') }}"
                        placeholder="https://exemple.com"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                    @error('website')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Logo
                    </label>

                    <input
                        type="file"
                        name="logo"
                        accept="image/jpeg,image/png,image/webp,image/svg+xml"
                        class="w-full rounded-xl border-gray-300">

                    <p class="text-xs text-gray-500 mt-2">
                        JPG, PNG, WEBP ou SVG — maximum 5 Mo.
                    </p>

                    @error('logo')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        placeholder="Présentation du partenaire..."
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>

                </div>

                <div>

                    <label class="inline-flex items-center gap-3">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            checked
                            class="rounded border-gray-300 text-primary focus:ring-primary">

                        <span>
                            Partenaire actif et visible sur le site public
                        </span>

                    </label>

                </div>

            </div>

        </x-ui.card>

        <div class="flex justify-end gap-4">

            <a
                href="{{ route('partners.index') }}"
                class="px-5 py-3 rounded-xl border hover:bg-gray-100">

                Annuler

            </a>

            <x-ui.button type="submit">
                Enregistrer le partenaire
            </x-ui.button>

        </div>

    </form>

</div>

@endsection