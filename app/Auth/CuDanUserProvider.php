<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;

/**
 * Auth provider for cudan guard.
 *
 * cu_dan.mat_khau in the SQL dump is already PBKDF2 format (PBKDF2$100000$...).
 * The registered Pbkdf2Hasher handles verification and creation natively,
 * so no custom validateCredentials logic is needed.
 *
 * This class exists as a named provider so future cudan-specific
 * auth behaviour (e.g. login throttle, device tracking) can be added here.
 */
class CuDanUserProvider extends EloquentUserProvider {}
