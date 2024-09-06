<?php

declare(strict_types=1);


// Підключення до бази SQLite
$dbFile = 'data/pressure_pulse_log.db';
if (!file_exists($dbFile)) {
    require_once 'setup_db_1.php';
}
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

// Отримуємо дані з форми
$date = $_POST['date'];
$time_period = $_POST['time_period'];
$systolic_pressure = $_POST['systolic_pressure'];
$diastolic_pressure = $_POST['diastolic_pressure'];
$pulse = $_POST['pulse'];

// Збереження даних у базу
$stmt = $conn->prepare("INSERT INTO pressure_pulse_log (user_id, date, time_period, systolic_pressure, diastolic_pressure, pulse) VALUES (?, ?, ?, ?, ?, ?)");
$user_id = 1; // Якщо є авторизація, можна додати унікального користувача
$stmt->execute([$user_id, $date, $time_period, $systolic_pressure, $diastolic_pressure, $pulse]);

header("Location: index.php"); // Переадресація після збереження
