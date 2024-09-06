<?php

declare(strict_types=1);

// Створення директорії, якщо вона не існує
$dbDir = 'data';
if (!is_dir($dbDir)) {
    if (!mkdir($dbDir, 0755, true)) {
        die("Не вдалося створити директорію '$dbDir'. Перевірте права доступу.");
    }
}

// Створюємо файл бази даних
$dbFile = $dbDir . '/pressure_pulse_log.db';
if (!file_exists($dbFile)) {
    if (!touch($dbFile)) {
        die("Не вдалося створити файл бази даних '$dbFile'. Перевірте права доступу.");
    }
    if (!chmod($dbFile, 0666)) {
        die("Не вдалося встановити права доступу до файлу '$dbFile'.");
    }
}

// Підключення до бази SQLite
try {
    $conn = new PDO('sqlite:' . $dbFile);

    // Перевіряємо, чи існує таблиця 'pressure_pulse_log', і якщо ні, створюємо її
    $tableCheck = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='pressure_pulse_log'");
    $tableExists = $tableCheck->fetch();

    if (!$tableExists) {
        // Створюємо таблицю, якщо вона не існує
        $conn->exec("CREATE TABLE IF NOT EXISTS pressure_pulse_log (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            date TEXT NOT NULL,
            time_period TEXT NOT NULL,
            systolic_pressure INTEGER NOT NULL,
            diastolic_pressure INTEGER NOT NULL,
            pulse INTEGER NOT NULL
        );");
    }
} catch (PDOException $e) {
    die("Помилка підключення до бази даних: " . $e->getMessage());
}
