<?php
require_once __DIR__ . '/includes/auth.php';
$userId = (int) current_user()['id'];
$letterId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'letter_id', FILTER_VALIDATE_INT);
$letter = $letterId ? letter_by_id($pdo, $letterId, $userId) : null;
if ($letterId && !$letter) {
    http_response_code(404);
    exit('That letter could not be found.');
}
$templates = ['rose', 'sunshine', 'midnight'];
$fonts = ['serif', 'handwritten', 'sans'];
$alignments = ['left', 'center', 'right'];
$paperColors = ['#fffdf8', '#fff4ed', '#f3f0e7', '#f8f0d8'];
$stickers = ['flower' => '✿', 'heart' => '♥', 'sparkle' => '✳', 'sun' => '☼'];
$requestedTemplate = (string) ($_GET['template'] ?? 'rose');
if (!in_array($requestedTemplate, $templates, true)) $requestedTemplate = 'rose';
$values = [
    'title' => $letter['title'] ?? '',
    'content' => $letter['content'] ?? '',
    'template' => $letter['template'] ?? $requestedTemplate,
    'font_family' => $letter['font_family'] ?? 'serif',
    'text_size' => $letter['text_size'] ?? 18,
    'text_color' => $letter['text_color'] ?? '#49372f',
    'text_align' => $letter['text_align'] ?? 'left',
    'paper_color' => $letter['paper_color'] ?? match ($letter['template'] ?? $requestedTemplate) {
        'sunshine' => '#fff9e8',
        'midnight' => '#f3f1eb',
        default => '#fff7f1',
    },
];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $values['title'] = trim((string) ($_POST['title'] ?? ''));
    $values['content'] = trim((string) ($_POST['content'] ?? ''));
    $values['template'] = (string) ($_POST['template'] ?? 'rose');
    $values['font_family'] = (string) ($_POST['font_family'] ?? 'serif');
    $values['text_size'] = filter_var($_POST['text_size'] ?? 18, FILTER_VALIDATE_INT);
    $values['text_color'] = (string) ($_POST['text_color'] ?? '#49372f');
    $values['text_align'] = (string) ($_POST['text_align'] ?? 'left');
    $values['paper_color'] = (string) ($_POST['paper_color'] ?? '#fffdf8');
    if ($values['title'] === '' || mb_strlen($values['title']) > 160) $errors[] = 'Give your letter a title, up to 160 characters.';
    if ($values['content'] === '' || mb_strlen($values['content']) > 30000) $errors[] = 'Write something in your letter (up to 30,000 characters).';
    if (!in_array($values['template'], $templates, true)) $errors[] = 'Choose one of the available templates.';
    if (!in_array($values['font_family'], $fonts, true)) $errors[] = 'Choose a valid font.';
    if (!in_array($values['text_align'], $alignments, true)) $errors[] = 'Choose a valid text alignment.';
    if (!in_array($values['paper_color'], $paperColors, true)) $errors[] = 'Choose a valid paper color.';
    if (!is_int($values['text_size']) || $values['text_size'] < 14 || $values['text_size'] > 28) $errors[] = 'Text size must be between 14 and 28.';
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $values['text_color'])) $errors[] = 'Choose a valid text color.';

    $stickerKeys = $_POST['sticker'] ?? [];
    $stickerXs = $_POST['sticker_x'] ?? [];
    $stickerYs = $_POST['sticker_y'] ?? [];
    $stickerWidths = $_POST['sticker_w'] ?? [];
    $stickerHeights = $_POST['sticker_h'] ?? [];
    if (!is_array($stickerKeys) || count($stickerKeys) > 12) $errors[] = 'Letters can have up to 12 stickers.';
    if (!is_array($stickerXs) || !is_array($stickerYs) || !is_array($stickerWidths) || !is_array($stickerHeights)) $errors[] = 'Sticker details are invalid.';
    foreach (is_array($stickerKeys) ? $stickerKeys : [] as $index => $key) {
        if (!isset($stickers[$key]) || !isset($stickerXs[$index], $stickerYs[$index], $stickerWidths[$index], $stickerHeights[$index]) || !is_numeric($stickerXs[$index]) || !is_numeric($stickerYs[$index]) || !is_numeric($stickerWidths[$index]) || !is_numeric($stickerHeights[$index]) || $stickerXs[$index] < 0 || $stickerXs[$index] > 100 || $stickerYs[$index] < 0 || $stickerYs[$index] > 100 || $stickerWidths[$index] < 28 || $stickerWidths[$index] > 100 || $stickerHeights[$index] < 28 || $stickerHeights[$index] > 100) {
            $errors[] = 'One of the sticker positions is invalid.';
            break;
        }
    }
    if (!$errors) {
        try {
            $newPhoto = upload_photo($_FILES['photo'] ?? []);
            $pdo->beginTransaction();
            $parameters = [
                'user_id' => $userId,
                'title' => $values['title'],
                'content' => $values['content'],
                'template' => $values['template'],
                'font_family' => $values['font_family'],
                'text_size' => $values['text_size'],
                'text_color' => $values['text_color'],
                'text_align' => $values['text_align'],
                'paper_color' => $values['paper_color'],
            ];
            if ($letter) {
                $statement = $pdo->prepare('UPDATE letters SET title = :title, content = :content, template = :template, font_family = :font_family, text_size = :text_size, text_color = :text_color, text_align = :text_align, paper_color = :paper_color WHERE id = :id AND user_id = :user_id');
                $statement->execute($parameters + ['id' => $letterId]);
                $savedId = $letterId;
                $pdo->prepare('DELETE FROM letter_stickers WHERE letter_id = :id')->execute(['id' => $savedId]);
            } else {
                $statement = $pdo->prepare('INSERT INTO letters (user_id, title, content, template, font_family, text_size, text_color, text_align, paper_color) VALUES (:user_id, :title, :content, :template, :font_family, :text_size, :text_color, :text_align, :paper_color)');
                $statement->execute($parameters);
                $savedId = (int) $pdo->lastInsertId();
            }
            if ($newPhoto) {
                $statement = $pdo->prepare('INSERT INTO letter_images (letter_id, image_path) VALUES (:letter_id, :image_path)');
                $statement->execute(['letter_id' => $savedId, 'image_path' => $newPhoto]);
            }
            if (is_array($stickerKeys)) {
                $statement = $pdo->prepare('INSERT INTO letter_stickers (letter_id, sticker_path, position_x, position_y, width, height) VALUES (:letter_id, :sticker_path, :position_x, :position_y, :width, :height)');
                foreach ($stickerKeys as $index => $key) {
                    $statement->execute(['letter_id' => $savedId, 'sticker_path' => $key, 'position_x' => (float) $stickerXs[$index], 'position_y' => (float) $stickerYs[$index], 'width' => (float) $stickerWidths[$index], 'height' => (float) $stickerHeights[$index]]);
                }
            }
            $pdo->commit();
            flash('success', $letter ? 'Your letter has been updated.' : 'Your letter has been saved to your library.');
            redirect('view-letter.php?id=' . $savedId);
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors[] = $exception instanceof RuntimeException ? $exception->getMessage() : 'Your letter could not be saved. Please try again.';
        }
    }
}
$imagePath = $letter['image_path'] ?? null;
$savedStickers = $letter['stickers'] ?? [];
$pageTitle = $letter ? 'Edit your letter' : 'Create a letter';
$extraCss = 'css/editor.css';
require __DIR__ . '/includes/header.php';
?>
<main class="editor-page section-wrap">
    <div class="editor-heading"><div><p class="eyebrow">YOUR WRITING DESK</p><h1><?= $letter ? 'A little more to say?' : 'Start with a feeling.' ?></h1></div><a class="text-link" href="library.php">← Back to library</a></div>
    <?php if ($errors): ?><div class="form-errors editor-errors" role="alert"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
    <form class="editor-layout" method="post" action="create-letter.php<?= $letter ? '?id=' . (int) $letterId : '' ?>" enctype="multipart/form-data" id="letter-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="letter_id" value="<?= (int) ($letterId ?? 0) ?>">
        <aside class="editor-controls">
            <section class="control-group"><p class="control-label">01 — THE WORDS</p><label>Letter title<input name="title" maxlength="160" required placeholder="A letter to someone..." value="<?= e($values['title']) ?>"></label><label>Your letter<textarea name="content" id="letter-content" maxlength="30000" required placeholder="Dear you,&#10;&#10;I've been meaning to tell you..."><?= e($values['content']) ?></textarea></label><div class="word-count"><span id="word-count">0 words</span><span>Just let it be yours.</span></div></section>
            <section class="control-group"><p class="control-label">02 — THE FEELING</p><label>Choose a page<select name="template" id="template-select"><option value="rose" <?= $values['template'] === 'rose' ? 'selected' : '' ?>>The keepsake · warm rose</option><option value="sunshine" <?= $values['template'] === 'sunshine' ? 'selected' : '' ?>>The postcard · soft sunshine</option><option value="midnight" <?= $values['template'] === 'midnight' ? 'selected' : '' ?>>The late note · ink & paper</option></select></label><div class="control-row"><label>Lettering<select name="font_family" id="font-select"><option value="serif" <?= $values['font_family'] === 'serif' ? 'selected' : '' ?>>Bookish</option><option value="handwritten" <?= $values['font_family'] === 'handwritten' ? 'selected' : '' ?>>Handwritten</option><option value="sans" <?= $values['font_family'] === 'sans' ? 'selected' : '' ?>>Simple</option></select></label><label>Size <output id="size-output"><?= (int) $values['text_size'] ?>px</output><input type="range" name="text_size" id="size-select" min="14" max="28" value="<?= (int) $values['text_size'] ?>"></label></div><div class="control-row"><label>Ink color<input type="color" name="text_color" id="color-select" value="<?= e($values['text_color']) ?>"></label><label>Paper<select name="paper_color" id="paper-select"><option value="#fffdf8" <?= $values['paper_color'] === '#fffdf8' ? 'selected' : '' ?>>Soft white</option><option value="#fff4ed" <?= $values['paper_color'] === '#fff4ed' ? 'selected' : '' ?>>Rose cream</option><option value="#f3f0e7" <?= $values['paper_color'] === '#f3f0e7' ? 'selected' : '' ?>>Oat paper</option><option value="#f8f0d8" <?= $values['paper_color'] === '#f8f0d8' ? 'selected' : '' ?>>Sun-warmed</option></select></label></div><div class="alignment-control" role="group" aria-label="Text alignment"><span>Alignment</span><?php foreach ($alignments as $alignment): ?><label><input type="radio" name="text_align" value="<?= $alignment ?>" <?= $values['text_align'] === $alignment ? 'checked' : '' ?>><span><?= ucfirst($alignment) ?></span></label><?php endforeach; ?></div></section>
            <section class="control-group"><p class="control-label">03 — THE LITTLE THINGS</p><label class="upload-label">Add a photograph<input type="file" name="photo" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG or WebP · up to 8 MB</small></label><div class="sticker-picker"><span>Add a sticker</span><div><?php foreach ($stickers as $key => $symbol): ?><button type="button" class="sticker-add" data-sticker="<?= e($key) ?>" aria-label="Add <?= e($key) ?> sticker"><?= e($symbol) ?></button><?php endforeach; ?></div></div><p class="control-hint">Drag to move, pull the corner to resize, or tap to remove.</p></section>
            <div class="editor-actions"><button class="button" type="submit">Save letter <span>♡</span></button><button class="button button-outline" type="button" data-print-letter>Download letter <span>↓</span></button><button class="preview-link" type="button" data-preview-letter>Preview your page ↗</button></div>
        </aside>
        <section class="preview-panel" id="letter-preview-panel"><div class="preview-toolbar"><span>YOUR LETTER, AS IT WILL FEEL</span><span><?= e(date('F j, Y')) ?></span></div><article class="letter-sheet editor-sheet paper-<?= e($values['template']) ?>" id="letter-preview" style="--paper-color: <?= e($values['paper_color']) ?>; --ink-color: <?= e($values['text_color']) ?>; --letter-size: <?= (int) $values['text_size'] ?>px; --letter-align: <?= e($values['text_align']) ?>">
                <div class="letter-topline"><span>WRITTEN WITH LETTERLY</span><span><?= e(date('M j, Y')) ?></span></div><h2 class="preview-title" id="preview-title"><?= e($values['title'] ?: 'A letter to someone...') ?></h2><p class="preview-greeting">Dear you,</p><div class="preview-content font-<?= e($values['font_family']) ?>" id="preview-content"><?= nl2br(e(trim($values['content'] ?: 'Your words will find their way here.'))) ?></div><?php if ($imagePath): ?><img class="preview-photo" id="preview-photo" src="<?= e($imagePath) ?>" alt="Your letter photograph"><?php else: ?><img class="preview-photo" id="preview-photo" alt="Your letter photograph" hidden><?php endif; ?><div class="sticker-layer" id="sticker-layer"><?php foreach ($savedStickers as $sticker): ?><?php if (isset($stickers[$sticker['sticker_path']])): ?><button type="button" class="placed-sticker" data-sticker="<?= e($sticker['sticker_path']) ?>" style="left: <?= e((string) $sticker['position_x']) ?>%; top: <?= e((string) $sticker['position_y']) ?>%; width: <?= (float) $sticker['width'] ?>px; height: <?= (float) $sticker['height'] ?>px" aria-label="Move, resize, or remove sticker"><span class="sticker-symbol"><?= e($stickers[$sticker['sticker_path']]) ?></span><span class="sticker-resize" aria-hidden="true"></span><input type="hidden" name="sticker[]" value="<?= e($sticker['sticker_path']) ?>"><input type="hidden" name="sticker_x[]" value="<?= e((string) $sticker['position_x']) ?>"><input type="hidden" name="sticker_y[]" value="<?= e((string) $sticker['position_y']) ?>"><input type="hidden" name="sticker_w[]" value="<?= e((string) $sticker['width']) ?>"><input type="hidden" name="sticker_h[]" value="<?= e((string) $sticker['height']) ?>"></button><?php endif; ?><?php endforeach; ?></div><p class="preview-signoff">With all my love, <span>always</span></p>
            </article><p class="preview-footnote">A page of your own, saved just for you.</p></section>
    </form>
</main>
<script src="js/editor.js" defer></script>
<?php require __DIR__ . '/includes/footer.php'; ?>