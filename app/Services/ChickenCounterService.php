<?php

namespace App\Services;

use App\Http\Controllers\DecryptionController;
use App\Http\Controllers\EncryptionController;

class ChickenCounterService
{
    protected $encryptionController;
    protected $decryptionController;

    public function __construct(
        EncryptionController $encryptionController,
        DecryptionController $decryptionController
    ) {
        $this->encryptionController = $encryptionController;
        $this->decryptionController = $decryptionController;
    }

    public function countChickens(array $recognitionResult): array
    {
        $counts = [
            'total_chicken' => count($recognitionResult['predictions']),
            'total_healthy_chicken' => 0,
            'total_unhealthy_chicken' => 0,
            'total_unknown_chicken' => 0
        ];

        foreach ($recognitionResult['predictions'] as $prediction) {
            switch ($prediction['class']) {
                case 'Healthy':
                    $counts['total_healthy_chicken']++;
                    break;
                case 'Unhealthy':
                    $counts['total_unhealthy_chicken']++;
                    break;
                case 'unknown':
                    $counts['total_unknown_chicken']++;
                    break;
            }
        }

        return $counts;
    }

    public function updateCounterData($model, array $newCounts): array
    {
        if ($model->exists) {
            $decryptedData = $this->decryptChickenCounterData($model);
            $updatedData = [];
            foreach ($decryptedData as $key => $value) {
                $updatedData[$key] = $value + $newCounts[$key];
            }

            return $this->encryptChickenCounterData($updatedData);
        }
        return $this->encryptChickenCounterData($newCounts);
    }

    protected function decryptChickenCounterData($data): array
    {
        $decryptedData = [];
        foreach (['total_chicken', 'total_healthy_chicken', 'total_unhealthy_chicken', 'total_unknown_chicken'] as $field) {
            $decryptedData[$field] = $this->decryptionController->decryptData($data->$field ?? 0);
        }
        return $decryptedData;
    }

    protected function encryptChickenCounterData(array $data): array
    {
        $encryptedData = [];
        foreach ($data as $key => $value) {
            $encryptedData[$key] = $this->encryptionController->encryptData($value);
        }
        return $encryptedData;
    }
}
