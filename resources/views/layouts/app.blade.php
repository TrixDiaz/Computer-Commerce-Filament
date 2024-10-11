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
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Banner -->
        <livewire:banner />

        <!-- Announcement -->
        <livewire:announcement />

        <!-- Navigation -->
        <livewire:navigation />

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- Footer -->
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
</body>

</html>