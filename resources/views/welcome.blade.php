<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Flowbite -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<style>
    @keyframes marquee {
        0% {
            transform: translateX(100%);
        }

        100% {
            transform: translateX(-100%);
        }
    }

    .animate-marquee {
        animation: marquee 20s linear infinite;
    }
</style>

<body class="antialiased font-sans">
    <!-- Banner -->
    <livewire:banner />

    <!-- Announcement -->
    <livewire:announcement />

    <!-- Navigation -->
    <livewire:navigation />

    <!-- Livewire HeroSection Component -->
    <livewire:hero-section />

    <!-- Heading -->
    <livewire:heading />

    <!-- Promo -->
    <livewire:promo />

    <!-- FAQ -->
    <livewire:faq />

    <!-- Newsletter -->
    <livewire:newsletter />

    <!-- Footer -->
    <livewire:footer />

    <!-- Flowbite -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    @livewireScripts
</body>

</html>