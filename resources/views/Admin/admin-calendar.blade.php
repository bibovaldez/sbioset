<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg dark:bg-gray-800/50 dark:text-gray-100 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-gray-500/20 dark:shadow-none">
                <section class="container mx-auto bg-white p-8 rounded-lg shadow-sm dark:bg-gray-800/50 dark:text-gray-100 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5  shadow-gray-500/20 dark:shadow-none">
                    <div>
                        {{-- change month arrow--}}
                        
                        <h2 class="text-2xl font-bold mb-4 text-center">{{ Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</h2>
                    </div>
                    <table class="w-full table-fixed border-collapse border border-gray-300 dark:border-gray-700">
                        <thead>
                            <tr class="text-center">
                                <th class="border border-gray-300 py-4 font-medium text-gray-700 dark:text-gray-100">Mon</th>
                                <th class="border border-gray-300 py-4 font-medium text-gray-700 dark:text-gray-100">Tue</th>
                                <th class="border border-gray-300 py-4 font-medium text-gray-700 dark:text-gray-100">Wed</th>
                                <th class="border border-gray-300 py-4 font-medium text-gray-700 dark:text-gray-100">Thu</th>
                                <th class="border border-gray-300 py-4 font-medium text-gray-700 dark:text-gray-100">Fri</th>
                                <th class="border border-gray-300 py-4 font-medium text-gray-700 dark:text-gray-100">Sat</th>
                                <th class="border border-gray-300 py-4 font-medium text-red-600 dark:text-red-400">Sun</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_chunk($calendarDays, 7) as $week)
                                <tr class="text-center">
                                    @foreach($week as $day)
                                        <td class="p-2 border border-gray-300 align-top h-32">
                                            @if($day)
                                                @php
                                                    $dataForDay = collect($data)->firstWhere('date', $day->format('Y-m-d'));
                                                @endphp
                                                <div class="font-bold text-gray-900 dark:text-gray-100">
                                                    {{ $day->format('j') }}
                                                </div>
                                                @if($dataForDay)
                                                    <div class="mt-1 text-xs ">
                                                        <div class=" dark:text-gray-400">Total: {{ $dataForDay['total_chicken'] }}</div>
                                                        <div class="text-green-600">Healthy: {{ $dataForDay['total_healthy_chicken'] }}</div>
                                                        <div class="text-red-600">Unhealthy: {{ $dataForDay['total_unhealthy_chicken'] }}</div>
                                                        <div class="text-gray-500">Unknown: {{ $dataForDay['total_unknown_chicken'] }}</div>
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>