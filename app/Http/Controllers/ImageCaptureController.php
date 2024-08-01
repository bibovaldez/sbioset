<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ImageCaptureController extends Controller
{
    protected $imageRecognitionController;
    protected $encryptionController;

    public function __construct(
        ImageRecognitionController $imageRecognitionController,
        EncryptionController $encryptionController
    ) {
        $this->imageRecognitionController = $imageRecognitionController;
        $this->encryptionController = $encryptionController;
    }

    public function upload(Request $request)
    {
        try {
            $this->validateImage($request);

            $uploadedFile = $request->file('image');
            $imageData = $uploadedFile->get();

            $recognitionResult = $this->imageRecognitionController->processImage($uploadedFile);


            if (!empty($recognitionResult['predictions'])) {
                $this->encryptAndStoreImage($imageData, $recognitionResult);
                return response()->json(['message' => 'Image processed successfully'], 201);
            } else {
                return response()->json(['message' => 'No predictions found'], 404);
            }
        } catch (ValidationException $e) {
            dd($e->errors());
            return $this->handleValidationException($e);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['error' => 'Image upload failed'], 500);
        }
    }

    protected function validateImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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
    protected function handleValidationException(ValidationException $e)
    {
        $errors = $e->validator->errors();
        return response()->json(['message' => $errors->first()], $errors->first());
    }
}
