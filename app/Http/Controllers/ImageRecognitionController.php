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
        $apiKey = config('services.roboflow.api_key');
        $url = config('services.roboflow.api_url');
        $imageBase64 = base64_encode(file_get_contents($image->getRealPath()));
        $confidenceThreshold = 0.4;
        $response = $client->post($url, [
            'query' => [
                'api_key' => $apiKey,
                'confidence_threshold' => $confidenceThreshold,
            ],
            'body' => $imageBase64,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);
        $response = json_decode($response->getBody(), true);

        return $this->analyzeResponse($response);
    }

    // analyze the response from the image recognition
    protected function analyzeResponse($response)
    {
        foreach ($response['predictions'] as &$prediction) {
            if ($prediction['class'] === 'Healthy' || $prediction['class'] === 'Unhealthy') {
                continue;
            } else {
                $prediction['class'] = 'unknown';
            }
        }
        return $response;
    }
}
