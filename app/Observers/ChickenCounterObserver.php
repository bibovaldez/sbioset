<?php

namespace App\Observers;

use App\Models\ChickenCounter;
use App\Models\CalendarData;
use App\Http\Controllers\DecryptionController;
use App\Http\Controllers\EncryptionController;

class ChickenCounterObserver
{
    protected $decryptionController;
    protected $encryptionController;

    public function __construct(
        EncryptionController $encryptionController,
        DecryptionController $decryptionController
    ) {
        $this->decryptionController = $decryptionController;
        $this->encryptionController = $encryptionController;
    }
    public function updated(ChickenCounter $chickenCounter)
    {
        $date = $chickenCounter->updated_at->toDateString();
        $teamId = $chickenCounter->team_id;


        // Find or create CalendarData for this specific date and team
        $calendarData = CalendarData::where('team_id', $teamId)
            ->whereDate('created_at', $date)
            ->first();

        // dd($date, $teamId, $calendarData->created_at->toDateString(), $calendarData->exists);

        if ($calendarData->exists) {
            // If data exists for today, update it
            $decryptedCalendarData = $this->decryptChickenCounterData($calendarData);
            $decryptedChickenCounter = $this->decryptChickenCounterData($chickenCounter);

            $updatedData = [
                'total_chicken' => $decryptedChickenCounter['total_chicken'] - $decryptedCalendarData['total_chicken'],
                'total_healthy_chicken' => $decryptedChickenCounter['total_healthy_chicken'] - $decryptedCalendarData['total_healthy_chicken'],
                'total_unhealthy_chicken' => $decryptedChickenCounter['total_unhealthy_chicken'] - $decryptedCalendarData['total_unhealthy_chicken'],
                'total_unknown_chicken' => $decryptedChickenCounter['total_unknown_chicken'] - $decryptedCalendarData['total_unknown_chicken'],
            ];

            // Ensure no negative values
            foreach ($updatedData as &$value) {
                $value = max(0, $value);
            }

            $encryptedData = $this->encryptChickenCounterData($updatedData);
        } else {
            // If no data exists for today, use the current ChickenCounter data
            $encryptedData = [
                'total_chicken' => $chickenCounter->total_chicken,
                'total_healthy_chicken' => $chickenCounter->total_healthy_chicken,
                'total_unhealthy_chicken' => $chickenCounter->total_unhealthy_chicken,
                'total_unknown_chicken' => $chickenCounter->total_unknown_chicken,
            ];
        }

        // Update or create the CalendarData
        $calendarData->fill($encryptedData);
        $calendarData->save();
        $decryptedCalendarData = $this->decryptChickenCounterData($calendarData);
        dd($decryptedCalendarData);
    }
    protected function encryptChickenCounterData($data)
    {
        return [
            'total_chicken' => $this->encryptionController->encryptData($data['total_chicken']),
            'total_healthy_chicken' => $this->encryptionController->encryptData($data['total_healthy_chicken']),
            'total_unhealthy_chicken' => $this->encryptionController->encryptData($data['total_unhealthy_chicken']),
            'total_unknown_chicken' => $this->encryptionController->encryptData($data['total_unknown_chicken']),
        ];
    }
    protected function decryptChickenCounterData($data)
    {
        return [
            'total_chicken' => $this->decryptionController->decryptData($data->total_chicken),
            'total_healthy_chicken' => $this->decryptionController->decryptData($data->total_healthy_chicken),
            'total_unhealthy_chicken' => $this->decryptionController->decryptData($data->total_unhealthy_chicken),
            'total_unknown_chicken' => $this->decryptionController->decryptData($data->total_unknown_chicken),
        ];
    }
}
