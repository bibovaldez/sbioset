<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

class EncryptionController extends Controller
{
    protected $key;
    protected $additionalData;

    public function __construct()
    {
        $this->key = base64_decode(env('KEY'));
        $this->additionalData = hash('sha256', env('AD'), true);
    }

    public function encryptData($data)
    {
        return $this->encrypt($data, $this->key, $this->additionalData);
    }

    private function encrypt(string $data, string $key, string $additionalData): ?string
    {
        // Generate nonce
        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);

        if (!extension_loaded('sodium')) {
            Log::error('Sodium extension is not loaded.');
            throw new \RuntimeException('Sodium extension is required for encryption.');
        }

        if (strlen($key) !== SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_KEYBYTES) {
            Log::error('Invalid key length for XChaCha20-Poly1305.');
            throw new \InvalidArgumentException('Invalid key length.');
        }

        try {
            $encrypted = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
                $data,
                $additionalData,
                $nonce,
                $key
            );

            if ($encrypted === false) {
                Log::error('Encryption failed.');
                return null;
            }

            // Prepend nonce to the encrypted data
            $encryptedWithNonce = $nonce . $encrypted;

            // Apply non-linear transformation
            $transformedCiphertext = $this->nonLinearTransform($encryptedWithNonce);

            return base64_encode($transformedCiphertext);
        } catch (\SodiumException $e) {
            Log::error('Sodium encryption failed: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('Unexpected error during encryption: ' . $e->getMessage());
            return null;
        } finally {
            if (isset($encrypted) && is_string($encrypted)) {
                sodium_memzero($encrypted);
            }
            if (isset($transformedCiphertext) && is_string($transformedCiphertext)) {
                sodium_memzero($transformedCiphertext);
            }
        }
    }
    private function nonLinearTransform(string $input): string
    {
        $output = '';
        $length = strlen($input);
        for ($i = 0; $i < $length; $i++) {
            $byte = ord($input[$i]);
            $transformed = ($byte + 255) ^ 0xA5;
            $output .= chr($transformed % 256);
        }
        return $output;
    }
}