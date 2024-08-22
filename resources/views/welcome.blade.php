<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bioset</title>
    <!-- Tab Icon svg icon.svg -->
    <link rel="icon" href="{{ asset('icon.ico') }}" type="image/x-icon" />    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom shapes and gradients */
        .custom-shape-divider {
            position: relative;
            overflow: hidden;
            width: 100%;
            height: 500px;
        }

        .custom-shape-divider::before {
            content: '';
            display: block;
            width: 100%;
            height: 500px;
            background: linear-gradient(90deg, rgb(2, 43, 155) 0%, rgb(144, 101, 243) 100%);
            clip-path: polygon(0 0, 100% 0, 100% 80%, 0 100%);
            position: absolute;
            top: 0;
            left: 0;
            z-index: -1;
        }
    </style>
</head>

<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white font-sans">

    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 p-4 flex justify-between items-center">
        <div class="flex items-center">
            <!-- Logo Placeholder -->
            <div class=" absolute shrink-0 flex items-center">
                <a href="{{ url('/') }}" class="flex items-center">
                    <x-application-mark class="block h-9 w-auto" />
                    <h1 class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 ml-2">Bioset</h1>
                </a>
            </div>
        </div>
        @if (Route::has('login'))
            <nav class="-mx-3 flex flex-1 justify-end">
                <button id="theme-toggle"
                    class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 mx-2">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                            fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                </button>
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="rounded-md px-3 py-2 text-xl text-indigo-600 dark:text-indigo-400 ring-1 font-semibold ring-transparent transition hover:text-black/70 dark:hover:text-white/80 focus:outline-none focus-visible:ring-[#FF2D20] dark:focus-visible:ring-white">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="rounded-md px-3 py-2 text-xl text-indigo-600 dark:text-indigo-400 font-semibold ring-1 ring-transparent transition hover:text-black/70 dark:hover:text-white/80 focus:outline-none focus-visible:ring-[#FF2D20] dark:focus-visible:ring-white">
                        {{ __('Log in') }}
                    </a>
                @endauth
            </nav>
        @endif
    </header>

    <!-- Main Content -->
    <main class="relative custom-shape-divider text-white dark:text-gray-100 py-24 px-4 lg:px-0">
  
        <div class="max-w-7xl mx-auto mt-[-50px] flex flex-col lg:flex-row items-center">
            <!-- Left Section: Text -->
            <div class="lg:w-1/2 px-4 lg:px-8 mb-10 lg:mb-0 text-center lg:text-left">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-6 leading-tight">Secured Bioset:</h2>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl mb-6 leading-snug">An application of Extended-Nonce
                    ChaCha20-Poly1305 Algorithm in Chicken Disease Classification System</h2>
            </div>

            <!-- Right Section: Image Placeholder with "Protected" Text -->
            <div class="relative w-full lg:w-1/2 px-4 lg:px-8">
                <div class="grid grid-cols-8 gap-1">
                    <!-- Placeholder grid for images -->
                    <div class="col-span-8 h-64 bg-gray-200 dark:bg-gray-700"></div>
                </div>
                <div class="aspect-w-16 aspect-h-9 bg-gray-200 dark:bg-gray-700">
                    <div class="absolute inset-0 bg-black bg-opacity-50 flex justify-center items-center">
                        <span class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white">PROTECTED</span>
                        <div
                            class="ml-4 w-8 h-8 sm:w-10 sm:h-10 lg:w-14 lg:h-14 bg-gray-300 dark:bg-gray-600 rounded-full">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer: Developer Profiles -->
    <footer class="relative bg-white dark:bg-gray-900 py-5">
        <div
            class="max-w-7xl mx-auto flex flex-col items-center lg:flex-row lg:justify-center gap-6 lg:gap-10 px-4 lg:px-0 lg:mt-[-150px]">
            <!-- Developer 1 -->
            <div class="w-full max-w-sm bg-gray-100 dark:bg-gray-700 rounded-lg shadow-lg p-4 flex items-center">
                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-full mr-4"></div>
                <div>
                    <p class="font-bold text-lg text-gray-900 dark:text-white">John Rey Valdez Jr</p>
                    <p class="text-gray-500 dark:text-gray-300">@Developer</p>
                </div>
            </div>
            <!-- Developer 2 -->
            <div class="w-full max-w-sm bg-gray-100 dark:bg-gray-700 rounded-lg shadow-lg p-4 flex items-center">
                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-full mr-4"></div>
                <div>
                    <p class="font-bold text-lg text-gray-900 dark:text-white">Donna Shane Ventura</p>
                    <p class="text-gray-500 dark:text-gray-300">@Developer</p>
                </div>
            </div>
            <!-- Developer 3 -->
            <div class="w-full max-w-sm bg-gray-100 dark:bg-gray-700 rounded-lg shadow-lg p-4 flex items-center">
                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-full mr-4"></div>
                <div>
                    <p class="font-bold text-lg text-gray-900 dark:text-white">Jaymar Chavez</p>
                    <p class="text-gray-500 dark:text-gray-300">@Developer</p>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
