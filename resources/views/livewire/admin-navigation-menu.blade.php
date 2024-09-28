<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 w-full h-full">
    <div>
        <div class="flex flex-col justify-center h-full">
            <div class="mb-5 flex justify-center items-center">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>
            </div>
            <div class="flex flex-col space-y-4">
                <!-- Navigation Links -->
                <x-nav-link href="{{ route('admin.dashboard-poultry') }}" :active="request()->routeIs('admin.dashboard-poultry')"
                    class="w-full flex items-center px-12 py-2 text-xm font-medium 
                     transition-colors duration-150 ease-in-out text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    <span>{{ __('Dashboard') }}</span>
                </x-nav-link>
                <x-nav-link href="{{ route('admin.calendar') }}" :active="request()->routeIs('admin.calendar')"
                    class="flex items-center px-12 py-2 text-sm font-medium 
                     transition-colors duration-150 ease-in-out text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span>{{ __('Calendar') }}</span>
                </x-nav-link>
                <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')"
                    class="flex items-center px-12 py-2 text-sm font-medium 
                     transition-colors duration-150 ease-in-out text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span>{{ __('Upload') }}</span>
                </x-nav-link>
            </div>
        </div>
    </div>
</nav>