<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Administration — EMSI')
    </title>

    <meta
        name="description"
        content="Espace d'administration de l'École de Formation Audiovisuelle."
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body class="bg-gray-50 text-gray-900 antialiased">

    <div class="flex h-screen overflow-hidden">

        <aside
            class="w-64 flex-shrink-0 bg-[#320080] text-white flex flex-col h-screen"
        >

            @include('components.ui.sidebar')

        </aside>


        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

            @include('components.ui.header')

            <main class="flex-1 overflow-y-auto p-8">

                @yield('content')

            </main>

        </div>

    </div>

    @stack('scripts')

</body>

</html>