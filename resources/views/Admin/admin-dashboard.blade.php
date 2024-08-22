<x-app-layout>
    <!-- Page Content -->
    <div class="flex-1 overflow-y-auto">
        <main class="max-w-7xl mx-auto py-1 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="border-4 border-dashed border-gray-200 rounded-lg p-4 dark:border-gray-700">
                    <h2 class="text-xl font-semibold mb-4 dark:text-gray-100">Poultry Details / Information</h2>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        @php
                            $stats = [
                                ['title' => 'Total Chicken', 'value' => 210],
                                ['title' => 'Healthy Chickens', 'value' => 200],
                                ['title' => 'Unhealthy Chicken', 'value' => 10],
                                ['title' => 'Unkown Chicken', 'value' => 0],
                            ];
                        @endphp

                        @foreach ($stats as $stat)
                            <div class="  bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500"">
                                <div class="px-4 py-5 sm:p-6">
                                    <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400 ">
                                        {{ $stat['title'] }}
                                    </dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $stat['value'] }}
                                    </dd>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        <!-- Placeholder for the chart -->
                        <div class="bg-white p-4 rounded-lg shadow">
                            <p class="text-center text-gray-500">Chart placeholder</p>
                            <!-- You would integrate your actual chart library here -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

</x-app-layout>
