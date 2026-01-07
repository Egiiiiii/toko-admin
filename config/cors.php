<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // Path yang diizinkan untuk diakses dari luar
    // Menambahkan 'admin/*' jaga-jaga jika ada request ajax spesifik dari admin panel
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'admin/*', 'up'],

    'allowed_methods' => ['*'],

    // DAFTAR DOMAIN YANG DIBOLEHKAN MENGAKSES API
    // Wajib spesifik (tidak boleh '*') karena supports_credentials = true
    'allowed_origins' => [
        'https://admin.roomify3.my.id', // Domain UI Admin
        'https://ip.roomify3.my.id',    // Domain UI Public (jika ada)
        'http://localhost:3000',        // Untuk development lokal (opsional)
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // WAJIB TRUE agar cookie login bisa dibagi antara Admin dan API
    'supports_credentials' => true,

];