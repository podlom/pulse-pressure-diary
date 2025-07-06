<?php

declare(strict_types=1);

/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2025
 */

// Define a constant to be used for allowing direct access
define('ALLOW_DIRECT_ACCESS', true);

require_once 'config.php';
require_once 'Database.php';

try {
    /** @var array $config */
    $database = new Database($config);
    $conn = $database->getConnection();
    $table = $database->getTableName();
    $userId = $_SESSION['user_id'] ?: 1;

    // Отримуємо дані з таблиці
    $sql = "SELECT date, time_period, systolic_pressure, diastolic_pressure, pulse FROM {$table} WHERE user_id = {$userId} ORDER BY date DESC";
    $stmt = $conn->query($sql);

    // Встановлюємо заголовки для експорту у CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $table . '.csv');

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
