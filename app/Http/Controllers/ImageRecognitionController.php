<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ImageRecognitionController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function processImage(UploadedFile $image)
    {
        return $this->recognizeImage($image);
    }

    protected function recognizeImage(UploadedFile $image)
    {
        $client = new Client();
        $apiKey = env('ROBOFLOW_API_KEY');
        $url = env('ROBOFLOW_API_URL');
        $imageBase64 = base64_encode(file_get_contents($image->getRealPath()));

        $response = $client->post($url, [
            'query' => [
                'api_key' => $apiKey,
            ],
            'body' => $imageBase64,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);
        $response = json_decode($response->getBody(), true);
        
        return $response;
    }
}
