<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class DecryptionController extends Controller
{
    protected $key;
    protected $additionalData;

    public function __construct()
    {
        $this->key = base64_decode(config('custom.encryption.key'));
        $this->additionalData = hash('sha256', config('custom.encryption.additional_data'), true);
    }

    public function decryptData($ciphertext)
    {
        return $this->decrypt($ciphertext, $this->key, $this->additionalData);
    }

    private function decrypt(string $encryptedData, string $key, string $additionalData): ?string
    {
        if (!extension_loaded('sodium')) {
            Log::error('Sodium extension is not loaded.');
            throw new \RuntimeException('Sodium extension is required for decryption.');
        }

        if (strlen($key) !== SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES) {
            Log::error('Invalid key length for XChaCha20-Poly1305.');
            throw new \InvalidArgumentException('Invalid key length.');
        }

        try {
            $decodedData = base64_decode($encryptedData, true);
            if ($decodedData === false) {
                Log::error('Invalid base64 encoding.');
                return null;
            }

            // Apply inverse of non-linear transformation
            $transformedData = $this->inverseNonLinearTransform($decodedData);

            // Extract nonce
            $nonce = substr($transformedData, 0, SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);
            $ciphertext = substr($transformedData, SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);

            $decrypted = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
                $ciphertext,
                $additionalData,
                $nonce,
                $key
            );

            if ($decrypted === false) {
                Log::error('Decryption failed.');
                return null;
            }

            return $decrypted;
        } catch (\SodiumException $e) {
            Log::error('Sodium decryption failed: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('Unexpected error during decryption: ' . $e->getMessage());
            return null;
        } finally {
            if (isset($decrypted) && is_string($decrypted)) {
                sodium_memzero($decrypted);
            }
            if (isset($transformedData) && is_string($transformedData)) {
                sodium_memzero($transformedData);
            }
        }
    }

    private function inverseNonLinearTransform(string $input): string
    {
        $output = '';
        $length = strlen($input);

        for ($i = 0; $i < $length; $i++) {
            $byte = ord($input[$i]);
            $reversed = ($byte ^ 0xA5) - 255; // Reverse the transformation
            $output .= chr($reversed % 256);
        }

        return $output;
    }
}