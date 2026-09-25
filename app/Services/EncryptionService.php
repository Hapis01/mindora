<?php

namespace App\Services;

use RuntimeException;

class EncryptionService
{
    protected string $masterKey;
    protected string $defaultVersion;

    public function __construct()
    {
        $rawKey = config('services.encryption.master_key', env('ENCRYPTION_MASTER_KEY'));

        if (empty($rawKey)) {
            throw new RuntimeException('ENCRYPTION_MASTER_KEY belum dikonfigurasi di environment (.env).');
        }

        if (str_starts_with($rawKey, 'base64:')) {
            $rawKey = base64_decode(substr($rawKey, 7));
        }

        if (strlen($rawKey) !== 32) {
            throw new RuntimeException('ENCRYPTION_MASTER_KEY harus tepat 32 bytes (256-bit).');
        }

        $this->masterKey = $rawKey;
        $this->defaultVersion = (string) config('services.encryption.version', env('ENCRYPTION_KEY_VERSION', 'v1'));
    }

    /**
     * Generate a cryptographically secure 256-bit Data Encryption Key (DEK).
     */
    public function generateDataKey(): string
    {
        return random_bytes(32);
    }

    /**
     * Derive a Key Encryption Key (KEK) from the Master Key using HKDF (RFC 5869).
     */
    protected function deriveKeyEncryptionKey(string $keyVersion): string
    {
        return hash_hkdf(
            algo: 'sha256',
            key: $this->masterKey,
            length: 32,
            info: 'Mindora_envelope_kek_' . $keyVersion,
            salt: 'Mindora_secure_salt_v1'
        );
    }

    /**
     * Verify encryption envelope metadata integrity.
     */
    public function verifyEncryptionMetadata(array $envelope): bool
    {
        if (!isset($envelope['v'], $envelope['alg'], $envelope['iv'], $envelope['tag'], $envelope['ciphertext'], $envelope['edek'])) {
            return false;
        }

        if ($envelope['alg'] !== 'AES-256-GCM') {
            return false;
        }

        $edek = $envelope['edek'];
        return isset($edek['iv'], $edek['tag'], $edek['key']);
    }

    /**
     * Encrypt sensitive plaintext using envelope encryption (AES-256-GCM).
     *
     * Master Key -> KEK (HKDF) -> Encrypts DEK
     * DEK (Random 256-bit) -> Encrypts Sensitive Data
     */
    public function encrypt(string $plaintext, ?string $keyVersion = null): string
    {
        $version = $keyVersion ?? $this->defaultVersion;
        $kek = $this->deriveKeyEncryptionKey($version);

        // 1. Generate fresh random 32-byte DEK & 12-byte IV for data
        $dek = $this->generateDataKey();
        $dataIv = random_bytes(12); // Standard GCM nonce: 96 bits

        // 2. Encrypt plaintext with AES-256-GCM
        $dataTag = '';
        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $dek,
            OPENSSL_RAW_DATA,
            $dataIv,
            $dataTag,
            '',
            16
        );

        if ($ciphertext === false) {
            throw new RuntimeException('Gagal melakukan enkripsi AES-256-GCM pada data sensitif.');
        }

        // 3. Encrypt DEK with KEK using AES-256-GCM
        $kekIv = random_bytes(12);
        $kekTag = '';
        $encryptedDek = openssl_encrypt(
            $dek,
            'aes-256-gcm',
            $kek,
            OPENSSL_RAW_DATA,
            $kekIv,
            $kekTag,
            '',
            16
        );

        if ($encryptedDek === false) {
            throw new RuntimeException('Gagal mengenkripsi Data Encryption Key (DEK).');
        }

        // 4. Construct envelope payload
        $envelope = [
            'v' => $version,
            'alg' => 'AES-256-GCM',
            'iv' => base64_encode($dataIv),
            'tag' => base64_encode($dataTag),
            'ciphertext' => base64_encode($ciphertext),
            'edek' => [
                'iv' => base64_encode($kekIv),
                'tag' => base64_encode($kekTag),
                'key' => base64_encode($encryptedDek),
            ],
        ];

        return base64_encode(json_encode($envelope));
    }

    /**
     * Decrypt envelope ciphertext payload (AES-256-GCM).
     */
    public function decrypt(?string $payload): ?string
    {
        if ($payload === null || trim($payload) === '') {
            return null;
        }

        $decodedJson = base64_decode($payload, true);
        if ($decodedJson === false) {
            // Attempt direct JSON parse if not base64 wrapped
            $decodedJson = $payload;
        }

        $envelope = json_decode($decodedJson, true);
        if (!is_array($envelope) || !$this->verifyEncryptionMetadata($envelope)) {
            throw new RuntimeException('Format envelope enkripsi tidak valid atau rusak.');
        }

        $version = (string) $envelope['v'];
        $kek = $this->deriveKeyEncryptionKey($version);

        // 1. Decrypt DEK using KEK
        $edekIv = base64_decode($envelope['edek']['iv']);
        $edekTag = base64_decode($envelope['edek']['tag']);
        $encryptedDek = base64_decode($envelope['edek']['key']);

        $dek = openssl_decrypt(
            $encryptedDek,
            'aes-256-gcm',
            $kek,
            OPENSSL_RAW_DATA,
            $edekIv,
            $edekTag
        );

        if ($dek === false) {
            throw new RuntimeException('Autentikasi tag KEK gagal: Kunci atau metadata telah dimanipulasi.');
        }

        // 2. Decrypt ciphertext using DEK
        $dataIv = base64_decode($envelope['iv']);
        $dataTag = base64_decode($envelope['tag']);
        $ciphertext = base64_decode($envelope['ciphertext']);

        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $dek,
            OPENSSL_RAW_DATA,
            $dataIv,
            $dataTag
        );

        if ($plaintext === false) {
            throw new RuntimeException('Autentikasi tag GCM gagal: Ciphertext data telah dimodifikasi atau rusak.');
        }

        return $plaintext;
    }

    /**
     * Generate secure HMAC verification hash for secondary factors (e.g. Date of Birth)
     * without storing plaintext or exposing raw sensitive data.
     */
    public function hashForVerification(string $value): string
    {
        return hash_hmac('sha256', trim($value), $this->masterKey);
    }

    /**
     * Constant-time verification of secondary factor hash.
     */
    public function verifyHash(string $plainValue, ?string $expectedHash): bool
    {
        if (empty($expectedHash)) {
            return false;
        }

        $calculated = $this->hashForVerification($plainValue);
        return hash_equals($expectedHash, $calculated);
    }
}
