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

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();

    // Extract user details
    $googleId = $userInfo->id;
    $email = $userInfo->email;
    $name = $userInfo->name;

    // Створюємо підключення до бази даних
    try {
        /** @var array $config */
        $database = new Database($config);
        $conn = $database->getConnection();
        $usersTable = $database->getUsersTableName();

        // Check if user exists in the database
        $stmt = $conn->prepare("SELECT * FROM {$usersTable} WHERE google_id = :google_id OR email = :email");
        $stmt->execute(['google_id' => $googleId, 'email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO {$usersTable} (google_id, name, email) VALUES (:google_id, :name, :email)");
            $stmt->execute(['google_id' => $googleId, 'name' => $name, 'email' => $email]);
            $userId = $conn->lastInsertId();
        } else {
            $userId = $user['id'];
        }

        // Save user session
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;

    } catch (PDOException $e) {
        die(__FILE__ . ' +' . __LINE__ . " От, халепа, помилка з підключенням до бази даних: " . $e->getMessage());
    } catch (Exception $e) {
        die(__FILE__ . ' +' . __LINE__ . " От, халепа, помилка: " . $e->getMessage());
    }

    // Redirect to main page
    header('Location: index.php');
    exit;

} else {
    header('Location: login.php');
    exit;
}
