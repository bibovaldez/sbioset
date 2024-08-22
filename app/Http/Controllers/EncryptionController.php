<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

class EncryptionController extends Controller
{
    protected $key;
    protected $nonce;
    protected $additionalData;

    public function __construct()
    {
        $this->key = base64_decode(env('KEY'));
        $this->nonce = base64_decode(env('NONCE'));
        $this->additionalData = hash('sha256', env('AD'), true);
    }

    public function encryptData($data)
    {
        return $this->compressAndEncode($this->encrypt($data));
    }
    protected function encrypt($data)
    {
        try {
            return sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
                $data,
                $this->additionalData,
                $this->nonce,
                $this->key
            );
        } catch (\Exception $e) {
            Log::error('Encryption failed: ' . $e->getMessage());
            return null;
        }
    }
    protected function compressAndEncode(string $data): string
    {
        return base64_encode(gzcompress($data, 9));
    }
}
