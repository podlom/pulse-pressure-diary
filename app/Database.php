<?php

declare(strict_types=1);

/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2024
 */


// Prevent direct access to this file
if (!defined('ALLOW_DIRECT_ACCESS')) {
    // You can redirect to an error page or show a 403 Forbidden message
    header('HTTP/1.1 403 Forbidden');
    exit('Direct access to this file not allowed.');
}


class Database
{
    private PDO $conn;
    private string $driver;

    public function __construct(array $config)
    {
        $this->driver = $config['db']['driver'];

        try {
            if ($this->driver === 'sqlite') {
                $dbFile = $config['db']['sqlite']['path'];
                $this->conn = new PDO('sqlite:' . $dbFile);
            } elseif ($this->driver === 'mysql') {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    $config['db']['mysql']['host'],
                    $config['db']['mysql']['dbname'],
                    $config['db']['mysql']['charset']
                );
                $this->conn = new PDO($dsn, $config['db']['mysql']['user'], $config['db']['mysql']['password']);
            } else {
                throw new Exception("Непідтримуваний драйвер бази даних: " . $this->driver);
            }

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Помилка підключення до бази даних: " . $e->getMessage());
        } catch (Exception $e) {
            die("Помилка: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function createTable(): void
    {
        $createTableSQL = "";

        if ($this->driver === 'sqlite') {
            $createTableSQL = "CREATE TABLE IF NOT EXISTS pressure_pulse_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                date TEXT NOT NULL,
                time_period TEXT NOT NULL,
                systolic_pressure INTEGER NOT NULL,
                diastolic_pressure INTEGER NOT NULL,
                pulse INTEGER NOT NULL
            );";
        } elseif ($this->driver === 'mysql') {
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

        $this->conn->exec($createTableSQL);
    }
}
