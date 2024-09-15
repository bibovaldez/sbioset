<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark:bg-gray-900">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- meta description --}}
    {{-- <meta http-equiv="Content-Security-Policy" content=" upgrade-insecure-requests"> --}}
    <meta name="description" content="This system focuses on security of user and data that being processed.">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('icon.ico') }}" type="image/x-icon" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">
    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    @livewireStyles

    <style>
        /* Custom scrollbar styles for WebKit browsers */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }

        ::-webkit-scrollbar-track {
            background-color: #f1f1f1;
        }

        /* Custom scrollbar styles for Firefox */
        body {
            scrollbar-width: thin;
            scrollbar-color: #888 #f1f1f1;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @php
            use App\Models\Team;
            $user = Auth::user();
            $team = Team::find($user->current_team_id);
            $role = $user->isAdmin()
                ? 'admin'
                : ($user->isUser() && $user->hasTeamRole($team, 'admin')
                    ? 'sub-admin'
                    : 'user');
        @endphp
        @livewire("{$role}-navigation-menu")
        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('modals')
    @stack('scripts')
    @stack('captured_image')
    @livewireScripts
    <!-- Digital Clock -->
    <div id="digital-clock" class="fixed bottom-0 left-0 m-4 text-gray-900 dark:text-gray-100"></div>
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