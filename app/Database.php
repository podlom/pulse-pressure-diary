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
    private array $config;

    private PDO $conn;

    private string $driver;

    private string $tableName;

    private string $usersTableName;

    public function __construct(array $config)
    {
        /** @var array $this->config */
        $this->config = $config;

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

            $this->tableName = $this->config['db']['tableName'];
            $this->usersTableName = $this->config['db']['usersTableName'];
        } catch (PDOException $e) {
            die(__FILE__ . ' +' . __LINE__ . " Помилка підключення до бази даних: " . $e->getMessage());
        } catch (Exception $e) {
            die(__FILE__ . ' +' . __LINE__ . " Помилка: " . $e->getMessage());
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

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getUsersTableName(): string
    {
        return $this->usersTableName;
    }

    public function createTables(): void
    {
        /** @var array $this->config */

        $createTableSQL = "";

        if ($this->driver === 'sqlite') {
            $createTableSQL = "CREATE TABLE IF NOT EXISTS {$this->getTableName()} (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                date TEXT NOT NULL,
                time_period TEXT NOT NULL,
                systolic_pressure INTEGER NOT NULL,
                diastolic_pressure INTEGER NOT NULL,
                pulse INTEGER NOT NULL
            );

            CREATE TABLE IF NOT EXISTS {$this->getUsersTableName()} (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                google_id TEXT NOT NULL,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                created_at TEXT NOT NULL
            );";
        } elseif ($this->driver === 'mysql') {
            $createTableSQL = "CREATE TABLE IF NOT EXISTS {$this->getTableName()} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                date DATE NOT NULL,
                time_period VARCHAR(50) NOT NULL,
                systolic_pressure INT NOT NULL,
                diastolic_pressure INT NOT NULL,
                pulse INT NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET={$this->config['db']['mysql']['charset']};

            CREATE TABLE IF NOT EXISTS {$this->getUsersTableName()} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                google_id VARCHAR(255) UNIQUE,
                name VARCHAR(255),
                email VARCHAR(255) UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET={$this->config['db']['mysql']['charset']};";
        }

        $this->conn->exec($createTableSQL);
    }
}
