<?php

return [
    'data_warehouse' => [
        'default' => [
            'driver' => 'mysql',
            'host' => env('DB_MEDICAL_PLATFORM_HOST', '127.0.0.1'),
            'port' => env('DB_MEDICAL_PLATFORM_PORT', '3306'),
            'database' => env('DB_MEDICAL_PLATFORM_DATABASE', 'forge'),
            'username' => env('DB_MEDICAL_PLATFORM_USERNAME', 'forge'),
            'password' => env('DB_MEDICAL_PLATFORM_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                PDO::ATTR_TIMEOUT => env('DB_MEDICAL_PLATFORM_CONNECTION_TIMEOUT', 10),
                PDO::ATTR_EMULATE_PREPARES => env('DB_MEDICAL_PLATFORM_PREPARES', false),
            ]) : [],
        ],
    ],
];
