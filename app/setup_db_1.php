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

require_once 'config.php';
require_once 'Database.php';

try {
    /** @var array $config */
    $database = new Database($config);
    $conn = $database->getConnection();
    $database->createTable();
} catch (Exception $e) {
    die("Помилка: " . $e->getMessage());
}
