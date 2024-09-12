<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\CalendarData;
use App\Http\Controllers\DecryptionController;
use Illuminate\Support\Facades\Auth;

class CalendarDataService
{
    protected $decryptionController;
    protected $user;

    public function __construct(DecryptionController $decryptionController)
    {
        $this->decryptionController = $decryptionController;
        $this->user = Auth::user();
    }

    public function getMonthData($year, $month)
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();
        $firstDayOfWeek = $startOfMonth->dayOfWeek;

        $calendarDays = $this->generateCalendarDays($startOfMonth, $endOfMonth, $firstDayOfWeek);
        $events = $this->getEventsForMonth($startOfMonth, $endOfMonth);

        return [
            'calendarDays' => $calendarDays,
            'events' => $events,
        ];
    }

    protected function generateCalendarDays($startOfMonth, $endOfMonth, $firstDayOfWeek)
    {
        $calendarDays = array_fill(0, $firstDayOfWeek, null);

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $calendarDays[] = $date->copy();
        }

        return $calendarDays;
    }

    protected function getEventsForMonth($startOfMonth, $endOfMonth)
    {
        $team = $this->user->current_team_id;

        $calendarData = CalendarData::where('team_id', $team)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();
        $events = [];
        foreach ($calendarData as $data) {
            $events[] = [
                'date' => $data->created_at->toDateString(),
                'total_chicken' => $this->decryptionController->decryptData($data->total_chicken),
                'total_healthy_chicken' => $this->decryptionController->decryptData($data->total_healthy_chicken),
                'total_unhealthy_chicken' => $this->decryptionController->decryptData($data->total_unhealthy_chicken),
                'total_unknown_chicken' => $this->decryptionController->decryptData($data->total_unknown_chicken),
            ];
        }

        return $events;
    }
}
