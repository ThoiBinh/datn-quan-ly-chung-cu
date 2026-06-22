<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    | pbkdf2 — custom driver registered in AppServiceProvider via Hash::extend()
    | Implements PBKDF2-SHA256, 100,000 iterations, 16-byte salt, 32-byte key.
    | Format: PBKDF2$<iterations>$<base64_salt>$<base64_hash>
    */

    'driver' => 'pbkdf2',

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => env('HASH_VERIFY', true),
    ],

    'argon' => [
        'memory'    => env('ARGON_MEMORY', 65536),
        'threads'   => env('ARGON_THREADS', 1),
        'time'      => env('ARGON_TIME', 4),
        'verify'    => env('HASH_VERIFY', true),
    ],

    'argon2id' => [
        'memory'    => env('ARGON_MEMORY', 65536),
        'threads'   => env('ARGON_THREADS', 1),
        'time'      => env('ARGON_TIME', 4),
        'verify'    => env('HASH_VERIFY', true),
    ],

];
