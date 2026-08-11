@extends('layouts.admin')

@section('content')

<div class="max-w-6xl mx-auto space-y-8">

    <div>

        <h1 class="text-3xl font-bold">
            Paramètres
        </h1>

        <p class="text-muted mt-1">
            Configurez les informations générales de l'école.
        </p>

    </div>

    @if(session('success'))

        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl">
            {{ session('success') }}
        </div>

    @endif

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
        action="{{ route('settings.update') }}"
        enctype="multipart/form-data"
        class="space-y-8">

        @csrf
        @method('PUT')

        <x-ui.card
            title="Informations de l'école"
            subtitle="Informations générales affichées sur le site">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="md:col-span-2">

                    <label class="block text-sm font-medium mb-2">
                        Nom de l'école
                    </label>

                    <input
                        type="text"
                        name="school_name"
                        value="{{ old('school_name', $settings->school_name) }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div class="md:col-span-2">

                    <label class="block text-sm font-medium mb-2">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('description', $settings->description) }}</textarea>

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Téléphone
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone', $settings->phone) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $settings->email) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div class="md:col-span-2">

                    <label class="block text-sm font-medium mb-2">
                        Adresse
                    </label>

                    <textarea
                        name="address"
                        rows="3"
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">{{ old('address', $settings->address) }}</textarea>

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Site web
                    </label>

                    <input
                        type="url"
                        name="website"
                        value="{{ old('website', $settings->website) }}"
                        placeholder="https://..."
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        WhatsApp
                    </label>

                    <input
                        type="text"
                        name="whatsapp"
                        value="{{ old('whatsapp', $settings->whatsapp) }}"
                        placeholder="+221..."
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

            </div>

        </x-ui.card>


        <x-ui.card
            title="Logo"
            subtitle="Logo officiel de l'école">

            <div class="space-y-5">

                @if($settings->logo)

                    <div>

                        <p class="text-sm text-muted mb-2">
                            Logo actuel
                        </p>

                        <div class="w-48 h-32 rounded-xl bg-gray-50 flex items-center justify-center p-4">

                            <img
                                src="{{ asset('storage/' . $settings->logo) }}"
                                alt="{{ $settings->school_name }}"
                                class="max-w-full max-h-full object-contain">

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
                        accept="image/jpeg,image/png,image/webp"
                        class="w-full rounded-xl border-gray-300">

                    <p class="text-xs text-gray-500 mt-2">
                        JPG, PNG ou WEBP — maximum 5 Mo.
                    </p>

                </div>

            </div>

        </x-ui.card>


        <x-ui.card
            title="Réseaux sociaux"
            subtitle="Liens vers les comptes officiels">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Facebook
                    </label>

                    <input
                        type="url"
                        name="facebook"
                        value="{{ old('facebook', $settings->facebook) }}"
                        placeholder="https://facebook.com/..."
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        Instagram
                    </label>

                    <input
                        type="url"
                        name="instagram"
                        value="{{ old('instagram', $settings->instagram) }}"
                        placeholder="https://instagram.com/..."
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

                <div>

                    <label class="block text-sm font-medium mb-2">
                        YouTube
                    </label>

                    <input
                        type="url"
                        name="youtube"
                        value="{{ old('youtube', $settings->youtube) }}"
                        placeholder="https://youtube.com/..."
                        class="w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">

                </div>

            </div>

        </x-ui.card>


        <div class="flex justify-end">

            <x-ui.button type="submit">
                Enregistrer les paramètres
            </x-ui.button>

        </div>

    </form>

</div>

@endsection