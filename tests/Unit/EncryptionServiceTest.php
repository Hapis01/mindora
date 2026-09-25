<?php

namespace Tests\Unit;

use App\Services\EncryptionService;
use RuntimeException;
use Tests\TestCase;

class EncryptionServiceTest extends TestCase
{
    protected EncryptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(EncryptionService::class);
    }

    public function test_can_encrypt_and_decrypt_plaintext(): void
    {
        $plaintext = 'Muhammad Hafiz - Sensitive Personal Data 1998-05-20';
        $encryptedPayload = $this->service->encrypt($plaintext);

        $this->assertNotEmpty($encryptedPayload);
        $this->assertNotEquals($plaintext, $encryptedPayload);

        // Verify envelope structure
        $decoded = json_decode(base64_decode($encryptedPayload), true);
        $this->assertIsArray($decoded);
        $this->assertEquals('v1', $decoded['v']);
        $this->assertEquals('AES-256-GCM', $decoded['alg']);
        $this->assertArrayHasKey('iv', $decoded);
        $this->assertArrayHasKey('tag', $decoded);
        $this->assertArrayHasKey('ciphertext', $decoded);
        $this->assertArrayHasKey('edek', $decoded);
        $this->assertArrayHasKey('key', $decoded['edek']);

        // Decrypt and compare
        $decrypted = $this->service->decrypt($encryptedPayload);
        $this->assertEquals($plaintext, $decrypted);
    }

    public function test_decryption_fails_if_ciphertext_is_tampered(): void
    {
        $plaintext = 'Secret Data to be tampered';
        $encryptedPayload = $this->service->encrypt($plaintext);

        $decoded = json_decode(base64_decode($encryptedPayload), true);
        // Tamper with ciphertext by corrupting 1 byte
        $rawCiphertext = base64_decode($decoded['ciphertext']);
        $tamperedCiphertext = substr($rawCiphertext, 0, -1) . chr(ord(substr($rawCiphertext, -1)) ^ 0xFF);
        $decoded['ciphertext'] = base64_encode($tamperedCiphertext);

        $tamperedPayload = base64_encode(json_encode($decoded));

        $this->expectException(RuntimeException::class);
        $this->service->decrypt($tamperedPayload);
    }

    public function test_secondary_factor_hash_verification(): void
    {
        $dob = '1998-05-20';
        $hash = $this->service->hashForVerification($dob);

        $this->assertTrue($this->service->verifyHash('1998-05-20', $hash));
        $this->assertFalse($this->service->verifyHash('1998-05-21', $hash));
        $this->assertFalse($this->service->verifyHash('random_string', $hash));
    }
}
