<x-app-layout>
    <!-- Page Content -->
    <div class="flex-1 overflow-y-auto">
        <main class="max-w-7xl mx-auto py-1 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="border-4 border-dashed border-gray-200 rounded-lg p-4 dark:border-gray-700">
                    <h2 class="text-xl font-semibold mb-4 dark:text-gray-100">Poultry Details / Information</h2>
                    {{-- Stats --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        @php
                            $stats = [
                                ['title' => 'Total Chicken', 'value' => $dashboardData['overallData']['total_chicken']],
                                [
                                    'title' => 'Healthy Chickens',
                                    'value' => $dashboardData['overallData']['total_healthy_chicken'],
                                ],
                                [
                                    'title' => 'Unhealthy Chicken',
                                    'value' => $dashboardData['overallData']['total_unhealthy_chicken'],
                                ],
                                [
                                    'title' => 'Unknown Chicken',
                                    'value' => $dashboardData['overallData']['total_unknown_chicken'],
                                ],
                                
                            ];
                        @endphp

                        @foreach ($stats as $stat)
                            <div
                                class="bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                                <div class="px-4 py-5 sm:p-6">
                                    <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                                        {{ $stat['title'] }}
                                    </dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $stat['value'] }}
                                    </dd>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Chart --}}
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 max-sm:grid-cols-1">
                        <div
                            class="bg-white p-4 rounded-lg shadow dark:bg-gray-800/50 dark:text-gray-100 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5  shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.02] transition-all duration-250 focus:outline focus:outline-2">
                            <canvas id="monthlyDataChart"></canvas>
                        </div>
                        <div
                            class="bg-white p-4 rounded-lg shadow dark:bg-gray-800/50 dark:text-gray-100 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5  shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.02] transition-all duration-250 focus:outline focus:outline-2">
                            <canvas id="dailyDataChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script >
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
</x-app-layout>
