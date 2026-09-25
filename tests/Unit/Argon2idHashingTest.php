<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Argon2idHashingTest extends TestCase
{
    public function test_passwords_are_hashed_using_argon2id(): void
    {
        $password = 'SecretMasterKey2026!';
        $hashed = Hash::make($password);

        $this->assertStringStartsWith('$argon2id$', $hashed);
        $this->assertTrue(Hash::check($password, $hashed));
        $this->assertFalse(Hash::check('WrongPassword', $hashed));
    }
}
