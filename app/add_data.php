<?php

declare(strict_types=1);

date_default_timezone_set('Europe/Kyiv');

$currentDate = date("Y-m-d");
$currentTime = date("H:i:s");

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pulse and pressure diary log application | Add an entry</title>
</head>
<body>
    <form method="POST" action="save_data.php">
        <label for="date">Дата:</label>
        <input type="date" name="date" value="<?= $currentDate ?>" required><br>

        <label for="time_period">Поточний час: <?= $currentTime ?></label>
        <input name="time_period" type="hidden" value="<?= $currentTime ?>" required><br>

        <label for="systolic_pressure">Верхній тиск:</label>
        <input type="number" name="systolic_pressure" required><br>

        <label for="diastolic_pressure">Нижній тиск:</label>
        <input type="number" name="diastolic_pressure" required><br>

        <label for="pulse">Пульс:</label>
        <input type="number" name="pulse" required><br>

        <button type="submit">Зберегти</button>
    </form>
</body>
</html>
