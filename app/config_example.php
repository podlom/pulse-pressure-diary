<?php

declare(strict_types=1);


/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2024
 */

require_once __DIR__ . '/vendor/autoload.php'; // Якщо використовуєш Composer


use Dotenv\Dotenv;


// Завантаження змінних середовища з .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Отримання змінних середовища
$driver = $_ENV['DB_DRIVER'] ?: 'mysql';
$host = $_ENV['DB_HOST'] ?: 'localhost';
$dbname = $_ENV['DB_NAME'] ?: 'pressure_pulse_log';
$user = $_ENV['DB_USER'] ?: 'root';
$password = $_ENV['DB_PASSWORD'] ?: '';
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
