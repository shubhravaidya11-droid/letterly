<?php
declare(strict_types=1);

$databaseHost = getenv('LETTERLY_DB_HOST') ?: '127.0.0.1';
$databaseName = getenv('LETTERLY_DB_NAME') ?: 'letterly_db';
$databaseUser = getenv('LETTERLY_DB_USER') ?: 'root';
$databasePassword = getenv('LETTERLY_DB_PASSWORD') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$databaseHost};dbname={$databaseName};charset=utf8mb4",
        $databaseUser,
        $databasePassword,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    http_response_code(500);
    exit('Letterly could not connect to MySQL. Import database/letterly.sql and check config/database.php.');
}