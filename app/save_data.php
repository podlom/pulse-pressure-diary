<?php

declare(strict_types=1);


/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2024
 */

session_start();

require_once 'config.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If not a POST request, block access
    header('HTTP/1.1 403 Forbidden');
    exit('Direct access to this file is not allowed.');
}

// Initialize an array to hold error messages
$errors = [];

// Валідуємо та отримуємо дані з форми
// Validate the date (required and valid date format)
if (empty($_POST['date'])) {
    $errors[] = 'Дата обов’язкова.';
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['date'])) {
    $errors[] = 'Невірний формат дати. Використовуйте формат РРРР-ММ-ДД.';
} else {
    $date = $_POST['date'];
}

// Validate the time period (required and valid format HH:MM:SS)
if (empty($_POST['time_period'])) {
    $errors[] = 'Часовий проміжок обов’язковий.';
} elseif (!preg_match('/^(2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9])$/', $_POST['time_period'])) {
    $errors[] = 'Невірний формат часу. Використовуйте формат ГГ:ХХ:СС.';
} else {
    $time_period = $_POST['time_period'];
}

// Validate systolic pressure (required, numeric, and within a reasonable range)
if (empty($_POST['systolic_pressure'])) {
    $errors[] = 'Верхній тиск обов’язковий.';
} elseif (!is_numeric($_POST['systolic_pressure']) || $_POST['systolic_pressure'] < 50 || $_POST['systolic_pressure'] > 250) {
    $errors[] = 'Введіть дійсне значення для верхнього тиску (50-250 мм рт. ст.).';
} else {
    $systolic_pressure = $_POST['systolic_pressure'];
}

// Validate diastolic pressure (required, numeric, and within a reasonable range)
if (empty($_POST['diastolic_pressure'])) {
    $errors[] = 'Нижній тиск обов’язковий.';
} elseif (!is_numeric($_POST['diastolic_pressure']) || $_POST['diastolic_pressure'] < 30 || $_POST['diastolic_pressure'] > 150) {
    $errors[] = 'Введіть дійсне значення для нижнього тиску (30-150 мм рт. ст.).';
} else {
    $diastolic_pressure = $_POST['diastolic_pressure'];
}

// Validate pulse (required, numeric, and within a reasonable range)
if (empty($_POST['pulse'])) {
    $errors[] = 'Пульс обов’язковий.';
} elseif (!is_numeric($_POST['pulse']) || $_POST['pulse'] < 30 || $_POST['pulse'] > 200) {
    $errors[] = 'Введіть дійсне значення для пульсу (30-200 ударів на хвилину).';
} else {
    $pulse = $_POST['pulse'];
}

// Check if there are any errors
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    header('Location: add_data.php');  // Замініть на ім'я вашої сторінки з формою
    exit;
}

try {
    if ($config['db']['driver'] === 'sqlite') {
        $dbFile = $config['db']['sqlite']['path'];

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

    if (isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors'])) {
        // Очищаємо помилки після успішного запису
        unset($_SESSION['form_errors']);
    }

    // Переадресація після збереження
    header("Location: index.php");
    exit;

} catch (PDOException $e) {
    die("Помилка підключення до бази даних: " . $e->getMessage());
} catch (Exception $e) {
    die("Помилка: " . $e->getMessage());
}
