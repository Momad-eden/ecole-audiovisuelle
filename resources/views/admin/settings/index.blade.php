@extends('layouts.admin')

@section('title', 'Paramètres de l’école')

@section('content')

<div class="max-w-6xl mx-auto space-y-8">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                Paramètres généraux
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Configurez l'identité de l'école, les coordonnées de contact et les liens vers vos réseaux sociaux officiels.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3">
            <x-lucide-check-circle-2 class="w-5 h-5 text-emerald-600 shrink-0" />
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-2xl p-5">
            <div class="flex items-start gap-3">
                <x-lucide-alert-circle class="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
                <div>
                    <h3 class="text-sm font-bold text-red-900 mb-1">Veuillez corriger les erreurs suivantes :</h3>
                    <ul class="list-disc list-inside space-y-1 text-xs text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('settings.update') }}"
        enctype="multipart/form-data"
        class="space-y-8"
    >
        @csrf
        @method('PUT')

        {{-- 1. IDENTITÉ DE L'ÉCOLE --}}
        <x-ui.card
            title="Identité de l'établissement"
            subtitle="Informations principales visibles sur tout le site public">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Nom officiel de l'école <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="school_name"
                        value="{{ old('school_name', $settings->school_name) }}"
                        required
                        class="w-full rounded-xl border-gray-300 focus:border-[#F5B800] focus:ring-[#F5B800] text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Description / Slogan
                    </label>
                    <textarea
                        name="description"
                        rows="3"
                        placeholder="Courte présentation affichée dans le pied de page et les métadonnées..."
                        class="w-full rounded-xl border-gray-300 focus:border-[#F5B800] focus:ring-[#F5B800] text-sm">{{ old('description', $settings->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Téléphone officiel
                    </label>
                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone', $settings->phone) }}"
                        placeholder="+221 33 000 00 00"
                        class="w-full rounded-xl border-gray-300 focus:border-[#F5B800] focus:ring-[#F5B800] text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Email officiel de contact
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $settings->email) }}"
                        placeholder="contact@emsi.sn"
                        class="w-full rounded-xl border-gray-300 focus:border-[#F5B800] focus:ring-[#F5B800] text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Adresse physique du campus
                    </label>
                    <textarea
                        name="address"
                        rows="2"
                        placeholder="Grand Théâtre National Doudou Ndiaye Rose, Dakar, Sénégal"
                        class="w-full rounded-xl border-gray-300 focus:border-[#F5B800] focus:ring-[#F5B800] text-sm">{{ old('address', $settings->address) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Site Web
                    </label>
                    <input
                        type="url"
                        name="website"
                        value="{{ old('website', $settings->website) }}"
                        placeholder="https://emsi.sn"
                        class="w-full rounded-xl border-gray-300 focus:border-[#F5B800] focus:ring-[#F5B800] text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Numéro WhatsApp (avec indicatif)
                    </label>
                    <input
                        type="text"
                        name="whatsapp"
                        value="{{ old('whatsapp', $settings->whatsapp) }}"
                        placeholder="+221 77 000 00 00"
                        class="w-full rounded-xl border-gray-300 focus:border-[#F5B800] focus:ring-[#F5B800] text-sm">
                </div>

            </div>

        </x-ui.card>


        {{-- 2. LOGO --}}
        <x-ui.card
            title="Logo de l'école"
            subtitle="Fichier image officiel de la marque EMSI">

            <div class="space-y-5">

                @if($settings->logo)
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">
                            Logo actuel
                        </p>
                        <div class="w-48 h-28 rounded-2xl bg-gray-900 flex items-center justify-center p-4 border border-gray-200">
                            <img
                                src="{{ asset('storage/' . $settings->logo) }}"
                                alt="{{ $settings->school_name }}"
                                class="max-w-full max-h-full object-contain">
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        {{ $settings->logo ? 'Remplacer le logo' : 'Téléverser un logo' }}
                    </label>
                    <input
                        type="file"
                        name="logo"
                        accept="image/jpeg,image/png,image/webp"
                        class="w-full rounded-xl border border-gray-300 p-2 text-sm">
                    <p class="text-xs text-gray-500 mt-2">
                        Format PNG transparent recommandé, JPG ou WEBP — maximum 5 Mo.
                    </p>
                </div>

            </div>

        </x-ui.card>


        {{-- 3. RÉSEAUX SOCIAUX & MESSAGERIE --}}
        <x-ui.card
            title="Réseaux sociaux & Canaux officiels"
            subtitle="Liens vers les plateformes sociales affichées dans l'en-tête, la page contact et le pied de page">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Instagram --}}
                <div>
                    <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        <x-lucide-instagram class="w-4 h-4 text-pink-600" />
                        Instagram
                    </label>
                    <input
                        type="url"
                        name="instagram"
                        value="{{ old('instagram', $settings->instagram) }}"
                        placeholder="https://instagram.com/emsidakar"
                        class="w-full rounded-xl border-gray-300 focus:border-[#F5B800] focus:ring-[#F5B800] text-sm">
                </div>

                {{-- Facebook --}}
                <div>
                    <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        <x-lucide-facebook class="w-4 h-4 text-blue-600" />
                        Facebook
                    </label>
                    <input
                        type="url"
                        name="facebook"
                        value="{{ old('facebook', $settings->facebook) }}"
                        placeholder="https://facebook.com/emsidakar"
                        class="w-full rounded-xl border-gray-300 focus:border-[#F5B800] focus:ring-[#F5B800] text-sm">
                </div>

                {{-- YouTube --}}
                <div>
                    <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        <x-lucide-youtube class="w-4 h-4 text-red-600" />
                        YouTube
                    </label>
                    <input
                        type="url"
                        name="youtube"
                        value="{{ old('youtube', $settings->youtube) }}"
                        placeholder="https://youtube.com/@emsidakar"
                        class="w-full rounded-xl border-gray-300 focus:border-[#F5B800] focus:ring-[#F5B800] text-sm">
                </div>

                {{-- TikTok --}}
                <div>
                    <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        <x-lucide-video class="w-4 h-4 text-gray-900" />
                        TikTok
                    </label>
                    <input
                        type="url"
                        name="tiktok"
                        value="{{ old('tiktok', $settings->tiktok) }}"
                        placeholder="https://tiktok.com/@emsidakar"
                        class="w-full rounded-xl border-gray-300 focus:border-[#F5B800] focus:ring-[#F5B800] text-sm">
                </div>

                {{-- LinkedIn --}}
                <div>
                    <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        <x-lucide-linkedin class="w-4 h-4 text-blue-700" />
                        LinkedIn
                    </label>
                    <input
                        type="url"
                        name="linkedin"
                        value="{{ old('linkedin', $settings->linkedin) }}"
                        placeholder="https://linkedin.com/school/emsidakar"
                        class="w-full rounded-xl border-gray-300 focus:border-[#F5B800] focus:ring-[#F5B800] text-sm">
                </div>

                {{-- Twitter / X --}}
                <div>
                    <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        <x-lucide-twitter class="w-4 h-4 text-sky-500" />
                        Twitter / X
                    </label>
                    <input
                        type="url"
                        name="twitter"
                        value="{{ old('twitter', $settings->twitter) }}"
                        placeholder="https://x.com/emsidakar"
                        class="w-full rounded-xl border-gray-300 focus:border-[#F5B800] focus:ring-[#F5B800] text-sm">
                </div>

            </div>

        </x-ui.card>


        <div class="flex items-center justify-end gap-4 pt-4">
            <x-ui.button type="submit" class="!px-8 !py-3.5 text-sm font-bold">
                Enregistrer les paramètres
            </x-ui.button>
        </div>

    </form>

</div>

@endsection