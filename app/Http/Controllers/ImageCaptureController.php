<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ChickenCounterController;



class ImageCaptureController extends Controller
{
    protected $imageRecognitionController;
    protected $encryptionController;
    protected $chickenCounterController;

    public function __construct(
        ImageRecognitionController $imageRecognitionController,
        EncryptionController $encryptionController,
        ChickenCounterController $chickenCounterController

    ) {
        $this->imageRecognitionController = $imageRecognitionController;
        $this->encryptionController = $encryptionController;
        $this->chickenCounterController = $chickenCounterController;
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
}
