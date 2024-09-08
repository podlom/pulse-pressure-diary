<?php

declare(strict_types=1);


/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2024
 */

// Отримання змінних середовища
$driver = getenv('DB_DRIVER') ?: 'sqlite';
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'pressure_pulse_log';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$charset = 'utf8mb4';

$config = [
    'db' => [
        'driver' => $driver,
        'sqlite' => [
            'path' => __DIR__ . '/data/pressure_pulse_log.db',
        ],
        'mysql' => [
            'host' => $host,
            'dbname' => $dbname,
            'user' => $user,
            'password' => $password,
            'charset' => $charset,
        ],
    ],
];
