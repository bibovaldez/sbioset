<x-admin-dashboard-layout>
    <div class="flex-1 overflow-y-auto">
        <main class="mx-auto py-1 sm:px-1 lg:px-2">
            <div class="px-1 py-4 sm:px-0">
                <div class=" rounded-lg  dark:border-gray-700">
                    {{-- Stats --}}
                    <div class="grid grid-cols-4 gap-2 mb-2">
                        @php
                            $stats = [
                                [
                                    'title' => 'Total Chicken',
                                    'value' => $dashboardData['overallData']['total_chicken'],
                                    'icon' => '💰', // Add an icon
                                    'percentage' => '+10% from last month', // Optional percentage data
                                ],
                                [
                                    'title' => 'Healthy Chickens',
                                    'value' => $dashboardData['overallData']['total_healthy_chicken'],
                                    'icon' => '🐔',
                                    'percentage' => '+5% from last month',
                                ],
                                [
                                    'title' => 'Unhealthy Chicken',
                                    'value' => $dashboardData['overallData']['total_unhealthy_chicken'],
                                    'icon' => '⚠️',
                                    'percentage' => '-2% from last month',
                                ],
                                [
                                    'title' => 'Unknown Chicken',
                                    'value' => $dashboardData['overallData']['total_unknown_chicken'],
                                    'icon' => '❓',
                                    'percentage' => '0% from last month',
                                ],
                            ];
                        @endphp

                        @foreach ($stats as $stat)
                            <div
                                class="bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                                <div class="px-4 py-4 sm:p-3">
                                    <!-- Title with icon -->
                                    <dt
                                        class="text-sm font-medium text-gray-500 truncate dark:text-gray-400 flex items-center">
                                        <span class="mr-1">{{ $stat['icon'] }}</span>
                                        {{ $stat['title'] }}
                                    </dt>
                                    <!-- Value -->
                                    <dd class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $stat['value'] }}
                                    </dd>
                                    <!-- Percentage change -->
                                    <dd class="text-sm text-green-500 dark:text-green-400">
                                        {{ $stat['percentage'] }}
                                    </dd>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Chart --}}
                    <div class="mt-1 grid grid-cols-1 md:grid-cols-2 gap-2 max-sm:grid-cols-1">
                        <div
                            class="bg-white p-4 rounded-lg shadow dark:bg-gray-800/50 dark:text-gray-100 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5  shadow-gray-500/20 dark:shadow-none flex  focus:outline focus:outline-2">
                            <canvas id="monthlyDataChart"></canvas>
                        </div>
                        <div
                            class="bg-white p-4 rounded-lg shadow dark:bg-gray-800/50 dark:text-gray-100 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5  shadow-gray-500/20 dark:shadow-none flex  focus:outline focus:outline-2">
                            <canvas id="dailyDataChart"></canvas>
                        </div>
                    </div>
                    {{-- Recent upload--}}
                    {{-- <div class="mt-1 bg-white p-4 rounded-lg shadow dark:bg-gray-800/50 dark:text-gray-100 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5  shadow-gray-500/20 dark:shadow-none flex  focus:outline focus:outline-2">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Uploads</h2>
                        <div class="mt-2">
                            <div class="overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead>
                                        <tr
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700">
                                            <th class="px-4 py-2">ID</th>
                                            <th class="px-4 py-2">Chicken ID</th>
                                            <th class="px-4 py-2">Status</th>
                                            <th class="px-4 py-2">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm divide-y dark:divide-gray-700 dark:divide-opacity-50">
                                        @foreach ($dashboardData['recentUploads'] as $upload)
                                            <tr class="text-gray-700 dark:text-gray-100">
                                                <td class="px-4 py-2">{{ $upload->id }}</td>
                                                <td class="px-4 py-2">{{ $upload->chicken_id }}</td>
                                                <td class="px-4 py-2">{{ $upload->status }}</td>
                                                <td class="px-4 py-2">{{ $upload->created_at->format('d M Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div> --}}
        </main>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const monthlyData = @json($dashboardData['monthlyData']);
            const dailyData = @json($dashboardData['dailyData']);

            const ctxMonthly = document.getElementById('monthlyDataChart').getContext('2d');
            const ctxDaily = document.getElementById('dailyDataChart').getContext('2d');

            const monthlyDataChart = new Chart(ctxMonthly, {
                type: 'bar',
                data: {
                    labels: ['Healthy Chicken', 'Unhealthy Chicken', 'Unknown Chicken'],
                    datasets: [{
                        label: 'Monthly Data',
                        data: [
                            monthlyData.total_healthy_chicken,
                            monthlyData.total_unhealthy_chicken,
                            monthlyData.total_unknown_chicken
                        ],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(75, 192, 192, 0.5)',
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(201, 203, 207, 0.5)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(201, 203, 207, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Monthly Data'
                        }
                    }
                }
            });

            const dailyDataChart = new Chart(ctxDaily, {
                type: 'bar',
                data: {
                    labels: ['Healthy Chicken', 'Unhealthy Chicken', 'Unknown Chicken'],
                    datasets: [{
                        label: 'Daily Data',
                        data: [
                            dailyData.total_healthy_chicken,
                            dailyData.total_unhealthy_chicken,
                            dailyData.total_unknown_chicken
                        ],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(75, 192, 192, 0.5)',
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(201, 203, 207, 0.5)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(201, 203, 207, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Daily Data'
                        }
                    }
                }
            });
        </script>
    </div>
</x-admin-dashboard-layout>
