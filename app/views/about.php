<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/legal_markdown.php';

$markdown = loadLegalMarkdownContent([
    BASE_PATH . '/aboutus.md',
]);

if ($markdown === '') {
    $markdown = "# A propos\n\nContenu indisponible pour le moment.";
}

$contentHtml = renderLegalMarkdown($markdown);
?>

<main class="main-content container" style="padding-top: var(--spacing-lg);">
    <div style="max-width: 900px; margin: 0 auto; background: var(--color-bg); border: 1px solid rgba(0,0,0,0.08); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); padding: clamp(18px, 2.5vw, 32px);">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom: var(--spacing-md);">
            <h1 style="margin: 0; font-size: clamp(26px, 4vw, 34px);">A propos</h1>
            <a href="<?php echo url('contact'); ?>" class="btn btn-ghost btn-sm">Contact</a>
        </div>
        <div class="legal-content"><?php echo $contentHtml; ?></div>
    </div>
</main>

<style>
    .legal-content h1,
    .legal-content h2,
    .legal-content h3 {
        margin: 18px 0 10px 0;
        line-height: 1.3;
    }

    .legal-content h1 {
        font-size: 30px;
    }

    .legal-content h2 {
        font-size: 24px;
    }

    .legal-content h3 {
        font-size: 18px;
    }

    .legal-content p {
        margin: 0 0 12px 0;
        line-height: 1.8;
    }

    .legal-content .legal-list {
        list-style: disc;
        padding-left: 20px;
        margin: 0 0 14px 0;
    }

    .legal-content .legal-list li {
        margin: 0 0 8px 0;
    }

    .legal-content a {
        color: var(--color-primary);
        text-decoration: underline;
    }

    .legal-content .legal-hr {
        border: none;
        border-top: 1px solid rgba(0,0,0,0.1);
        margin: 16px 0;
    }
</style>
