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

    <!-- Botman -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/assets/css/chat.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

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

        input:focus {
            @apply outline-none ring-0;
        }
    </style>
</head>

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

    <!-- Carousel Product -->
    <livewire:carousel-product />

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

    <script>
        var botmanWidget = {
            aboutText: 'Welcome! I\'m your virtual assistant, ready to help.',
            introMessage: "Hello! How can I assist you today?",
            title: 'Virtual Assistant',
            mainColor: '#4a90e2',
            bubbleBackground: '#4a90e2',
            aboutLink: 'https://www.google.com',
            open: true,
            disableUserInput: false,
            inputDisabled: false,
            placeholderText: 'Send a message...',
            autofocus: false,
            chatServer: '/botman',
            frameEndpoint: '/botman/chat',
            displayMessageTime: true
        };
    </script>

    <!-- Botman -->
    <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>
    <script id="botmanWidget" src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/chat.js'></script>

    <!-- Add this script to remove focus -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const chatInput = document.querySelector('#userText');
                if (chatInput) {
                    chatInput.blur();
                }
            }, 100);
        });
    </script>

    @livewireScripts
</body>

</html>