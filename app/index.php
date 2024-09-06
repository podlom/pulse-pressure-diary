<?php

declare(strict_types=1);


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
  <title>Pulse and pressure diary log application | List entries</title>
</head>
<body>
<table>
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
</body>
</html>

<?php

  $conn = null; // Закриваємо підключення
