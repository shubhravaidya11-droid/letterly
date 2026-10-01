<?php
require_once __DIR__ . '/includes/auth.php';
$userId = (int) current_user()['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verify_csrf();
    $letterId = filter_input(INPUT_POST, 'letter_id', FILTER_VALIDATE_INT);
    if ($letterId) {
        $statement = $pdo->prepare('DELETE FROM letters WHERE id = :id AND user_id = :user_id');
        $statement->execute(['id' => $letterId, 'user_id' => $userId]);
        flash('success', $statement->rowCount() ? 'Your letter has been deleted.' : 'That letter could not be found.');
    }
    redirect('library.php');
}
$statement = $pdo->prepare('SELECT l.*, (SELECT image_path FROM letter_images WHERE letter_id = l.id ORDER BY id DESC LIMIT 1) AS image_path FROM letters l WHERE l.user_id = :user_id ORDER BY l.updated_at DESC');
$statement->execute(['user_id' => $userId]);
$letters = $statement->fetchAll();
$pageTitle = 'My library';
require __DIR__ . '/includes/header.php';
?>
<main class="app-main section-wrap library-page">
    <div class="dashboard-welcome"><div><p class="eyebrow">YOUR PERSONAL COLLECTION</p><h1>My library <span>♡</span></h1><p><?= count($letters) ?> <?= count($letters) === 1 ? 'letter' : 'letters' ?> kept close.</p></div><a class="button" href="create-letter.php">New letter <span>↗</span></a></div>
    <?php if ($letters): ?><div class="library-grid"><?php foreach ($letters as $letter): ?><article class="library-card"><a class="saved-letter-preview paper-<?= e($letter['template']) ?>" href="view-letter.php?id=<?= (int) $letter['id'] ?>"><?php if ($letter['image_path']): ?><img class="library-photo" src="<?= e($letter['image_path']) ?>" alt="Photo in <?= e($letter['title']) ?>"><?php endif; ?><span><?= e($letter['title']) ?></span><small><?= nl2br(e(mb_substr($letter['content'], 0, 140))) ?></small><span class="preview-date"><?= e(date('F j, Y', strtotime($letter['created_at']))) ?></span></a><div class="library-card-bottom"><div><h2><a href="view-letter.php?id=<?= (int) $letter['id'] ?>"><?= e($letter['title']) ?></a></h2><p>Created <?= e(date('M j, Y', strtotime($letter['created_at']))) ?></p></div><div class="card-actions"><a href="edit-letter.php?id=<?= (int) $letter['id'] ?>" aria-label="Edit letter">✎</a><a href="view-letter.php?id=<?= (int) $letter['id'] ?>&amp;print=1" aria-label="Download letter">↓</a><form method="post" onsubmit="return confirm('Are you sure you want to delete this letter?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="letter_id" value="<?= (int) $letter['id'] ?>"><button type="submit" aria-label="Delete letter">×</button></form></div></div></article><?php endforeach; ?></div><?php else: ?><div class="empty-state"><span>✿</span><h2>A little room for your words.</h2><p>Your saved letters will find a home here.</p><a class="button" href="create-letter.php">Write your first letter <span>↗</span></a></div><?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>