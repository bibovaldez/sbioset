<x-app-layout>
    <!-- Page Content -->
    <div class="flex-1 overflow-y-auto">
        <div class="rounded-lg dark:border-gray-700">
            <x-dashboard-nav />
            {{-- slot holder {{ $slot }} --}}
            {{ $slot }}
        </div>
    </div>
</x-app-layout>
