@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto space-y-8">

    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold">
                Modifier le partenaire
            </h1>

            <p class="text-muted mt-1">
                Modifier les informations du partenaire.
            </p>

        </div>

        <a
            href="{{ route('partners.index') }}"
            class="px-5 py-3 rounded-xl border hover:bg-gray-100">

            Retour

        </a>

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
        action="{{ route('partners.update', $partner) }}"
        enctype="multipart/form-data"
        class="space-y-8">

        @csrf
        @method('PUT')

        <x-ui.card
            title="Informations du partenaire"
            subtitle="Modifier les informations">

            <div class="space-y-6">

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Nom du partenaire
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $partner->name) }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Site web
                    </label>

                    <input
                        type="url"
                        name="website"
                        value="{{ old('website', $partner->website) }}"
                        placeholder="https://exemple.com"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                @if($partner->logo)

                    <div>

                        <p class="text-sm font-medium mb-2">
                            Logo actuel
                        </p>

                        <div class="w-full h-48 bg-gray-50 rounded-xl flex items-center justify-center p-6">

                            <img
                                src="{{ asset('storage/' . $partner->logo) }}"
                                alt="{{ $partner->name }}"
                                class="max-h-36 max-w-full object-contain">

                        </div>

                    </div>

                @endif

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Nouveau logo
                    </label>

                    <input
                        type="file"
                        name="logo"
                        accept="image/jpeg,image/png,image/webp,image/svg+xml"
                        class="w-full rounded-xl border-gray-300">

                    <p class="text-xs text-gray-500 mt-2">
                        Laissez vide pour conserver le logo actuel.
                    </p>

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('description', $partner->description) }}</textarea>

                </div>

                <div>

                    <label class="inline-flex items-center gap-3">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked(old('is_active', $partner->is_active))
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
                Enregistrer les modifications
            </x-ui.button>

        </div>

    </form>

</div>

@endsection