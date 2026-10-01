<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $secure,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Your session expired. Please go back, refresh the page, and try again.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function take_flash(): ?array
{
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $message;
}

function current_user(): ?array
{
    if (!empty($_SESSION['user'])) {
        return $_SESSION['user'];
    }
    if (empty($_COOKIE['letterly_remember'])) {
        return null;
    }

    $parts = explode(':', $_COOKIE['letterly_remember'], 2);
    if (count($parts) !== 2 || !preg_match('/^[a-f0-9]{32}$/', $parts[0]) || !preg_match('/^[a-f0-9]{64}$/', $parts[1])) {
        return null;
    }

    require_once __DIR__ . '/../config/database.php';
    $statement = $pdo->prepare('SELECT t.token_hash, t.user_id, u.full_name, u.username, u.email FROM remember_tokens t JOIN users u ON u.id = t.user_id WHERE t.selector = :selector AND t.expires_at > NOW()');
    $statement->execute(['selector' => $parts[0]]);
    $record = $statement->fetch();
    if (!$record || !hash_equals($record['token_hash'], hash('sha256', $parts[1]))) {
        return null;
    }

    $_SESSION['user'] = ['id' => (int) $record['user_id'], 'full_name' => $record['full_name'], 'username' => $record['username'], 'email' => $record['email']];
    session_regenerate_id(true);
    return $_SESSION['user'];
}

function issue_remember_token(PDO $pdo, int $userId): void
{
    $selector = bin2hex(random_bytes(16));
    $validator = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30);
    $statement = $pdo->prepare('INSERT INTO remember_tokens (user_id, selector, token_hash, expires_at) VALUES (:user_id, :selector, :token_hash, :expires_at)');
    $statement->execute(['user_id' => $userId, 'selector' => $selector, 'token_hash' => hash('sha256', $validator), 'expires_at' => $expiresAt]);
    setcookie('letterly_remember', $selector . ':' . $validator, [
        'expires' => time() + 60 * 60 * 24 * 30,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function require_auth(): void
{
    if (!current_user()) {
        flash('error', 'Please sign in to continue.');
        redirect('login.php');
    }
}

function upload_photo(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > 8 * 1024 * 1024) {
        throw new RuntimeException('Choose an image smaller than 8 MB.');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime]) || !getimagesize($file['tmp_name'])) {
        throw new RuntimeException('Photos must be JPG, PNG, or WebP images.');
    }

    $directory = dirname(__DIR__) . '/uploads/photos';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('The photo upload folder could not be created.');
    }
    $name = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $name)) {
        throw new RuntimeException('The photo could not be saved.');
    }
    return 'uploads/photos/' . $name;
}

function letter_by_id(PDO $pdo, int $letterId, int $userId): ?array
{
    $statement = $pdo->prepare('SELECT * FROM letters WHERE id = :id AND user_id = :user_id');
    $statement->execute(['id' => $letterId, 'user_id' => $userId]);
    $letter = $statement->fetch();
    if (!$letter) {
        return null;
    }

    $statement = $pdo->prepare('SELECT image_path FROM letter_images WHERE letter_id = :id ORDER BY id DESC LIMIT 1');
    $statement->execute(['id' => $letterId]);
    $letter['image_path'] = $statement->fetchColumn() ?: null;
    $statement = $pdo->prepare('SELECT * FROM letter_stickers WHERE letter_id = :id ORDER BY id');
    $statement->execute(['id' => $letterId]);
    $letter['stickers'] = $statement->fetchAll();
    return $letter;
}