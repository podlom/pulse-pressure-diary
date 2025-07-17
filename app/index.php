<?php

declare(strict_types=1);

  /**
   * @author Taras Shkodenko <podlom@gmail.com>
   * @copyright Shkodenko V. Taras 2025
   */

// Define a constant to be used for allowing direct access
define('ALLOW_DIRECT_ACCESS', true);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Database.php';

global $config;

$db = new Database($config);
$conn = $db->getConnection();

if (!$conn) {
    echo "<p>Не вдалося підключитися до бази даних.</p>";
    exit;
}

$page = isset($_REQUEST['page']) && $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;

// Перевіряємо, чи існує таблиця
$tableName = getenv('TABLE_NAME') ?: 'pressure_pulse_log';
$stmt = $conn->query("SHOW TABLES LIKE '{$tableName}'");
$tableExists = $stmt->fetch();

if (!$tableExists) {
    echo "<p>Таблиця '{$tableName}' не існує. Створіть новий запис через <a href='add_data.php'>форму додавання даних</a>.</p>";
    exit;
}

if ($config['db']['driver'] == 'sqlite') {
    // Перевіряємо, чи існує таблиця pressure_pulse_log
    $tableCheck = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='pressure_pulse_log'");
    $tableExists = $tableCheck->fetch();

    if (!$tableExists) {
        echo "<p>Таблиця бази даних '" . $db->getTableName() . "' не існує. Створіть перший запис через <a href='add_data.php'>форму додавання даних</a>.</p>";
        exit;
    }
}

    // Pagination setup
    $limit = 50;

    $offset = ($page - 1) * $limit;
    // Total count for pagination
    $totalStmt = $conn->query("SELECT COUNT(id) FROM " . $db->getTableName());
    $total = $totalStmt->fetchColumn();
    $totalPages = ceil($total / $limit);

    // Отримуємо дані
    if (!isset($_SESSION) || empty($_SESSION['user_id'])) {
        $userId = 1;
    } else {
        $userId = $_SESSION['user_id'] ?: 1;
    }
    $stmt = $conn->prepare("SELECT date, time_period, systolic_pressure, diastolic_pressure, pulse FROM " . $db->getTableName() . " WHERE user_id = {$userId} ORDER BY date DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Щоденник показників вимірювання тиску та пульсу - сторінка <?php echo $page; ?> | записи щоденника</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
  <div class="container">
    <h1>Щоденник показників (<?php echo $total; ?>) вимірювання тиску та пульсу</h1>
    <p>Додати запис через <a href='add_data.php'>форму додавання даних</a>.</p>

<?php if (count($records) > 0): ?>
    <table>
      <caption>Дані записів (<?php echo $total; ?>) щоденника показників тиску та пусльсу - сторінка <?php echo $page; ?></caption>
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
        <?php foreach ($records as $row): ?>
        <tr>
          <td><?php echo $row['date']; ?></td>
          <td><?php echo $row['time_period']; ?></td>
		  <td class="<?php echo ($row['systolic_pressure'] > 129) ? 'text-danger font-weight-bold' : ''; ?>">
			<?php echo $row['systolic_pressure']; ?>
		  </td>
		  <td class="<?php echo ($row['diastolic_pressure'] > 84) ? 'text-danger font-weight-bold' : ''; ?>">
			<?php echo $row['diastolic_pressure']; ?>
		  </td>
		  <td class="<?php echo ($row['pulse'] > 90) ? 'text-warning font-weight-bold' : ''; ?>">
			<?php echo $row['pulse']; ?>
		  </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Pagination links -->
    <div style="margin-top: 20px;">
      <?php if ($page > 1): ?>
          <a href="?page=<?= $page - 1 ?>">« Попередня сторінка</a>
      <?php endif; ?>
      Сторінка <?= $page ?> з <?= $totalPages ?>
      <?php if ($page < $totalPages): ?>
          <a href="?page=<?= $page + 1 ?>">Наступна сторінка »</a>
      <?php endif; ?>
    </div>
<?php else: ?>
    <p>Поки що немає записів.</p>
<?php endif; ?>

    <p>Додати ще один запис через <a href='add_data.php'>форму додавання даних</a>.</p>
  </div>

  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>

<?php

  $conn = null; // Закриваємо підключення
