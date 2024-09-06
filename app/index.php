<?php

declare(strict_types=1);


  /**
   * @author Taras Shkodenko <podlom@gmail.com>
   * @copyright Shkodenko V. Taras 2024
   */

  // Define a constant to be used for allowing direct access
  define('ALLOW_DIRECT_ACCESS', true);

  // Підключення до бази SQLite
  $dbFile = 'data/pressure_pulse_log.db';
  if (!file_exists($dbFile)) {
    require_once 'setup_db_1.php';
  }
  $conn = new PDO('sqlite:' . $dbFile);

  // Перевіряємо, чи існує таблиця pressure_pulse_log
  $tableCheck = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='pressure_pulse_log'");
  $tableExists = $tableCheck->fetch();

  if (!$tableExists) {
      echo "<p>Таблиця 'pressure_pulse_log' не існує. Створіть запис через <a href='add_data.php'>форму додавання даних</a>.</p>";
      exit;
  }

  // Отримуємо дані
  $sql = "SELECT date, time_period, systolic_pressure, diastolic_pressure, pulse FROM pressure_pulse_log WHERE user_id = 1 ORDER BY date DESC";
  $stmt = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Щоденник показників вимірювання тиску та пульсу | записи щоденника</title>
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
  <div class="container">
    <h1>Щоденник показників вимірювання тиску та пульсу</h1>
    <table>
      <caption>Дані записів щоденника показників тиску та пусльсу</caption>
      <thead>
        <tr>
          <th>Дата</th>
          <th>Час</th>
          <th>Верхній тиск</th>
          <th>Нижній тиск</th>
          <th>Пульс</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
          <td><?php echo $row['date']; ?></td>
          <td><?php echo $row['time_period']; ?></td>
          <td><?php echo $row['systolic_pressure']; ?></td>
          <td><?php echo $row['diastolic_pressure']; ?></td>
          <td><?php echo $row['pulse']; ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <p>Додати ще один запис через <a href='add_data.php'>форму додавання даних</a>.</p>
  </div>
  
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>

<?php

  $conn = null; // Закриваємо підключення
