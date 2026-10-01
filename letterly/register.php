<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';
if (current_user()) {
    redirect('dashboard.php');
}
$errors = [];
$values = ['full_name' => '', 'email' => '', 'username' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($values as $key => $_) {
        $values[$key] = trim((string) ($_POST[$key] ?? ''));
    }
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');
    if (mb_strlen($values['full_name']) < 2 || mb_strlen($values['full_name']) > 120) $errors[] = 'Please enter your full name.';
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($values['email']) > 190) $errors[] = 'Enter a valid email address.';
    if (!preg_match('/^[A-Za-z0-9_]{3,40}$/', $values['username'])) $errors[] = 'Username must be 3–40 letters, numbers, or underscores.';
    if (strlen($password) < 8 || strlen($password) > 200) $errors[] = 'Your password must be at least 8 characters.';
    if ($password !== $confirmPassword) $errors[] = 'Those passwords do not match.';
    if (!$errors) {
        $statement = $pdo->prepare('SELECT id FROM users WHERE email = :email OR username = :username LIMIT 1');
        $statement->execute(['email' => $values['email'], 'username' => $values['username']]);
        if ($statement->fetch()) {
            $errors[] = 'That email or username is already in use.';
        } else {
            $statement = $pdo->prepare('INSERT INTO users (full_name, username, email, password) VALUES (:full_name, :username, :email, :password)');
            $statement->execute(['full_name' => $values['full_name'], 'username' => $values['username'], 'email' => $values['email'], 'password' => password_hash($password, PASSWORD_DEFAULT)]);
            flash('success', 'Your account is ready. Sign in and write your first letter.');
            redirect('login.php');
        }
    }
}
$pageTitle = 'Create your account';
$extraCss = 'css/auth.css';
require __DIR__ . '/includes/header.php';
?>
<main class="auth-page"><div class="auth-aside auth-aside-register"><span class="auth-aside-mark">L</span><p class="eyebrow">A small home for your words</p><h1>Keep the things<br>you don't want<br>to <em>forget.</em></h1><span class="auth-aside-note">Your words. Your memories. Yours to keep.</span></div>
    <section class="auth-panel"><div class="auth-panel-inner"><p class="eyebrow">Make yourself at home</p><h2>Create your account</h2><p class="auth-subtitle">Your letters will be waiting here when you're ready.</p>
        <?php if ($errors): ?><div class="form-errors" role="alert"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
        <form method="post" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>Full name<input name="full_name" autocomplete="name" required maxlength="120" value="<?= e($values['full_name']) ?>"></label>
            <label>Email address<input name="email" type="email" autocomplete="email" required maxlength="190" value="<?= e($values['email']) ?>"></label>
            <label>Username<input name="username" autocomplete="username" required minlength="3" maxlength="40" pattern="[A-Za-z0-9_]+" value="<?= e($values['username']) ?>"></label>
            <div class="auth-fields-two"><label>Password<input name="password" type="password" autocomplete="new-password" required minlength="8"></label><label>Confirm password<input name="confirm_password" type="password" autocomplete="new-password" required minlength="8"></label></div>
            <button class="button auth-submit" type="submit">Create account <span>↗</span></button>
        </form><p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
    </div></section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>