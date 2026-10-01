<?php
require_once __DIR__ . '/includes/auth.php';
$letterId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$letter = $letterId ? letter_by_id($pdo, $letterId, (int) current_user()['id']) : null;
if (!$letter) {
    http_response_code(404);
    exit('That letter could not be found.');
}
$stickerSymbols = ['flower' => '✿', 'heart' => '♥', 'sparkle' => '✳', 'sun' => '☼'];
$pageTitle = $letter['title'];
$extraCss = 'css/editor.css';
require __DIR__ . '/includes/header.php';
?>
<main class="view-page section-wrap"><div class="view-toolbar"><a class="text-link" href="library.php">← Back to your library</a><div><a class="button button-outline" href="edit-letter.php?id=<?= (int) $letter['id'] ?>">Edit letter <span>✎</span></a><button class="button" type="button" data-print-letter>Download / print <span>↓</span></button></div></div>
    <article class="letter-sheet view-sheet paper-<?= e($letter['template']) ?>" style="--paper-color: <?= e($letter['paper_color']) ?>; --ink-color: <?= e($letter['text_color']) ?>; --letter-size: <?= (int) $letter['text_size'] ?>px; --letter-align: <?= e($letter['text_align']) ?>"><div class="letter-topline"><span>WRITTEN WITH LETTERLY</span><span><?= e(date('F j, Y', strtotime($letter['created_at']))) ?></span></div><h1 class="preview-title"><?= e($letter['title']) ?></h1><p class="preview-greeting">Dear you,</p><div class="preview-content font-<?= e($letter['font_family']) ?>"><?= nl2br(e($letter['content'])) ?></div><?php if ($letter['image_path']): ?><img class="preview-photo" src="<?= e($letter['image_path']) ?>" alt="Photo added to <?= e($letter['title']) ?>"><?php endif; ?><div class="sticker-layer"><?php foreach ($letter['stickers'] as $sticker): ?><?php if (isset($stickerSymbols[$sticker['sticker_path']])): ?><span class="placed-sticker read-only" style="left: <?= e((string) $sticker['position_x']) ?>%; top: <?= e((string) $sticker['position_y']) ?>%; width: <?= (float) $sticker['width'] ?>px; height: <?= (float) $sticker['height'] ?>px" aria-hidden="true"><?= e($stickerSymbols[$sticker['sticker_path']]) ?></span><?php endif; ?><?php endforeach; ?></div><p class="preview-signoff">With all my love, <span>always</span></p></article>
</main>
<?php if (isset($_GET['print']) && $_GET['print'] === '1'): ?><script>window.addEventListener('load', () => window.print());</script><?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>