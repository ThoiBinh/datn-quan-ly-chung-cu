<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Auth provider for nhan_vien guard.
 *
 * nhan_vien.mat_khau in the SQL dump is bcrypt ($2y$12$...).
 * New passwords (via Hash::make()) use PBKDF2.
 *
 * On first login with a legacy bcrypt hash:
 *   1. Verify using password_verify() (bcrypt)
 *   2. Rehash to PBKDF2 and save — transparent to the user
 *   3. Subsequent logins use PBKDF2 normally
 */
class NhanVienUserProvider extends EloquentUserProvider
{
    private function isBcryptHash(string $hash): bool
    {
        return str_starts_with($hash, '$2y$') || str_starts_with($hash, '$2a$');
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $stored = $user->getAuthPassword();

        if ($this->isBcryptHash($stored)) {
            if (!password_verify($credentials['password'], $stored)) {
                return false;
            }
            // Auto-upgrade: rehash legacy bcrypt to PBKDF2
            $user->forceFill(['mat_khau' => $this->hasher->make($credentials['password'])])->save();
            return true;
        }

        return $this->hasher->check($credentials['password'], $stored);
    }
}
