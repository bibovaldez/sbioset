<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CalendarDataService;

class CalendarController extends Controller
{
    protected $calendarDataService;

    public function __construct(CalendarDataService $calendarDataService)
    {
        $this->calendarDataService = $calendarDataService;
    }

    public function show(Request $request)
    {
        // dd($request->input('year'));
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
    
        $calendarData = $this->calendarDataService->getMonthData($year, $month);

        return view('Admin.admin-calendar', [
            'calendarDays' => $calendarData['calendarDays'],
            'data' => $calendarData['events'],
            'month' => $month,
            'year' => $year,
        ]);
    }
}