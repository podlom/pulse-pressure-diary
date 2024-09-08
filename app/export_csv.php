<?php

declare(strict_types=1);


/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2024
 */

require_once 'config.php';

try {
    if ($config['db']['driver'] === 'sqlite') {
        $dbFile = $config['db']['sqlite']['path'];
        $conn = new PDO('sqlite:' . $dbFile);
    } elseif ($config['db']['driver'] === 'mysql') {
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

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

} catch (PDOException $e) {
    die("Помилка підключення до бази даних: " . $e->getMessage());
} catch (Exception $e) {
    die("Помилка: " . $e->getMessage());
}
