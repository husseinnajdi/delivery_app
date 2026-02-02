<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Secret Key
    |--------------------------------------------------------------------------
    |
    | This key is used to sign your JSON Web Tokens. You can generate a random
    | string using `php artisan key:generate --show` or manually.
    |
    */

    'key' => env('JWT_SECRET', 'secret'),

    /*
    |--------------------------------------------------------------------------
    | JWT Algorithm
    |--------------------------------------------------------------------------
    |
    | This defines which algorithm will be used to sign the token.
    | Common choices: HS256, HS512, RS256, etc.
    |
    */

    'algo' => 'HS256',
];
