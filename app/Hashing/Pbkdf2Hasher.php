<?php

namespace App\Hashing;

use Illuminate\Contracts\Hashing\Hasher;
use RuntimeException;

/**
 * PBKDF2-SHA256 hasher.
 *
 * Stored format: PBKDF2$<iterations>$<base64url_salt>$<base64url_hash>
 * Example:       PBKDF2$100000$VqJyVgQgy00acE4D0saAQg==$cu58x2s...Mq4=
 *
 * Parameters:
 *   - Algorithm : SHA-256
 *   - Iterations: 100,000  (NIST SP 800-132 minimum for SHA-256)
 *   - Salt      : 16 bytes (128-bit) cryptographically random
 *   - Output    : 32 bytes (256-bit derived key)
 */
class Pbkdf2Hasher implements Hasher
{
    private const ALGO       = 'sha256';
    private const ITERATIONS = 100_000;
    private const SALT_BYTES = 16;
    private const KEY_BYTES  = 32;
    private const PREFIX     = 'PBKDF2$';

    public function make(#[\SensitiveParameter] $value, array $options = []): string
    {
        $iterations = $options['iterations'] ?? self::ITERATIONS;
        $salt       = random_bytes(self::SALT_BYTES);
        $hash       = hash_pbkdf2(self::ALGO, $value, $salt, $iterations, self::KEY_BYTES, true);

        return self::PREFIX . $iterations
            . '$' . base64_encode($salt)
            . '$' . base64_encode($hash);
    }

    public function check(#[\SensitiveParameter] $value, $hashedValue, array $options = []): bool
    {
        if (!$this->isPbkdf2($hashedValue)) {
            return false;
        }

        $parts = explode('$', $hashedValue);
        if (count($parts) !== 4) {
            return false;
        }

        [, $iterations, $salt64, $hash64] = $parts;
        $salt       = base64_decode($salt64, strict: true);
        $storedHash = base64_decode($hash64, strict: true);

        if ($salt === false || $storedHash === false) {
            return false;
        }

        $computed = hash_pbkdf2(self::ALGO, $value, $salt, (int) $iterations, self::KEY_BYTES, true);

        return hash_equals($storedHash, $computed);
    }

    public function needsRehash($hashedValue, array $options = []): bool
    {
        if (!$this->isPbkdf2($hashedValue)) {
            return true;
        }

        $parts      = explode('$', $hashedValue);
        $iterations = isset($parts[1]) ? (int) $parts[1] : 0;
        $target     = $options['iterations'] ?? self::ITERATIONS;

        return $iterations < $target;
    }

    public function info($hashedValue): array
    {
        return [
            'algo'            => self::ALGO,
            'algoName'        => 'pbkdf2-sha256',
            'options'         => [],
            'isPbkdf2'        => $this->isPbkdf2($hashedValue),
        ];
    }

    private function isPbkdf2(string $hash): bool
    {
        return str_starts_with($hash, self::PREFIX);
    }
}
