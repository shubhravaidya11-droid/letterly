<?php
require_once __DIR__ . '/includes/auth.php';
$user = current_user();
$userId = (int) $user['id'];
$statement = $pdo->prepare('SELECT COUNT(*) FROM letters WHERE user_id = :user_id');
$statement->execute(['user_id' => $userId]);
$letterCount = (int) $statement->fetchColumn();
$statement = $pdo->prepare('SELECT id, title, content, template, created_at, updated_at FROM letters WHERE user_id = :user_id ORDER BY updated_at DESC LIMIT 3');
$statement->execute(['user_id' => $userId]);
$recentLetters = $statement->fetchAll();
$pageTitle = 'Your writing desk';
require __DIR__ . '/includes/header.php';
?>
<main class="app-main section-wrap">
    <div class="dashboard-welcome"><div><p class="eyebrow">YOUR LETTERLY DESK</p><h1>Welcome back, <?= e(explode(' ', $user['full_name'])[0]) ?> <span>♡</span></h1><p>Some words are worth coming back to.</p></div><a class="button" href="create-letter.php">Write a letter <span>↗</span></a></div>
    <div class="dashboard-stats"><div class="stat-block"><span>LETTERS SAVED</span><strong><?= $letterCount ?></strong></div><div class="stat-block"><span>YOUR LIBRARY</span><a href="library.php">Open collection ↗</a></div><div class="stat-block"><span>ACCOUNT</span><a href="account.php">Account settings ↗</a></div></div>
    <section class="recent-section"><div class="section-heading"><div><p class="eyebrow">THE LATEST PAGES</p><h2>Recently written</h2></div><a class="text-link" href="library.php">See your library <span>↗</span></a></div>
        <?php if ($recentLetters): ?><div class="letter-card-grid"><?php foreach ($recentLetters as $letter): ?><article class="saved-letter-card"><a class="saved-letter-preview paper-<?= e($letter['template']) ?>" href="view-letter.php?id=<?= (int) $letter['id'] ?>"><span><?= e($letter['title']) ?></span><small><?= nl2br(e(mb_substr($letter['content'], 0, 100))) ?></small></a><div class="saved-letter-meta"><div><h3><a href="view-letter.php?id=<?= (int) $letter['id'] ?>"><?= e($letter['title']) ?></a></h3><p>Updated <?= e(date('M j, Y', strtotime($letter['updated_at']))) ?></p></div><a class="icon-link" href="edit-letter.php?id=<?= (int) $letter['id'] ?>" aria-label="Edit <?= e($letter['title']) ?>">↗</a></div></article><?php endforeach; ?></div><?php else: ?><div class="empty-state"><span>✿</span><h3>Your first letter is waiting.</h3><p>Start with a feeling, a memory, or just a hello.</p><a class="text-link" href="create-letter.php">Create your first letter ↗</a></div><?php endif; ?>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>