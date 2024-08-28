<?php

namespace App\Services;

use App\Models\ChickenCounter;
use App\Models\CalendarData;
use App\Http\Controllers\DecryptionController;
use Carbon\Carbon;

class DashboardService
{
    protected $decryptionController;

    public function __construct(DecryptionController $decryptionController)
    {
        $this->decryptionController = $decryptionController;
    }

    public function getDashboardData()
    {
        $overallData = $this->getOverallData();
        $monthlyData = $this->getMonthlyData();
        $dailyData = $this->getDailyData();

        // dd($overallData, $monthlyData, $dailyData);

        return [
            'overallData' => $overallData,
            'monthlyData' => $monthlyData,
            'dailyData' => $dailyData,
        ];
    }

    protected function getOverallData()
    {
        $chickenCounter = ChickenCounter::first();
        if (!$chickenCounter) {
            return $this->getEmptyCountData();
        }

        return $this->decryptCountData($chickenCounter);
    }

    protected function getMonthlyData()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return $this->getAggregatedData($startOfMonth, $endOfMonth);
    }

    protected function getDailyData()
    {
        $today = Carbon::today();

        return $this->getAggregatedData($today, $today->copy()->endOfDay());
    }

    protected function getAggregatedData($startDate, $endDate)
    {
        $calendarData = CalendarData::whereBetween('created_at', [$startDate, $endDate])->get();

        $aggregatedData = $this->getEmptyCountData();

        foreach ($calendarData as $data) {
            $decryptedData = $this->decryptCountData($data);
            foreach ($aggregatedData as $key => $value) {
                $aggregatedData[$key] += $decryptedData[$key];
            }
        }

        return $aggregatedData;
    }

    protected function decryptCountData($data)
    {
        return [
            'total_chicken' => $this->decryptionController->decryptData($data->total_chicken),
            'total_healthy_chicken' => $this->decryptionController->decryptData($data->total_healthy_chicken),
            'total_unhealthy_chicken' => $this->decryptionController->decryptData($data->total_unhealthy_chicken),
            'total_unknown_chicken' => $this->decryptionController->decryptData($data->total_unknown_chicken),
        ];
    }

    protected function getEmptyCountData()
    {
        return [
            'total_chicken' => 0,
            'total_healthy_chicken' => 0,
            'total_unhealthy_chicken' => 0,
            'total_unknown_chicken' => 0,
        ];
    }
}