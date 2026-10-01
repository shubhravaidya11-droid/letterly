<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Write it. Keep it. Remember it.';
require __DIR__ . '/includes/header.php';
?>
<main>
    <section class="hero section-wrap" id="home">
        <div class="hero-copy reveal">
            <p class="eyebrow"><span></span> A little place for your big feelings</p>
            <h1>Write it.<br><em>Keep it.</em><br>Remember it.</h1>
            <p class="hero-intro">Turn your thoughts, memories and feelings into beautiful digital letters.</p>
            <div class="hero-actions"><a class="button" href="create-letter.php">Create a letter <span aria-hidden="true">↗</span></a><a class="text-link" href="#templates">Explore templates <span aria-hidden="true">↓</span></a></div>
            <div class="hero-note"><span class="note-avatars">M · J · Y</span><span>For the people, places & versions of you.</span></div>
        </div>
        <div class="hero-art reveal" aria-label="A decorated letter with a photograph">
            <div class="art-orbit"></div><span class="floating-spark spark-one">✳</span><span class="floating-spark spark-two">✦</span>
            <article class="letter-sheet hero-sheet">
                <div class="letter-topline"><span>FROM THE HEART</span><span>OCTOBER 01, 2026</span></div>
                <p class="letter-greeting">Dear you,</p>
                <p class="letter-excerpt">I hope you remember this little afternoon. The light was golden, the coffee was warm, and for once, there was nowhere else we needed to be.</p>
                <div class="letter-photo-wrap"><img src="https://images.unsplash.com/photo-1470252649378-9c29740c9fa8?auto=format&fit=crop&w=700&q=85" alt="Golden evening light over a quiet field"><span class="photo-caption">that slow Sunday feeling</span></div>
                <p class="letter-signoff">With all my love, <span>always</span></p>
                <span class="sheet-sticker sticker-flower">✿</span><span class="sheet-sticker sticker-heart">♥</span>
            </article>
            <div class="hero-caption"><span class="caption-line"></span> A moment, pressed in paper</div>
        </div>
    </section>

    <section class="intro-band" id="about"><div class="section-wrap intro-inner"><span class="section-index">A NOTE TO BEGIN</span><p>Some things feel better <em>when you write them down.</em></p><span class="intro-flower">✳</span></div></section>

    <section class="section-wrap feature-section" id="features">
        <div class="section-heading reveal"><div><p class="eyebrow">Thoughtfully made, just for you</p><h2>Everything you need<br>to create your letter.</h2></div><p>Small details make a world of difference. Bring the whole feeling with you.</p></div>
        <div class="feature-grid">
            <article class="feature-item reveal"><span class="feature-icon icon-photo">▧</span><h3>Add photos</h3><p>Add your favorite memories and photographs directly to your letters.</p></article>
            <article class="feature-item reveal"><span class="feature-icon icon-sticker">✧</span><h3>Add stickers</h3><p>Decorate your letters with cute, aesthetic and expressive stickers.</p></article>
            <article class="feature-item reveal"><span class="feature-icon icon-download">↓</span><h3>Download letters</h3><p>Download your finished letter and keep a personal copy.</p></article>
            <article class="feature-item reveal"><span class="feature-icon icon-library">▤</span><h3>Your own library</h3><p>Save letters to your private library and find them whenever you need.</p></article>
            <article class="feature-item reveal"><span class="feature-icon icon-template">▧</span><h3>Beautiful templates</h3><p>Start writing with a considered design that already feels like you.</p></article>
            <article class="feature-item reveal"><span class="feature-icon icon-private">♡</span><h3>Private by nature</h3><p>Your saved words stay connected to your personal account.</p></article>
        </div>
    </section>

    <section class="steps-section"><div class="section-wrap"><div class="section-heading steps-heading reveal"><div><p class="eyebrow">A simple ritual</p><h2>Three little steps.</h2></div><span class="handwritten">then it's yours</span></div><div class="steps-grid">
        <article class="step-card reveal"><span class="step-number">01</span><div class="step-rule"></div><h3>Create</h3><p>Choose a template and start writing your letter.</p></article>
        <article class="step-card reveal"><span class="step-number">02</span><div class="step-rule"></div><h3>Personalize</h3><p>Add photos, stickers, colors and decorative details.</p></article>
        <article class="step-card reveal"><span class="step-number">03</span><div class="step-rule"></div><h3>Save & download</h3><p>Keep a copy or save your letter in your library.</p></article>
    </div></div></section>

    <section class="showcase-section section-wrap"><div class="showcase-copy reveal"><p class="eyebrow">A page of your own</p><h2>Make every letter<br><em>feel like yours.</em></h2><p>Pick the paper. Find the words. Add the little things that make it unmistakably yours.</p><a class="button" href="create-letter.php">Start writing <span aria-hidden="true">↗</span></a></div>
        <div class="showcase-art reveal"><div class="tape tape-top"></div><article class="letter-sheet showcase-sheet"><div class="letter-topline"><span>FOR SOMEONE SPECIAL</span><span>01 / 01</span></div><h3>To the days<br>we'll talk about</h3><div class="showcase-content"><p>Dear friend,</p><p>Thank you for making ordinary days feel like stories I want to tell again. Here's to more long walks, second cups of tea, and not saying goodbye at the door.</p><span class="showcase-photo"><img src="https://images.unsplash.com/photo-1511988617509-a57c8a288659?auto=format&fit=crop&w=700&q=85" alt="Friends spending an afternoon together"><span>somewhere in summer</span></span><p class="showcase-sign">Yours, always <span>♡</span></p></div><span class="showcase-sticker">✿</span></article><span class="showcase-flower">✳</span></div></section>

    <section class="templates-section" id="templates"><div class="section-wrap"><div class="section-heading reveal"><div><p class="eyebrow">A lovely place to begin</p><h2>Find your first page.</h2></div><a class="text-link" href="create-letter.php">See all templates <span>↗</span></a></div><div class="template-grid">
        <a class="template-tile template-rose reveal" href="create-letter.php?template=rose"><span class="template-label">01 · THE KEEPSAKE</span><span class="template-title">A little<br>something</span><span class="template-decoration">✿</span><span class="template-open">Start with this ↗</span></a>
        <a class="template-tile template-sun reveal" href="create-letter.php?template=sunshine"><span class="template-label">02 · THE POSTCARD</span><span class="template-title">Wish you<br>were here</span><span class="template-decoration">☼</span><span class="template-open">Start with this ↗</span></a>
        <a class="template-tile template-ink reveal" href="create-letter.php?template=midnight"><span class="template-label">03 · THE LATE NOTE</span><span class="template-title">Things I<br>never said</span><span class="template-decoration">✳</span><span class="template-open">Start with this ↗</span></a>
    </div></div></section>

    <section class="library-teaser section-wrap"><div class="library-copy reveal"><p class="eyebrow">A home for your words</p><h2>Your letters deserve<br>a place to stay.</h2><p>Create an account and build your personal Letterly library. Save the little things, and return to your memories whenever you want.</p><a class="button button-light" href="<?= current_user() ? 'library.php' : 'register.php' ?>"><?= current_user() ? 'Visit my library' : 'Create your account' ?> <span>↗</span></a></div><div class="library-mock reveal"><div class="library-mock-top"><span>MY LETTERS</span><span>04 NOTES</span></div><div class="mock-letter-row"><span class="mock-thumb mock-thumb-one">dear<br>future me</span><div><strong>To My Future Self</strong><small>September 18, 2026 · 3 min read</small></div><span class="mock-arrow">↗</span></div><div class="mock-letter-row"><span class="mock-thumb mock-thumb-two">for<br>mom</span><div><strong>A Letter to Mom</strong><small>August 02, 2026 · 2 min read</small></div><span class="mock-arrow">↗</span></div><div class="mock-letter-row"><span class="mock-thumb mock-thumb-three">what I<br>meant</span><div><strong>Things I Never Said</strong><small>July 24, 2026 · 4 min read</small></div><span class="mock-arrow">↗</span></div><div class="mock-letter-row"><span class="mock-thumb mock-thumb-four">little<br>moments</span><div><strong>My Favorite Memories</strong><small>June 11, 2026 · 3 min read</small></div><span class="mock-arrow">↗</span></div></div></section>

    <section class="closing-note"><div class="section-wrap closing-inner"><span class="closing-flower">✿</span><p class="eyebrow">There's no perfect way to say it</p><h2>There is only your way.</h2><a class="button" href="create-letter.php">Write your first letter <span>↗</span></a></div></section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>