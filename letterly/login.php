<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';
if (current_user()) redirect('dashboard.php');
$error = '';
$identity = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $identity = trim((string) ($_POST['identity'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $statement = $pdo->prepare('SELECT id, full_name, username, email, password FROM users WHERE email = :email OR username = :username LIMIT 1');
    $statement->execute(['email' => $identity, 'username' => $identity]);
    $user = $statement->fetch();
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int) $user['id'], 'full_name' => $user['full_name'], 'username' => $user['username'], 'email' => $user['email']];
        if (!empty($_POST['remember'])) issue_remember_token($pdo, (int) $user['id']);
        redirect('dashboard.php');
    }
    $error = 'We couldn’t match those details. Please try again.';
}
$pageTitle = 'Log in';
$extraCss = 'css/auth.css';
require __DIR__ . '/includes/header.php';
?>
<main class="auth-page"><div class="auth-aside"><span class="auth-aside-mark">L</span><p class="eyebrow">Welcome back</p><h1>Your words<br>are right where<br>you <em>left them.</em></h1><span class="auth-aside-note">A quiet place to return to.</span></div>
    <section class="auth-panel"><div class="auth-panel-inner"><p class="eyebrow">Come on in</p><h2>Log in to Letterly</h2><p class="auth-subtitle">Pick up where your heart left off.</p>
        <?php if ($error): ?><div class="form-errors" role="alert"><p><?= e($error) ?></p></div><?php endif; ?>
        <form method="post" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>Email or username<input name="identity" autocomplete="username" required value="<?= e($identity) ?>"></label>
            <label>Password<input name="password" type="password" autocomplete="current-password" required></label>
            <label class="check-label"><input type="checkbox" name="remember" value="1"><span>Remember me for 30 days</span></label>
            <button class="button auth-submit" type="submit">Log in <span>↗</span></button>
        </form><p class="auth-switch">New to Letterly? <a href="register.php">Create an account</a></p>
    </div></section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>