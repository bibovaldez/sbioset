<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg dark:bg-gray-800/50 dark:text-gray-100 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-gray-500/20 dark:shadow-none">
                <section class="container mx-auto bg-white p-8 rounded-lg shadow-sm dark:bg-gray-800/50 dark:text-gray-100 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 shadow-gray-500/20 dark:shadow-none">
                    <div class="mb-4">
                        <h1 class="text-2xl font-bold mb-4 text-center">
                            {{ Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                        </h1>
                        {{-- Selecting dates --}}
                        <form id="calendar-form" action="{{ route('admin.calendar') }}" method="get" class="flex flex-col sm:flex-row sm:items-center sm:justify-center">
                            <div class="flex mb-4 sm:mb-0 sm:mr-4">
                                <label for="month" class="mr-2">Select Month:</label>
                                <select id="month" name="month" class="border rounded px-2 py-1">
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}" @if ($m == $month) selected @endif>
                                            {{ Carbon\Carbon::create(null, $m, 1)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex">
                                <label for="year" class="mr-2">Select Year:</label>
                                <select id="year" name="year" class="border rounded py-1">
                                    @foreach (range(2021, 2030) as $y)
                                        <option value="{{ $y }}" @if ($y == $year) selected @endif>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
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
                                @foreach (array_chunk($calendarDays, 7) as $week)
                                    <tr class="text-center">
                                        @foreach ($week as $day)
                                            <td class="p-2 border border-gray-300 align-top h-32">
                                                @if ($day)
                                                    @php
                                                        $dataForDay = collect($data)->firstWhere('date', $day->format('Y-m-d'));
                                                    @endphp
                                                    <div class="font-bold text-gray-900 dark:text-gray-100">
                                                        {{ $day->format('j') }}
                                                    </div>
                                                    @if ($dataForDay)
                                                        <div class="mt-1 text-xs">
                                                            <div class="dark:text-gray-400">Total: {{ $dataForDay['total_chicken'] }}</div>
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
                    </div>
                </section>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('month').addEventListener('change', function() {
            document.getElementById('calendar-form').submit();
        });

        document.getElementById('year').addEventListener('change', function() {
            document.getElementById('calendar-form').submit();
        });
    </script>
</x-app-layout>
