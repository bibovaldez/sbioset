<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="This system focuses on security of user and data that being processed.">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Tab Icon svg icon.svg --}}
    <link rel="icon" href="{{ asset('icon.ico') }}" type="image/x-icon" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://www.google.com/recaptcha/api.js?render=6LeEUA4qAAAAACi7eQFSCJzR0Ehj4A-dwnaEJ7vS"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Styles -->
    @livewireStyles
</head>

<body>
    <div class="font-sans text-gray-900 antialiased">
        {{ $slot }}
    </div>

    <!-- Digital Clock -->
    <div id="digital-clock" class="fixed bottom-0 left-0 m-4 text-gray-900 dark:text-gray-100"></div>

    @stack('scripts')
    @livewireScripts

    <script>
        function updateClock() {
            const now = new Date();
            let hours = now.getHours();
            let ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds} ${ampm}`;
            document.getElementById('digital-clock').textContent = timeString;
        }

        setInterval(updateClock, 1000);
        updateClock(); // Initial call to display clock immediately
    </script>
</body>

</html>