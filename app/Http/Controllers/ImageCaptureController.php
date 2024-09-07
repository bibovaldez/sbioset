<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ChickenCounterController;
use App\Http\Controllers\EncryptionController;
use App\Http\Controllers\ImageRecognitionController;
use App\Rules\Recaptcha;
use App\Http\Controllers\SaveImageResultController;
use Illuminate\Support\Facades\Log;

class ImageCaptureController extends Controller
{
    protected $imageRecognitionController;
    protected $encryptionController;
    protected $chickenCounterController;
    protected $saveImageResultController;

    public function __construct(
        ImageRecognitionController $imageRecognitionController,
        EncryptionController $encryptionController,
        ChickenCounterController $chickenCounterController,
        SaveImageResultController $saveImageResultController

    ) {
        $this->imageRecognitionController = $imageRecognitionController;
        $this->encryptionController = $encryptionController;
        $this->chickenCounterController = $chickenCounterController;
        $this->saveImageResultController = $saveImageResultController;
    }

    public function upload(Request $request)
    {
        try {
            $this->validateImage($request); // Validate the image

            $uploadedFile = $request->file('image'); // Get the uploaded file
            $imageData = $uploadedFile->get(); // Get the image data

            $recognitionResult = $this->imageRecognitionController->processImage($uploadedFile); // Process the image
           
            // Check if predictions are found
            if (!empty($recognitionResult['predictions'])) {
                $this->updateChickenCounter($recognitionResult);
                $this->saveImageResultController->saveImageResult($imageData, $recognitionResult);
                $this->LogActivity($recognitionResult);
                return response()->json(['message' => 'Image processed successfully'], 201);
            } else {
                return response()->json(['message' => 'No predictions found'], 404);
            }
        } catch (ValidationException $e) {
    
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
  
            return response()->json(['error' => 'Image upload failed'], 500);
        }
    }

    protected function validateImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:6144', // Validate the image 
            'recaptcha_token' => ['required', new Recaptcha],
        ], [
            'image.required' => 202,
            'image.image' => 203,
            'image.mimes' => 204,
            'image.max' => 413,
        ]);
    }
  

    // Update the chicken counter overall count
    protected function updateChickenCounter($recognitionResult)
    {
        $this->chickenCounterController->updateChickenCounter($recognitionResult);
    }
    protected function handleValidationException(ValidationException $e)
    {
        $errors = $e->validator->errors();
        return response()->json(['message' => $errors->first()], $errors->first());
    }

    protected function LogActivity($recognitionResult)
    {
        Log::info('Image Upload Activity', [
            'user_id' => Auth::id(),
            'username' => Auth::user()->name,
            'user_email' => Auth::user()->email,
            'image_id' => $recognitionResult['inference_id'],
        ]);
    }
}
