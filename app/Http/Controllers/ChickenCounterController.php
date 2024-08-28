<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\CalendarData;
use App\Models\ChickenCounter;
use Illuminate\Support\Facades\Auth;
use App\Services\ChickenCounterService;

class ChickenCounterController extends Controller
{
    protected $chickenCounterService;

    public function __construct(ChickenCounterService $chickenCounterService)
    {
        $this->chickenCounterService = $chickenCounterService;
    }

    public function updateChickenCounter($recognitionResult)
    {
        $counts = $this->chickenCounterService->countChickens($recognitionResult);
        
        $this->updateOverallCounter($counts);// Update the overall counter
        $this->updateCalendarData($counts);// Update the calendar data day by day

        return $counts;
    }

    protected function updateOverallCounter(array $counts)
    {
        $teamId = Auth::user()->currentTeam->id;
        $currentCounter = ChickenCounter::firstOrNew(['team_id' => $teamId]);

        $updatedData = $this->chickenCounterService->updateCounterData($currentCounter, $counts);
        
        $currentCounter->fill($updatedData);
        $currentCounter->save();
    }

    protected function updateCalendarData(array $counts)
    {
        $today = Carbon::today();
        $teamId = Auth::user()->currentTeam->id;

        $calendarData = CalendarData::firstOrNew([
            'team_id' => $teamId,
            'created_at' => $today,
        ]);

        $updatedData = $this->chickenCounterService->updateCounterData($calendarData, $counts);

        $calendarData->fill($updatedData);
        $calendarData->save();
    }
}