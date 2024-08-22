<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\User;
use App\Models\chicken_counter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Rules\Recaptcha;
use Illuminate\Support\Facades\DB;


class ImageCaptureController extends Controller
{
    protected $imageRecognitionController;
    protected $encryptionController;
    protected $decryptionController;

    public function __construct(
        ImageRecognitionController $imageRecognitionController,
        EncryptionController $encryptionController,
        DecryptionController $decryptionController
    ) {
        $this->imageRecognitionController = $imageRecognitionController;
        $this->encryptionController = $encryptionController;
        $this->decryptionController = $decryptionController;
    }

    public function upload(Request $request)
    {
        try {
            $this->validateImage($request); // Validate the image

            $uploadedFile = $request->file('image'); // Get the uploaded file
            $imageData = $uploadedFile->get(); // Get the image data

            $recognitionResult = $this->imageRecognitionController->processImage($uploadedFile); // Process the image

            // Update the chicken counter
            $this->updateChickenCounter($recognitionResult);
            // Check if predictions are found
            if (!empty($recognitionResult['predictions'])) {
                $this->encryptAndStoreImage($imageData, $recognitionResult);
                return response()->json(['message' => 'Image processed successfully'], 201);
            } else {
                return response()->json(['message' => 'No predictions found'], 404);
            }
        } catch (ValidationException $e) {
            dd($e);
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Image upload failed'], 500);
        }
    }

    protected function validateImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            // recaptcha validation
            // 'recaptcha_token' => ['required', new Recaptcha],
        ], [
            'image.required' => 202,
            'image.image' => 203,
            'image.mimes' => 204,
            'image.max' => 413,
        ]);
    }
    protected function encryptAndStoreImage($imageData, $recognitionResult)
    {
        $encryptedImage = $this->encryptionController->encryptData($imageData);
        $encryptedRecognitionResult = $this->encryptionController->encryptData(json_encode($recognitionResult));
        Image::create([
            'user_id' => Auth::id(),
            'team_id' => Auth::user()->currentTeam->id,
            'encrypted_image' => $encryptedImage,
            'recognition_result_encrypted' => $encryptedRecognitionResult,
        ]);
    }

    // Update the chicken counter
    protected function updateChickenCounter($recognitionResult)
    {
        // count how many predictions are found
        $totalChicken = count($recognitionResult['predictions']);
        // count how many predictions are healthy
        $totalHealthyChicken = count(array_filter($recognitionResult['predictions'], function ($prediction) {
            return $prediction['class'] === 'Healthy';
        }));
        // count how many predictions are unhealthy
        $totalUnhealthyChicken = count(array_filter($recognitionResult['predictions'], function ($prediction) {
            return $prediction['class'] === 'Unhealthy';
        }));
        // count how many predictions are unknown
        $totalUnknownChicken = count(array_filter($recognitionResult['predictions'], function ($prediction) {
            return $prediction['class'] === 'unknown';
        }));

        // Get the current chicken counter data
        $currentCounter = chicken_counter::where('team_id', Auth::user()->currentTeam->id)->first();
        
        if ($currentCounter) {
            // If data exists, decrypt, add new counts, and encrypt
            $decryptedData = $this->decryptChickenCounterData($currentCounter);
            $updatedData = [
                'total_chicken' => $decryptedData['total_chicken'] + $totalChicken,
                'total_healthy_chicken' => $decryptedData['total_healthy_chicken'] + $totalHealthyChicken,
                'total_unhealthy_chicken' => $decryptedData['total_unhealthy_chicken'] + $totalUnhealthyChicken,
                'total_unknown_chicken' => $decryptedData['total_unknown_chicken'] + $totalUnknownChicken,
            ];

            $encryptedData = $this->encryptChickenCounterData($updatedData);
        } else {
            // If no data exists, encrypt the new counts
            $encryptedData = $this->encryptChickenCounterData([
                'total_chicken' => $totalChicken,
                'total_healthy_chicken' => $totalHealthyChicken,
                'total_unhealthy_chicken' => $totalUnhealthyChicken,
                'total_unknown_chicken' => $totalUnknownChicken,
            ]);
            
        }

        // Update or create the chicken counter with encrypted data
        chicken_counter::updateOrCreate(
            ['team_id' => Auth::user()->currentTeam->id],
            $encryptedData
        );
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

    protected function encryptChickenCounterData($data)
    {
        return [
            'total_chicken' => $this->encryptionController->encryptData($data['total_chicken']),
            'total_healthy_chicken' => $this->encryptionController->encryptData($data['total_healthy_chicken']),
            'total_unhealthy_chicken' => $this->encryptionController->encryptData($data['total_unhealthy_chicken']),
            'total_unknown_chicken' => $this->encryptionController->encryptData($data['total_unknown_chicken']),
        ];
    }

    protected function handleValidationException(ValidationException $e)
    {
        $errors = $e->validator->errors();
        return response()->json(['message' => $errors->first()], $errors->first());
    }
}
