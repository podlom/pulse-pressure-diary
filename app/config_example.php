<?php

declare(strict_types=1);

/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2025
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
$charset = $_ENV['DB_CHARSET'] ?: 'utf8mb4';
$tableName = $_ENV['TABLE_NAME'] ?: 'pressure_pulse_log';
$usersTableName = $_ENV['TABLE_USERS'] ?: 'users';

global $config;

$config = [
    'db' => [
        'driver' => $driver,
        'sqlite' => [
            'path' => __DIR__ . '/data/' . $dbname . '.db',
        ],
        'mysql' => [
            'host' => $host,
            'dbname' => $dbname,
            'user' => $user,
            'password' => $password,
            'charset' => $charset,
        ],
        'tableName' => $tableName,
        'usersTableName' => $usersTableName,
    ],
];
