<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DecryptionController extends Controller
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
    public function decryptData($ciphertext)
    {
        return $this->decrypt($this->decompressAndDecode($ciphertext));
    }
    protected function decrypt($ciphertext)
    {
        try {
            $decrypted = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
                $ciphertext,
                $this->additionalData,
                $this->nonce,
                $this->key
            );
            return $decrypted !== false ? $decrypted : null;
        } catch (\Exception $e) {
            Log::error('Decryption failed: ' . $e->getMessage());
            return null;
        }
    }

    protected function decompressAndDecode(string $data): string
    {
        return gzuncompress(base64_decode($data));
    }
}
