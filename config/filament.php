<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filament Path
    |--------------------------------------------------------------------------
    |
    | The default is 'admin' but you can change it to whatever you like.
    |
    */

    'path' => env('FILAMENT_PATH', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Filament Domain
    |--------------------------------------------------------------------------
    |
    | You may change the domain where Filament should be active.
    | If the domain is null, all domains will be valid.
    |
    */

    'domain' => env('FILAMENT_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | Filament Branding
    |--------------------------------------------------------------------------
    |
    | The branding name that will be displayed on the admin panel.
    |
    */

    'brand' => 'UniPost Admin',

    /*
    |--------------------------------------------------------------------------
    | Filament Default Filesystem Disk
    |--------------------------------------------------------------------------
    */

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Filament Auth Configuration
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'guard' => env('FILAMENT_AUTH_GUARD', 'web'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Middleware
    |--------------------------------------------------------------------------
    */

    'middleware' => [
        'auth' => [
            Illuminate\Cookie\Middleware\EncryptCookies::class,
            Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            Illuminate\Session\Middleware\StartSession::class,
            Illuminate\View\Middleware\ShareErrorsFromSession::class,
            Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ],

];
