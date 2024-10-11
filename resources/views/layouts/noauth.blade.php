<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Botman -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/assets/css/chat.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        input:focus {
            @apply outline-none ring-0;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <livewire:navigation />
        <div>
            {{ $slot }}
        </div>
        <livewire:footer />
    </div>
    <!-- Flowbite -->
    <script src="{{ asset('js/flowbite.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('swal:success', (data) => {
                Swal.fire({
                    title: data[0].title,
                    text: data[0].text,
                    icon: data[0].icon,
                    timer: data[0].timer,
                    showConfirmButton: false
                });
            });
        });
    </script>

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
</body>

</html>