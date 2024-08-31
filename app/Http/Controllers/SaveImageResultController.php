<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\EncryptionController;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;

class SaveImageResultController extends Controller
{
 
    protected $encryptionController;

    public function __construct(
        EncryptionController $encryptionController,

    ) {
        $this->encryptionController = $encryptionController;
    }

    public function saveImageResult($imageData, $recognitionResult)
    {
        $this->encryptAndStoreImage($imageData, $recognitionResult);
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
}
