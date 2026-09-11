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

    <!-- Favicon EMSI -->
    <link rel="icon" type="image/x-icon" href="{{ $siteSettings?->logo ? asset('storage/' . $siteSettings->logo) : asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $siteSettings?->logo ? asset('storage/' . $siteSettings->logo) : asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $siteSettings?->logo ? asset('storage/' . $siteSettings->logo) : asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $siteSettings?->logo ? asset('storage/' . $siteSettings->logo) : asset('apple-touch-icon.png') }}">

    <!-- Polices Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

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
        <main class="flex-1 pt-[64px] md:pt-[104px]">

            @yield('content')

        </main>


        {{-- FOOTER --}}
        <x-public.footer />

    </div>


    @stack('scripts')

</body>

</html>