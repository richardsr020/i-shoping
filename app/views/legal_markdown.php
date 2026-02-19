<?php

if (!function_exists('loadLegalMarkdownContent')) {
    function loadLegalMarkdownContent(array $candidatePaths): string {
        foreach ($candidatePaths as $path) {
            $normalizedPath = trim((string)$path);
            if ($normalizedPath === '' || !is_file($normalizedPath) || !is_readable($normalizedPath)) {
                continue;
            }

            $content = file_get_contents($normalizedPath);
            if (is_string($content) && trim($content) !== '') {
                return $content;
            }
        }

        return '';
    }
}

if (!function_exists('renderLegalMarkdownInline')) {
    function renderLegalMarkdownInline(string $text): string {
        $chunks = preg_split('/(\[[^\]]+\]\([^)]+\)|\*\*[^*]+\*\*)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (!is_array($chunks)) {
            return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        }

        $html = '';
        foreach ($chunks as $chunk) {
            if ($chunk === '') {
                continue;
            }

            if (preg_match('/^\[([^\]]+)\]\(([^)]+)\)$/', $chunk, $m)) {
                $label = htmlspecialchars(trim((string)$m[1]), ENT_QUOTES, 'UTF-8');
                $href = htmlspecialchars(trim((string)$m[2]), ENT_QUOTES, 'UTF-8');
                $isExternal = strpos($href, 'http://') === 0 || strpos($href, 'https://') === 0 || strpos($href, 'mailto:') === 0;
                $attrs = $isExternal ? ' target="_blank" rel="noopener noreferrer"' : '';
                $html .= '<a href="' . $href . '"' . $attrs . '>' . $label . '</a>';
                continue;
            }

            if (preg_match('/^\*\*([^*]+)\*\*$/', $chunk, $m)) {
                $html .= '<strong>' . htmlspecialchars((string)$m[1], ENT_QUOTES, 'UTF-8') . '</strong>';
                continue;
            }

            $html .= htmlspecialchars($chunk, ENT_QUOTES, 'UTF-8');
        }

        return $html;
    }
}

if (!function_exists('renderLegalMarkdown')) {
    function renderLegalMarkdown(string $markdown): string {
        $content = str_replace(["\r\n", "\r"], "\n", $markdown);
        $lines = explode("\n", $content);
        $html = '';
        $inList = false;

        foreach ($lines as $lineRaw) {
            $line = trim((string)$lineRaw);

            if ($line === '') {
                if ($inList) {
                    $html .= '</ul>';
                    $inList = false;
                }
                continue;
            }

            if (preg_match('/^---+$/', $line)) {
                if ($inList) {
                    $html .= '</ul>';
                    $inList = false;
                }
                $html .= '<hr class="legal-hr">';
                continue;
            }

            if (preg_match('/^###\s+(.+)$/', $line, $m)) {
                if ($inList) {
                    $html .= '</ul>';
                    $inList = false;
                }
                $html .= '<h3>' . renderLegalMarkdownInline((string)$m[1]) . '</h3>';
                continue;
            }

            if (preg_match('/^##\s+(.+)$/', $line, $m)) {
                if ($inList) {
                    $html .= '</ul>';
                    $inList = false;
                }
                $html .= '<h2>' . renderLegalMarkdownInline((string)$m[1]) . '</h2>';
                continue;
            }

            if (preg_match('/^#\s+(.+)$/', $line, $m)) {
                if ($inList) {
                    $html .= '</ul>';
                    $inList = false;
                }
                $html .= '<h1>' . renderLegalMarkdownInline((string)$m[1]) . '</h1>';
                continue;
            }

            if (preg_match('/^\*\s+(.+)$/', $line, $m)) {
                if (!$inList) {
                    $html .= '<ul class="legal-list">';
                    $inList = true;
                }
                $html .= '<li>' . renderLegalMarkdownInline((string)$m[1]) . '</li>';
                continue;
            }

            if ($inList) {
                $html .= '</ul>';
                $inList = false;
            }

            $html .= '<p>' . renderLegalMarkdownInline($line) . '</p>';
        }

        if ($inList) {
            $html .= '</ul>';
        }

        return $html;
    }
}
