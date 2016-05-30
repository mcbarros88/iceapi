<?php
return [
// Global Topixradio vars
    'admin_mail' => env('ICE_ADMIN_MAIL','vaxobo@leeching.net'),
    'start-port' => env( 'ICE_START_PORT', '8000'),
    'end-port' => env( 'ICE_END_PORT', '8999'),
    'root' => env( 'ICE_ROOT', '/var/www/iceapi/iceapi/storage/app/icecast/'),
    'url' => env( 'ICE_URL', 'http://localhost/'),
    'laravel' => env( 'LARAVEL_ROOT', '/var/www/topix-radio-laravel'),
    'hostname' => env( 'ICE_HOSTNAME', 'localhost'),
];