@extends('layouts.admin')

@section('content')

<div class="space-y-8">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold">
                Actualités
            </h1>

            <p class="text-muted mt-1">
                Gérez les actualités et publications de l'école.
            </p>
        </div>

        <x-ui.button href="{{ route('news.create') }}">
            + Nouvelle actualité
        </x-ui.button>

    </div>


    {{-- Message succès --}}
    @if(session('success'))

        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl">
            {{ session('success') }}
        </div>

    @endif


    {{-- Liste --}}
    <x-ui.card
        title="Publications"
        subtitle="Toutes les actualités enregistrées">

        @if($news->count())

            <div class="space-y-5">

                @foreach($news as $article)

                    <div class="flex flex-col md:flex-row gap-5 p-5 border border-gray-100 rounded-2xl hover:bg-gray-50 transition">

                        {{-- Image --}}
                        <div class="w-full md:w-52 h-36 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">

                            @if($article->image)

                                <img
                                    src="{{ asset('storage/' . $article->image) }}"
                                    alt="{{ $article->title }}"
                                    class="w-full h-full object-cover">

                            @else

                                <div class="w-full h-full flex items-center justify-center text-gray-300">

                                    <x-lucide-newspaper class="w-10 h-10"/>

                                </div>

                            @endif

                        </div>


                        {{-- Contenu --}}
                        <div class="flex-1 min-w-0">

                            <div class="flex flex-wrap items-center gap-3">

                                <h2 class="text-lg font-semibold">
                                    {{ $article->title }}
                                </h2>

                                @if($article->is_published)

                                    <x-ui.badge variant="success">
                                        Publiée
                                    </x-ui.badge>

                                @else

                                    <x-ui.badge variant="danger">
                                        Brouillon
                                    </x-ui.badge>

                                @endif

                            </div>


                            @if($article->excerpt)

                                <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                                    {{ $article->excerpt }}
                                </p>

                            @endif


                            <div class="flex flex-wrap gap-4 text-sm text-gray-500 mt-4">

                                <span>
                                    {{ $article->created_at->format('d/m/Y') }}
                                </span>

                                @if($article->published_at)

                                    <span>
                                        Publiée le
                                        {{ $article->published_at->format('d/m/Y') }}
                                    </span>

                                @endif

                            </div>

                        </div>


                        {{-- Actions --}}
                        <div class="flex md:flex-col justify-end gap-2">

                            <a
                                href="{{ route('news.show', $article) }}"
                                class="p-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200"
                                title="Voir">

                                <x-lucide-eye class="w-4 h-4"/>

                            </a>

                            <a
                                href="{{ route('news.edit', $article) }}"
                                class="p-2 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200"
                                title="Modifier">

                                <x-lucide-pencil class="w-4 h-4"/>

                            </a>

                            <form
                                method="POST"
                                action="{{ route('news.destroy', $article) }}"
                                onsubmit="return confirm('Supprimer cette actualité ?');">

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

                    </div>

                @endforeach

            </div>

            <div class="mt-6">
                {{ $news->links() }}
            </div>

        @else

            <div class="text-center py-16">

                <x-lucide-newspaper class="w-12 h-12 mx-auto text-gray-300"/>

                <h2 class="text-lg font-semibold mt-4">
                    Aucune actualité
                </h2>

                <p class="text-muted mt-2">
                    Vous n'avez encore publié aucune actualité.
                </p>

                <div class="mt-6">

                    <x-ui.button href="{{ route('news.create') }}">
                        Créer une actualité
                    </x-ui.button>

                </div>

            </div>

        @endif

    </x-ui.card>

</div>

@endsection