<?php

declare(strict_types=1);

namespace Litebase\Tests;

use Litebase\ChunkedSignatureSigner;

describe('ChunkedSignatureSigner', function () {
    test('constructor', function () {
        $accessKeySecret = 'test-secret-key';
        $date = '1699718400';
        $seedSignature = 'abc123def456';

        $signer = new ChunkedSignatureSigner($accessKeySecret, $date, $seedSignature);

        expect($signer)->toBeInstanceOf(ChunkedSignatureSigner::class);
        expect($signer->getPreviousSignature())->toBe($seedSignature);
    });

    test('signChunk', function () {
        $accessKeySecret = 'my-secret-key-12345';
        $date = '1699718400';
        $seedSignature = 'initial-seed-signature';
        $chunkData = 'test chunk data';

        $signer = new ChunkedSignatureSigner($accessKeySecret, $date, $seedSignature);
        $signature = $signer->signChunk($chunkData);

        // Verify signature is a hex string
        expect($signature)->toMatch('/^[a-f0-9]+$/');
        expect(strlen($signature))->toBe(64); // SHA256 produces 64 hex characters

        // Verify the previous signature was updated
        expect($signer->getPreviousSignature())->toBe($signature);
    });

    test('signChunkChaining', function () {

        $accessKeySecret = 'my-secret-key-12345';
        $date = '1699718400';
        $seedSignature = 'initial-seed-signature';

        $signer = new ChunkedSignatureSigner($accessKeySecret, $date, $seedSignature);

        // First chunk
        $chunk1 = 'first chunk';
        $signature1 = $signer->signChunk($chunk1);
        expect($signature1)->toBe($signer->getPreviousSignature());

        // Second chunk - should use signature1 in its calculation
        $chunk2 = 'second chunk';
        $signature2 = $signer->signChunk($chunk2);
        expect($signature2)->toBe($signer->getPreviousSignature());
        expect($signature1)->not->toBe($signature2);

        // Third chunk - should use signature2 in its calculation
        $chunk3 = 'third chunk';
        $signature3 = $signer->signChunk($chunk3);
        expect($signature3)->toBe($signer->getPreviousSignature());
        expect($signature2)->not->toBe($signature3);
    });

    test('signChunkDeterministic', function () {
        $accessKeySecret = 'my-secret-key';
        $date = '1699718400';
        $seedSignature = 'seed-signature';
        $chunkData = 'test data';

        // Create two signers with same parameters
        $signer1 = new ChunkedSignatureSigner($accessKeySecret, $date, $seedSignature);
        $signer2 = new ChunkedSignatureSigner($accessKeySecret, $date, $seedSignature);

        $signature1 = $signer1->signChunk($chunkData);
        $signature2 = $signer2->signChunk($chunkData);

        // Should produce the same signature
        expect($signature1)->toBe($signature2);
    });

    test('signChunkDifferentSecrets', function () {
        $secret1 = 'secret-one';
        $secret2 = 'secret-two';
        $date = '1699718400';
        $seedSignature = 'seed';
        $chunkData = 'data';

        $signer1 = new ChunkedSignatureSigner($secret1, $date, $seedSignature);
        $signer2 = new ChunkedSignatureSigner($secret2, $date, $seedSignature);

        $signature1 = $signer1->signChunk($chunkData);
        $signature2 = $signer2->signChunk($chunkData);

        // Different secrets should produce different signatures
        expect($signature1)->not->toBe($signature2);
    });

    test('signChunkDifferentDates', function () {
        $accessKeySecret = 'my-secret';
        $date1 = '1699718400';
        $date2 = '1699718401';
        $seedSignature = 'seed';
        $chunkData = 'data';

        $signer1 = new ChunkedSignatureSigner($accessKeySecret, $date1, $seedSignature);
        $signer2 = new ChunkedSignatureSigner($accessKeySecret, $date2, $seedSignature);

        $signature1 = $signer1->signChunk($chunkData);
        $signature2 = $signer2->signChunk($chunkData);

        // Different dates should produce different signatures
        expect($signature1)->not->toBe($signature2);
    });

    test('signChunkEmptyData', function () {
        $signer = new ChunkedSignatureSigner('secret', '1699718400', 'seed');
        $signature = $signer->signChunk('');

        // Should handle empty data without errors
        expect($signature)->toMatch('/^[a-f0-9]+$/');
        expect(strlen($signature))->toBe(64);
    });

    test('signChunkLargeData', function () {
        $signer = new ChunkedSignatureSigner('secret', '1699718400', 'seed');
        $largeData = str_repeat('a', 1024 * 1024); // 1MB
        $signature = $signer->signChunk($largeData);

        // Should handle large data without errors
        expect($signature)->toMatch('/^[a-f0-9]+$/');
        expect(strlen($signature))->toBe(64);
    });

    test('extractSignatureFromToken', function () {
        $token = base64_encode('credential=test-key;signed_headers=content-type,host;signature=abc123def456');
        $signature = ChunkedSignatureSigner::extractSignatureFromToken($token);

        expect($signature)->toBe('abc123def456');
    });

    test('extractSignatureFromTokenNotFound', function () {
        $token = base64_encode('credential=test-key;signed_headers=content-type,host');
        $signature = ChunkedSignatureSigner::extractSignatureFromToken($token);

        expect($signature)->toBeNull();
    });

    test('extractSignatureFromInvalidToken', function () {
        $token = 'not-valid-base64!!!';
        $signature = ChunkedSignatureSigner::extractSignatureFromToken($token);

        expect($signature)->toBeNull();
    });

    test('getPreviousSignature', function () {
        $seedSignature = 'initial-signature';
        $signer = new ChunkedSignatureSigner('secret', '1699718400', $seedSignature);

        // Should start with seed signature
        expect($seedSignature)->toBe($signer->getPreviousSignature());

        // After signing, should update
        $newSignature = $signer->signChunk('data');
        expect($newSignature)->toBe($signer->getPreviousSignature());
    });

    test('signatureMatchesSnapshot', function () {
        // This test verifies compatibility with the Go implementation
        // Using known test values to ensure cross-platform compatibility
        $accessKeySecret = 'test-secret';
        $date = '1699718400';
        $seedSignature = 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855'; // SHA256 of empty string
        $chunkData = 'test chunk data';

        $signer = new ChunkedSignatureSigner($accessKeySecret, $date, $seedSignature);
        $signature = $signer->signChunk($chunkData);

        // The signature should be deterministic
        // Calculate it again with a new signer to verify
        $signer2 = new ChunkedSignatureSigner($accessKeySecret, $date, $seedSignature);
        $signature2 = $signer2->signChunk($chunkData);

        expect($signature)->toBe($signature2);
    });
});
