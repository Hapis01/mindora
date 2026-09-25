<?php

namespace App\Models;

use App\Services\EncryptionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'encrypted_name',
        'encrypted_dob',
        'encrypted_phone',
        'encrypted_address',
        'gender',
        'avatar_path',
        'dob_verification_hash',
        'key_version',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Masked Accessors (Section 7: Profile Masking)
     * Default state shows strictly masked placeholder: ---
     */
    public function getMaskedNameAttribute(): string
    {
        return '---';
    }

    public function getMaskedDobAttribute(): string
    {
        return '---';
    }

    public function getMaskedPhoneAttribute(): string
    {
        return '---';
    }

    public function getMaskedAddressAttribute(): string
    {
        return '---';
    }

    /**
     * Decrypt all sensitive fields into an associative array.
     */
    public function getDecryptedData(EncryptionService $encryptionService): array
    {
        return [
            'name' => $this->encrypted_name ? $encryptionService->decrypt($this->encrypted_name) : null,
            'date_of_birth' => $this->encrypted_dob ? $encryptionService->decrypt($this->encrypted_dob) : null,
            'phone' => $this->encrypted_phone ? $encryptionService->decrypt($this->encrypted_phone) : null,
            'address' => $this->encrypted_address ? $encryptionService->decrypt($this->encrypted_address) : null,
            'gender' => $this->gender,
            'avatar_path' => $this->avatar_path,
        ];
    }

    /**
     * Encrypt and store sensitive profile data.
     */
    public function setEncryptedProfile(array $data, EncryptionService $encryptionService): void
    {
        if (isset($data['name']) && $data['name'] !== null) {
            $this->encrypted_name = $encryptionService->encrypt((string) $data['name']);
        }

        if (isset($data['date_of_birth']) && !empty($data['date_of_birth'])) {
            $dobString = (string) $data['date_of_birth'];
            $this->encrypted_dob = $encryptionService->encrypt($dobString);
            $this->dob_verification_hash = $encryptionService->hashForVerification($dobString);
        }

        if (isset($data['phone']) && $data['phone'] !== null) {
            $this->encrypted_phone = $encryptionService->encrypt((string) $data['phone']);
        }

        if (isset($data['address']) && $data['address'] !== null) {
            $this->encrypted_address = $encryptionService->encrypt((string) $data['address']);
        }

        if (isset($data['gender'])) {
            $this->gender = $data['gender'];
        }

        $this->key_version = (string) config('services.encryption.version', 'v1');
    }

    /**
     * Verify secondary factor: Date of Birth.
     * Uses constant-time HMAC check against dob_verification_hash without needing decryption.
     */
    public function verifyDob(string $inputDob, EncryptionService $encryptionService): bool
    {
        if (!empty($this->dob_verification_hash)) {
            return $encryptionService->verifyHash(trim($inputDob), $this->dob_verification_hash);
        }

        // Fallback: decrypt stored dob if verification hash wasn't previously set
        if ($this->encrypted_dob) {
            $decrypted = $encryptionService->decrypt($this->encrypted_dob);
            return trim($inputDob) === trim((string) $decrypted);
        }

        return false;
    }
}
