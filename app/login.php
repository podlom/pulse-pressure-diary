<?php

declare(strict_types=1);

/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2025
 */

// Define a constant to be used for allowing direct access
define('ALLOW_DIRECT_ACCESS', true);

require_once 'config.php';
require_once 'Database.php';
require_once 'vendor/autoload.php';


use Dotenv\Dotenv;

session_start();

// Завантаження змінних середовища з .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$clientId = $_ENV['GOOGLE_CLIENT_ID'];
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];
$callbackUrl = $_ENV['GOOGLE_CALLBACK_URL'];
// TODO: add empty GOOGLE_ variables validation...

$client = new Google_Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($callbackUrl);
$client->addScope('email');
$client->addScope('profile');

$loginUrl = $client->createAuthUrl();
header('Location: ' . $loginUrl);
exit;
