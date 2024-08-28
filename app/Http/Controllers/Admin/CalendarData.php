<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
class CalendarData extends Controller
{
    public function show(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $firstDayOfWeek = $startOfMonth->dayOfWeek; // 0 (Sunday) - 6 (Saturday)

        $calendarDays = [];

        // Padding blank days before the start of the month
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $calendarDays[] = null;
        }

        // Adding the days of the month to the calendar array
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $calendarDays[] = $date->copy();
        }

        // Sample events
        $events = [
            '2024-08-24' => ['TC:209', 'TCU:21', 'TCH:20'],
            '2024-08-25' => ['TC:209', 'TCU:21', 'TCH:20'],
            // Add more events as necessary
        ];

        return view('Admin.admin-calendar', [
            'calendarDays' => $calendarDays,
            'events' => $events,
            'month' => $month,
            'year' => $year,
        ]);
    }
}