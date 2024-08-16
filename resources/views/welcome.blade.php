<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bioset</title>
    {{-- Tab Icon svg icon.svg --}}
    <link rel="icon" href="{{ asset('icon.svg') }}" type="image" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom shapes and gradients */
        .custom-shape-divider {
            position: relative;
            overflow: hidden;
            width: 100%;
            height: 500px;
            /* Increased height */
        }

        .custom-shape-divider::before {
            content: '';
            display: block;
            width: 100%;
            height: 500px;
            /* Increased height */
            background: linear-gradient(90deg, rgb(2, 43, 155) 0%, rgb(106, 47, 244) 100%);
            clip-path: polygon(0 0, 100% 0, 100% 80%, 0 100%);
            /* Adjusted clip-path for a smoother curve */
            position: absolute;
            top: 0;
            left: 0;
            z-index: -1;
        }
    </style>
</head>

<body class="bg-white font-sans">

    <!-- Header -->
    <header class="bg-white p-4 flex justify-between items-center">
        <div class="flex items-center">
            <!-- Logo Placeholder -->
            <div class="w-12 h-12 bg-gray-300 rounded-full mr-2"></div>
            <h1 class="text-2xl font-bold text-indigo-600">BIOSET</h1>
        </div>
        @if (Route::has('login'))
            <nav class="-mx-3 flex flex-1 justify-end">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="rounded-md px-3 py-2 text-xl text-indigo-600 ring-1 font-semibold ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="rounded-md px-3 py-2 text-xl text-indigo-600 font-semibold ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                        {{ __('Log in') }}
                    </a>
                @endauth
            </nav>
        @endif
    </header>

    <!-- Main Content -->
    <main class="relative custom-shape-divider text-white py-24"> <!-- Increased padding for content -->
        <div class="max-w-7xl mx-auto mt-[-50px] flex flex-col lg:flex-row items-center">
            <!-- Left Section: Text -->
            <div class="lg:w-1/2 px-8 mb-10 lg:mb-0">
                <h2 class="text-5xl font-bold mb-6 leading-tight">Secured Bioset:</h2>
                <h2 class="text-4xl mb-6 leading-snug">An application of Extended-Nonce ChaCha20-Poly1305 Algorithm in
                    Chicken Disease Classification System</h2>
            </div>

            <!-- Right Section: Image Placeholder with "Protected" Text -->
            <div class="relative lg:w-1/2 px-8">
                <div class="grid grid-cols-8 gap-1">
                    <!-- Placeholder grid for images (64x64 grid) -->
                    <div class="col-span-8 h-64 bg-gray-200"></div>
                </div>
                <div class="absolute inset-0 bg-black bg-opacity-50 flex justify-center items-center">
                    <span class="text-5xl font-bold text-white">PROTECTED</span>
                    <div class="ml-4 w-14 h-14 bg-gray-300 rounded-full"></div> <!-- Placeholder for shield icon -->
                </div>
            </div>
        </div>
    </main>

    <!-- Footer: Developer Profiles -->
    <footer class="bg-white py-8 ">
        <div class="max-w-7xl mx-auto flex justify-center gap-28 mt-[-120px]">
            <div class="w-80 bg-gray-100 rounded-lg shadow-lg p-4 flex items-center">
                <div class="w-16 h-16 bg-gray-200 rounded-full mr-4"></div> <!-- Placeholder for image -->
                <div>
                    <p class="font-bold text-lg">John Rey Valdez Jr</p>
                    <p class="text-gray-500">@Developer</p>
                </div>
            </div>
            <!-- Developer 2 -->
            <div class="w-80 bg-gray-100 rounded-lg shadow-lg p-4 flex items-center">
                <div class="w-16 h-16 bg-gray-200 rounded-full mr-4"></div> <!-- Placeholder for image -->
                <div>
                    <p class="font-bold text-lg">Donna Shane Ventura</p>
                    <p class="text-gray-500">@Developer</p>
                </div>
            </div>
            <!-- Developer 3 -->
            <div class="w-80 bg-gray-100 rounded-lg shadow-lg p-4 flex items-center">
                <div class="w-16 h-16 bg-gray-200 rounded-full mr-4"></div> <!-- Placeholder for image -->
                <div>
                    <p class="font-bold text-lg">Jaymar Chavez</p>
                    <p class="text-gray-500">@Developer</p>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
