<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');
verify_csrf();
if (!empty($_COOKIE['letterly_remember'])) {
    $selector = explode(':', $_COOKIE['letterly_remember'], 2)[0];
    if (preg_match('/^[a-f0-9]{32}$/', $selector)) {
        $statement = $pdo->prepare('DELETE FROM remember_tokens WHERE selector = :selector');
        $statement->execute(['selector' => $selector]);
    }
    setcookie('letterly_remember', '', ['expires' => time() - 3600, 'path' => '/', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'httponly' => true, 'samesite' => 'Lax']);
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $parameters = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $parameters['path'], $parameters['domain'], $parameters['secure'], $parameters['httponly']);
}
session_destroy();
redirect('index.php');