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

    <!-- Favicon EMSI -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Polices Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

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