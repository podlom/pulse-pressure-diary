<?php

declare(strict_types=1);

session_start();

  /**
   * @author Taras Shkodenko <podlom@gmail.com>
   * @copyright Shkodenko V. Taras 2024
   */
    date_default_timezone_set('Europe/Kyiv');

    // Define a constant to be used for allowing direct access
    define('ALLOW_DIRECT_ACCESS', true);

    $currentDate = date("Y-m-d");
    $currentTime = date("H:i:s");

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Щоденник показників вимірювання тиску та пульсу | додати новий запис</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1><a href="/" title="Щоденник показників вимірювання тиску та пульсу | записи щоденника">Щоденник тиску та пульсу</a> - додати новий запис</h1>
        <?php

            // Перевіряємо, чи є збережені помилки у сесії
            if (!empty($_SESSION['form_errors'])) {
                foreach ($_SESSION['form_errors'] as $error) {
                    echo '<p class="text-danger">' . htmlspecialchars($error) . '</p>';
                }

                // Очищаємо помилки після їх відображення
                unset($_SESSION['form_errors']);
            }

        ?>
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
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>
