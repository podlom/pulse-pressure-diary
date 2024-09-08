<?php

declare(strict_types=1);


// Підключення до бази SQLite
$dbFile = 'data/pressure_pulse_log.db';
$conn = new PDO('sqlite:' . $dbFile);

// Отримуємо дані з таблиці
$sql = "SELECT date, time_period, systolic_pressure, diastolic_pressure, pulse FROM pressure_pulse_log WHERE user_id = 1 ORDER BY date DESC";
$stmt = $conn->query($sql);

// Встановлюємо заголовки для експорту у CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=pressure_pulse_log.csv');

// Відкриваємо потік для запису даних у CSV
$output = fopen('php://output', 'w');

// Записуємо заголовки колонок у CSV
fputcsv($output, ['Дата', 'Час дня', 'Верхній тиск', 'Нижній тиск', 'Пульс']);

// Записуємо дані в CSV
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

// Закриваємо потік
fclose($output);
exit;