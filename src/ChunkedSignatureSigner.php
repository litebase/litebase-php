<?php

namespace Litebase;

/**
 * ChunkedSignatureSigner handles signature calculation for chunked uploads in LQTP.
 * This implements chunked signature validation similar to AWS Signature Version 4.
 */
class ChunkedSignatureSigner
{
    protected ?string $accessKeySecret;

    protected string $date;

    protected string $previousSignature;

    /**
     * Create a new ChunkedSignatureSigner instance.
     */
    public function __construct(?string $accessKeySecret, string $date, string $seedSignature)
    {
        $this->accessKeySecret = $accessKeySecret;
        $this->date = $date;
        $this->previousSignature = $seedSignature;
    }

    /**
     * Sign a chunk of data and return the signature.
     *
     * Signature calculation (LQTP protocol):
     *  1. Hash the chunk data: chunkHash = SHA256(chunkData)
     *  2. Create string to sign: stringToSign = previousSignature + chunkHash
     *  3. Generate signing key chain:
     *     - dateKey = HMAC-SHA256(accessKeySecret, date)
     *     - serviceKey = HMAC-SHA256(dateKey, "litebase_request")
     *  4. Sign: signature = HMAC-SHA256(serviceKey, stringToSign)
     *
     * The signature chains ensure chunks are sent in the correct order and prevents tampering.
     */
    public function signChunk(string $chunkData): string
    {
        // Calculate the hash of the chunk data
        $chunkHash = hash('sha256', $chunkData);

        // Create the string to sign for this chunk
        // Format: previousSignature + chunkHash
        $stringToSign = $this->previousSignature.$chunkHash;

        // Create the signing key chain (same as in request signature validation)
        $dateKey = hash_hmac('sha256', $this->date, $this->accessKeySecret ?? '');
        $serviceKey = hash_hmac('sha256', 'litebase_request', $dateKey);

        // Sign the chunk
        $signature = hash_hmac('sha256', $stringToSign, $serviceKey);

        // Update the previous signature for the next chunk
        $this->previousSignature = $signature;

        return $signature;
    }

    /**
     * Get the current previous signature (for testing/debugging).
     */
    public function getPreviousSignature(): string
    {
        return $this->previousSignature;
    }

    /**
     * Extract the signature from a base64 encoded authorization token.
     *
     * @param  string  $token  The base64 encoded token
     * @return string|null The extracted signature or null if not found
     */
    public static function extractSignatureFromToken(string $token): ?string
    {
        $decoded = base64_decode($token, true);

        if ($decoded === false) {
            return null;
        }

        $parts = explode(';', $decoded);

        foreach ($parts as $part) {
            if (str_starts_with($part, 'signature=')) {
                return substr($part, strlen('signature='));
            }
        }

        return null;
    }
}
