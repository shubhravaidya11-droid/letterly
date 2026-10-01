<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'Letterly';
$user = current_user();
$notice = take_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f8f3eb">
    <title><?= e($pageTitle) ?> | Letterly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&family=Kalam:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <?php if (!empty($extraCss)): ?><link rel="stylesheet" href="<?= e($extraCss) ?>"><?php endif; ?>
    <script src="js/main.js" defer></script>
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php"><span class="brand-mark">L</span><span>letterly<span class="brand-period">.</span></span></a>
    <button class="menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false"><span></span><span></span></button>
    <nav class="main-nav" aria-label="Main navigation">
        <a href="index.php#home">Home</a>
        <a href="create-letter.php">Create letter</a>
        <a href="index.php#templates">Templates</a>
        <a href="<?= $user ? 'library.php' : 'login.php' ?>">My library</a>
        <a href="index.php#about">About</a>
        <div class="nav-account">
            <?php if ($user): ?>
                <a class="nav-login" href="dashboard.php"><?= e(explode(' ', $user['full_name'])[0]) ?></a>
                <form class="nav-logout-form" method="post" action="logout.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="button button-small" type="submit">Log out</button></form>
            <?php else: ?>
                <a class="nav-login" href="login.php">Log in</a>
                <a class="button button-small" href="register.php">Create account</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
<?php if ($notice): ?><div class="flash flash-<?= e($notice['type']) ?>" role="status"><?= e($notice['message']) ?></div><?php endif; ?>