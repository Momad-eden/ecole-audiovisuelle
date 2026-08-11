@extends('layouts.admin')

@section('content')

<div class="space-y-8">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold">
                Utilisateurs
            </h1>

            <p class="text-muted mt-1">
                Gérez les comptes et les accès à l'administration.
            </p>
        </div>

        <x-ui.button href="{{ route('users.create') }}">
            + Nouvel utilisateur
        </x-ui.button>

    </div>

    @if(session('success'))

        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl">
            {{ session('success') }}
        </div>

    @endif

    @if(session('error'))

        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl">
            {{ session('error') }}
        </div>

    @endif

    <x-ui.card
        title="Comptes administrateurs"
        subtitle="Utilisateurs autorisés à accéder à l'administration">

        @if($users->count())

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="text-left px-6 py-4">
                                Utilisateur
                            </th>

                            <th class="text-left px-6 py-4">
                                Email
                            </th>

                            <th class="text-left px-6 py-4">
                                Rôle
                            </th>

                            <th class="text-left px-6 py-4">
                                Créé le
                            </th>

                            <th class="text-center px-6 py-4">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($users as $user)

                            <tr class="border-t hover:bg-gray-50">

                                <td class="px-6 py-4">

                                    <div class="font-medium">
                                        {{ $user->name }}
                                    </div>

                                    @if($user->id === auth()->id())

                                        <span class="text-xs text-primary">
                                            Votre compte
                                        </span>

                                    @endif

                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $user->email }}
                                </td>

                                <td class="px-6 py-4">

                                    @switch($user->role)

                                        @case('directeur')

                                            <x-ui.badge variant="success">
                                                Directeur
                                            </x-ui.badge>

                                            @break

                                        @case('gestionnaire')

                                            <x-ui.badge variant="info">
                                                Gestionnaire
                                            </x-ui.badge>

                                            @break

                                        @case('secretaire')

                                            <x-ui.badge variant="warning">
                                                Secrétaire
                                            </x-ui.badge>

                                            @break

                                        @case('communication')

                                            <x-ui.badge variant="info">
                                                Communication
                                            </x-ui.badge>

                                            @break

                                    @endswitch

                                </td>

                                <td class="px-6 py-4 text-gray-500">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>

                                <td class="px-6 py-4">

                                    <div class="flex justify-center gap-2">

                                        <a
                                            href="{{ route('users.show', $user) }}"
                                            class="p-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">

                                            <x-lucide-eye class="w-4 h-4"/>

                                        </a>

                                        <a
                                            href="{{ route('users.edit', $user) }}"
                                            class="p-2 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200">

                                            <x-lucide-pencil class="w-4 h-4"/>

                                        </a>

                                        @if($user->id !== auth()->id())

                                            <form
                                                method="POST"
                                                action="{{ route('users.destroy', $user) }}"
                                                onsubmit="return confirm('Supprimer cet utilisateur ?');">

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="p-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">

                                                    <x-lucide-trash-2 class="w-4 h-4"/>

                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>

        @else

            <div class="text-center py-16">

                <x-lucide-users
                    class="w-12 h-12 mx-auto text-gray-300"/>

                <h2 class="text-lg font-semibold mt-4">
                    Aucun utilisateur
                </h2>

            </div>

        @endif

    </x-ui.card>

</div>

@endsection