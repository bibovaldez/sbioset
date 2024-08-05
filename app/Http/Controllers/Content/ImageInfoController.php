<?php

namespace App\Http\Controllers\Content;

use App\Models\Image;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\DecryptionController;

class ImageInfoController extends Controller
{
    protected $DecryptionController;
    public function __construct(DecryptionController $DecryptionController)
    {
        $this->DecryptionController = $DecryptionController;
    }
    public function index()
    {
        return $this->image();
    }


    // decrpt imageresult
    protected function image()
    {
        // Access the database view recognition_results
        // Access the database view recognition_results
        $results = DB::table('recognition_results')->get();
        // dd($results);
        // Initialize an array to hold decrypted data
        $decryptedData = [];

        // Decrypting example (if data is encrypted using Laravel's encryption)
        foreach ($results as $result) {
            // Assuming 'data' is the column name that contains encrypted data
            $decryptedColumnData = $this->DecryptionController->decryptData($result->recognition_result_encrypted);
            $decryptedData[] = $decryptedColumnData;
        }
        // Return or further process decrypted data
        return $decryptedData;
    }
}
