<div class="rounded-lg shadow-sm bg-white dark:bg-gray-800">
    <nav class="flex space-x-2 p-2">
        <x-nav-link href="{{ route('admin.dashboard-poultry') }}" :active="request()->routeIs('admin.dashboard-poultry')"
            class="px-6 py-3 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100">
            Poultry
        </x-nav-link>
        <x-nav-link href="{{ route('admin.dashboard-piggery') }}" :active="request()->routeIs('admin.dashboard-piggery')"
            class="px-6 py-3 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100">
            Piggery
        </x-nav-link>
        <x-nav-link href="{{ route('admin.dashboard-feeds') }}" :active="request()->routeIs('admin.dashboard-feeds')"
            class="px-6 py-3 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100">
            Feeds
        </x-nav-link>
        <x-nav-link href="{{ route('admin.dashboard-medicine') }}" :active="request()->routeIs('admin.dashboard-medicine')"
            class="px-6 py-3 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100">
            Medicine
        </x-nav-link>
    </nav>
</div>
