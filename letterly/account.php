<?php
require_once __DIR__ . '/includes/auth.php';
$user = current_user();
$userId = (int) $user['id'];
$errors = [];
$values = ['full_name' => $user['full_name'], 'email' => $user['email'], 'username' => $user['username']];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($values as $key => $_) $values[$key] = trim((string) ($_POST[$key] ?? ''));
    $currentPassword = (string) ($_POST['current_password'] ?? '');
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');
    if (mb_strlen($values['full_name']) < 2 || mb_strlen($values['full_name']) > 120) $errors[] = 'Please enter your full name.';
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($values['email']) > 190) $errors[] = 'Enter a valid email address.';
    if (!preg_match('/^[A-Za-z0-9_]{3,40}$/', $values['username'])) $errors[] = 'Username must be 3–40 letters, numbers, or underscores.';
    $statement = $pdo->prepare('SELECT password FROM users WHERE id = :id');
    $statement->execute(['id' => $userId]);
    $passwordHash = $statement->fetchColumn();
    if (!password_verify($currentPassword, (string) $passwordHash)) $errors[] = 'Enter your current password to save account changes.';
    if ($newPassword !== '' && strlen($newPassword) < 8) $errors[] = 'A new password must be at least 8 characters.';
    if ($newPassword !== $confirmPassword) $errors[] = 'The new passwords do not match.';
    if (!$errors) {
        $statement = $pdo->prepare('SELECT id FROM users WHERE (email = :email OR username = :username) AND id <> :id LIMIT 1');
        $statement->execute(['email' => $values['email'], 'username' => $values['username'], 'id' => $userId]);
        if ($statement->fetch()) {
            $errors[] = 'That email or username is already in use.';
        } else {
            if ($newPassword !== '') {
                $statement = $pdo->prepare('UPDATE users SET full_name = :full_name, email = :email, username = :username, password = :password WHERE id = :id');
                $statement->execute(['full_name' => $values['full_name'], 'email' => $values['email'], 'username' => $values['username'], 'password' => password_hash($newPassword, PASSWORD_DEFAULT), 'id' => $userId]);
            } else {
                $statement = $pdo->prepare('UPDATE users SET full_name = :full_name, email = :email, username = :username WHERE id = :id');
                $statement->execute(['full_name' => $values['full_name'], 'email' => $values['email'], 'username' => $values['username'], 'id' => $userId]);
            }
            $_SESSION['user'] = ['id' => $userId, 'full_name' => $values['full_name'], 'username' => $values['username'], 'email' => $values['email']];
            flash('success', 'Your account details have been updated.');
            redirect('account.php');
        }
    }
}
$pageTitle = 'Account settings';
$extraCss = 'css/auth.css';
require __DIR__ . '/includes/header.php';
?>
<main class="account-page section-wrap"><div class="editor-heading"><div><p class="eyebrow">YOUR ACCOUNT</p><h1>Keep your details up to date.</h1></div><a class="text-link" href="dashboard.php">← Your writing desk</a></div>
    <section class="account-panel"><p class="auth-subtitle">For your security, enter your current password before saving changes.</p><?php if ($errors): ?><div class="form-errors" role="alert"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
        <form method="post" class="auth-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Full name<input name="full_name" autocomplete="name" required maxlength="120" value="<?= e($values['full_name']) ?>"></label><label>Email address<input name="email" type="email" autocomplete="email" required maxlength="190" value="<?= e($values['email']) ?>"></label><label>Username<input name="username" autocomplete="username" required minlength="3" maxlength="40" pattern="[A-Za-z0-9_]+" value="<?= e($values['username']) ?>"></label><label>Current password<input name="current_password" type="password" autocomplete="current-password" required></label><div class="auth-fields-two"><label>New password <span class="optional-label">Optional</span><input name="new_password" type="password" autocomplete="new-password" minlength="8"></label><label>Confirm new password<input name="confirm_password" type="password" autocomplete="new-password"></label></div><button class="button auth-submit" type="submit">Save account details <span>↗</span></button></form>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>