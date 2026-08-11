<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        @yield(
        'title',
        'EMSI — École de Formation Audiovisuelle'
        )
    </title>

    <meta
        name="description"
        content="@yield(
            'description',
            'EMSI — École de Formation Audiovisuelle au cœur du Grand Théâtre National Doudou Ndiaye Rose, Dakar.'
        )">

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])

</head>


<body
    class="
        min-h-screen
        bg-white
        text-[#111111]
        antialiased
    ">

    <div class="min-h-screen flex flex-col">

        {{-- HEADER --}}
        <x-public.header />


        {{-- CONTENU --}}
        <main class="flex-1 pt-[76px]">

            @yield('content')

        </main>


        {{-- FOOTER --}}
        <x-public.footer />

    </div>


    @stack('scripts')

</body>

</html>