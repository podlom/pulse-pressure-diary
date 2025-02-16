<?php

declare(strict_types=1);

/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2025
 */

// Define a constant to be used for allowing direct access
define('ALLOW_DIRECT_ACCESS', true);

session_start();
session_unset();
session_destroy();

header('Location: login.php');
exit;
