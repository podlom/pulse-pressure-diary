<?php

declare(strict_types=1);

/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2024
 */

// Prevent direct access to this file
if (!defined('ALLOW_DIRECT_ACCESS')) {
    // You can redirect to an error page or show a 403 Forbidden message
    header('HTTP/1.1 403 Forbidden');
    exit('Direct access to this file not allowed.');
}

require_once 'config.php';

try {
    if ($config['db']['driver'] === 'sqlite') {
        $dbFile = $config['db']['sqlite']['path'];

        // Створення директорії, якщо вона не існує
        $dbDir = dirname($dbFile);
        if (!is_dir($dbDir)) {
            if (!mkdir($dbDir, 0755, true)) {
                die("Не вдалося створити директорію '$dbDir'. Перевірте права доступу.");
            }
        }

        // Створення файлу бази даних
        if (!file_exists($dbFile)) {
            if (!touch($dbFile)) {
                die("Не вдалося створити файл бази даних '$dbFile'. Перевірте права доступу.");
            }
            if (!chmod($dbFile, 0666)) {
                die("Не вдалося встановити права доступу до файлу '$dbFile'.");
            }
        }

        // Підключення до SQLite
        $conn = new PDO('sqlite:' . $dbFile);
    } elseif ($config['db']['driver'] === 'mysql') {
        // Підключення до MySQL
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config['db']['mysql']['host'],
            $config['db']['mysql']['dbname'],
            $config['db']['mysql']['charset']
        );
        $conn = new PDO($dsn, $config['db']['mysql']['user'], $config['db']['mysql']['password']);
    } else {
        throw new Exception("Непідтримуваний драйвер бази даних: " . $config['db']['driver']);
    }

    // Встановлюємо режим помилок PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Перевірка існування таблиці та створення її, якщо необхідно
    $createTableSQL = "";

    if ($config['db']['driver'] === 'sqlite') {
        $createTableSQL = "CREATE TABLE IF NOT EXISTS pressure_pulse_log (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            date TEXT NOT NULL,
            time_period TEXT NOT NULL,
            systolic_pressure INTEGER NOT NULL,
            diastolic_pressure INTEGER NOT NULL,
            pulse INTEGER NOT NULL
        );";
    } elseif ($config['db']['driver'] === 'mysql') {
        $createTableSQL = "CREATE TABLE IF NOT EXISTS pressure_pulse_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            date DATE NOT NULL,
            time_period VARCHAR(50) NOT NULL,
            systolic_pressure INT NOT NULL,
            diastolic_pressure INT NOT NULL,
            pulse INT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    }

    $conn->exec($createTableSQL);
} catch (PDOException $e) {
    die("Помилка підключення до бази даних: " . $e->getMessage());
} catch (Exception $e) {
    die("Помилка: " . $e->getMessage());
}
