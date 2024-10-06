<?php

namespace App\Http\Controllers;

use App\Models\recentUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ChickenCounterController;
use App\Http\Controllers\EncryptionController;
use App\Http\Controllers\ImageRecognitionController;
use App\Rules\Recaptcha;
use App\Http\Controllers\SaveImageResultController;
use Illuminate\Support\Facades\Log;
use App\Notifications\ActivityNotification;
use Illuminate\Support\Facades\Notification;

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
                // Save the image result
                $this->saveImageResultController->saveImageResult($imageData, $recognitionResult);

                // Upadte recent uploads
                $this->updateRecentUploads($recognitionResult);

                // Log the activity
                $this->LogActivity($recognitionResult);
                return response()->json(['message' => 'Image processed successfully'], 201);
            } else {
                return response()->json(['message' => 'No predictions found'], 404);
            }
        } catch (ValidationException $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
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

        // email notification to admin
        $subject = 'Image Upload Activity';
        $message = sprintf(
            "User Information:\n\n- User ID: %d\n- Name: %s\n- Email: %s\n\nAction:\n\n- Uploaded Image ID: %s\n- Date Uploaded: %s",
            Auth::id(),
            Auth::user()->name,
            Auth::user()->email,
            $recognitionResult['inference_id'],
            now()->toDateTimeString()
        );


        Notification::route('mail', env('ADMIN_EMAIL'))
            ->notify(new ActivityNotification($subject, $message));
    }

    protected function updateRecentUploads($recognitionResult)
    {
        // use the model to update the recent uploads
        $recentUpload = recentUploads::create([
            'image_id' => $recognitionResult['inference_id'],
            'user_id' => Auth::id(),
            'team_id' => Auth::user()->current_team_id,
        ]);
    }
}
