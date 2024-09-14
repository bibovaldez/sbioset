<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <section class="container mx-auto p-6">
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold mb-4 text-center text-gray-800 dark:text-white">
                            {{ Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                        </h1>
                        <form id="calendar-form" action="{{ route('admin.calendar') }}" method="get"
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                            @csrf
                            @honeypot
                            <div class="flex items-center">
                                <label for="month" class="mr-2 text-gray-700 dark:text-gray-300">Month:</label>
                                <select id="month" name="month"
                                    class="form-select rounded-md shadow-sm mt-1 block w-full">
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}"
                                            @if ($m == $month) selected @endif>
                                            {{ Carbon\Carbon::create(null, $m, 1)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center">
                                <label for="year" class="mr-2 text-gray-700 dark:text-gray-300">Year:</label>
                                <select id="year" name="year"
                                    class="form-select rounded-md shadow-sm mt-1 block w-full">
                                    @php
                                        $currentYear = date('Y');
                                    @endphp

                                    @foreach (range($currentYear, $currentYear - 8) as $y)
                                        <option value="{{ $y }}"
                                            @if ($y == $year) selected @endif>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="text-center bg-gray-100 dark:bg-gray-700">
                                    @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                                        <th
                                            class="border border-gray-200 dark:border-gray-600 py-2 px-1 text-sm font-medium {{ $day === 'Sun' ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $day }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (array_chunk($calendarDays, 7) as $week)
                                    <tr class="text-center">
                                        @foreach ($week as $day)
                                            <td
                                                class="border border-gray-200 dark:border-gray-600 p-1 h-24 sm:h-32 align-top {{ !$day ? 'bg-gray-50 dark:bg-gray-800' : '' }}">
                                                @if ($day)
                                                    @php
                                                        $dataForDay = collect($data)->firstWhere(
                                                            'date',
                                                            $day->format('Y-m-d'),
                                                        );
                                                        $isToday = $day->isToday();
                                                    @endphp
                                                    <div class="flex flex-col h-full">
                                                        <div
                                                            class="text-sm font-semibold mb-1 {{ $isToday ? 'bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center mx-auto' : 'text-gray-700 dark:text-gray-300' }}">
                                                            {{ $day->format('j') }}
                                                        </div>
                                                        @if ($dataForDay)
                                                            <div class="flex-grow flex flex-col justify-center text-xs">
                                                                <div class="text-gray-600 dark:text-gray-400">Total:
                                                                    {{ $dataForDay['total_chicken'] }}</div>
                                                                <div class="text-green-600 dark:text-green-400">Healthy:
                                                                    {{ $dataForDay['total_healthy_chicken'] }}</div>
                                                                <div class="text-red-600 dark:text-red-400">Unhealthy:
                                                                    {{ $dataForDay['total_unhealthy_chicken'] }}</div>
                                                                <div class="text-gray-500 dark:text-gray-500">Unknown:
                                                                    {{ $dataForDay['total_unknown_chicken'] }}</div>
                                                            </div>
                                                        @endif
                                                    </div>
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
        document.getElementById('month').addEventListener('change', () => document.getElementById('calendar-form')
            .submit());
        document.getElementById('year').addEventListener('change', () => document.getElementById('calendar-form').submit());
    </script>
</x-app-layout>
