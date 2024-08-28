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
        // dd($request->all());  -> "date" => "2024-08-13"
        $year = $request -> input('year', date('Y'));
        $month = $request -> input('month', date('m'));
    // dd($year, $month);
        $calendarData = $this->calendarDataService->getMonthData($year, $month);

        return view('Admin.admin-calendar', [
            'calendarDays' => $calendarData['calendarDays'],
            'data' => $calendarData['events'],
            'month' => $month,
            'year' => $year,
        ]);
    }
}