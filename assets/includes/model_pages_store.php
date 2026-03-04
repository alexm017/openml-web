<?php
declare(strict_types=1);

function alphabit_model_pages_storage_path(): string
{
    return dirname(__DIR__, 2) . '/data/model_pages/pages.json';
}

function alphabit_model_pages_allowed_seasons(): array
{
    return ['intothedeep', 'decode'];
}

function alphabit_model_pages_is_valid_season(string $season): bool
{
    return in_array($season, alphabit_model_pages_allowed_seasons(), true);
}

function alphabit_model_pages_slugify(string $value): string
{
    $slug = strtolower(trim($value));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim((string) $slug, '-');
    if ($slug === '') {
        return '';
    }

    return substr($slug, 0, 64);
}

function alphabit_model_pages_generate_id(): string
{
    try {
        return bin2hex(random_bytes(8));
    } catch (Throwable $exception) {
        return str_replace('.', '', uniqid('', true));
    }
}

function alphabit_model_pages_ensure_storage(): void
{
    $path = alphabit_model_pages_storage_path();
    $dir = dirname($path);
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    if (is_dir($dir) && !is_writable($dir)) {
        @chmod($dir, 0777);
    }

    if (!is_file($path)) {
        @file_put_contents($path, "[]\n", LOCK_EX);
    }
    if (is_file($path) && !is_writable($path)) {
        @chmod($path, 0666);
    }
}

function alphabit_model_pages_normalize_record(array $raw): array
{
    $season = strtolower(trim((string) ($raw['season'] ?? 'intothedeep')));
    if (!alphabit_model_pages_is_valid_season($season)) {
        $season = 'intothedeep';
    }

    $slug = alphabit_model_pages_slugify((string) ($raw['slug'] ?? ''));
    if ($slug === '') {
        $slug = 'page-' . date('YmdHis');
    }

    $id = trim((string) ($raw['id'] ?? ''));
    if ($id === '') {
        $id = alphabit_model_pages_generate_id();
    }

    $createdAt = trim((string) ($raw['created_at'] ?? ''));
    $updatedAt = trim((string) ($raw['updated_at'] ?? ''));
    if ($createdAt === '') {
        $createdAt = date('c');
    }
    if ($updatedAt === '') {
        $updatedAt = $createdAt;
    }

    return [
        'id' => $id,
        'season' => $season,
        'slug' => $slug,
        'title_en' => trim((string) ($raw['title_en'] ?? 'Untitled Page')),
        'title_ro' => trim((string) ($raw['title_ro'] ?? '')),
        'content_en' => trim((string) ($raw['content_en'] ?? '')),
        'content_ro' => trim((string) ($raw['content_ro'] ?? '')),
        'is_active' => !empty($raw['is_active']),
        'created_at' => $createdAt,
        'updated_at' => $updatedAt,
    ];
}

function alphabit_model_pages_load_all(): array
{
    alphabit_model_pages_ensure_storage();

    $path = alphabit_model_pages_storage_path();
    $raw = @file_get_contents($path);
    if (!is_string($raw) || $raw === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return [];
    }

    $pages = [];
    $seenIds = [];
    foreach ($decoded as $entry) {
        if (!is_array($entry)) {
            continue;
        }

        $normalized = alphabit_model_pages_normalize_record($entry);
        if (isset($seenIds[$normalized['id']])) {
            continue;
        }

        $seenIds[$normalized['id']] = true;
        $pages[] = $normalized;
    }

    return $pages;
}

function alphabit_model_pages_save_all(array $pages): bool
{
    alphabit_model_pages_ensure_storage();

    $path = alphabit_model_pages_storage_path();
    if (!is_file($path) || !is_writable($path)) {
        return false;
    }

    $normalizedPages = [];
    foreach ($pages as $page) {
        if (!is_array($page)) {
            continue;
        }
        $normalizedPages[] = alphabit_model_pages_normalize_record($page);
    }

    $json = json_encode($normalizedPages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    if (!is_string($json)) {
        return false;
    }

    return @file_put_contents($path, $json . "\n", LOCK_EX) !== false;
}

function alphabit_model_pages_find(array $pages, string $season, string $slug, bool $onlyActive = false): ?array
{
    $season = strtolower(trim($season));
    $slug = alphabit_model_pages_slugify($slug);

    foreach ($pages as $page) {
        if (!is_array($page)) {
            continue;
        }
        if (($page['season'] ?? '') !== $season) {
            continue;
        }
        if (($page['slug'] ?? '') !== $slug) {
            continue;
        }
        if ($onlyActive && empty($page['is_active'])) {
            continue;
        }

        return alphabit_model_pages_normalize_record($page);
    }

    return null;
}

function alphabit_model_pages_find_by_id(array $pages, string $id): ?array
{
    $id = trim($id);
    if ($id === '') {
        return null;
    }

    foreach ($pages as $page) {
        if (!is_array($page)) {
            continue;
        }
        if (($page['id'] ?? '') === $id) {
            return alphabit_model_pages_normalize_record($page);
        }
    }

    return null;
}

function alphabit_model_pages_upsert(array &$pages, array $record, ?string $targetId = null): array
{
    $normalized = alphabit_model_pages_normalize_record($record);
    $targetId = ($targetId === null) ? $normalized['id'] : trim($targetId);
    if ($targetId === '') {
        $targetId = $normalized['id'];
    }

    $replaced = false;
    foreach ($pages as $index => $page) {
        if (!is_array($page)) {
            continue;
        }
        if (($page['id'] ?? '') === $targetId) {
            $existing = alphabit_model_pages_normalize_record($page);
            $normalized['id'] = $targetId;
            $normalized['created_at'] = $existing['created_at'];
            $normalized['updated_at'] = date('c');
            $pages[$index] = $normalized;
            $replaced = true;
            break;
        }
    }

    if (!$replaced) {
        $normalized['id'] = ($normalized['id'] !== '') ? $normalized['id'] : alphabit_model_pages_generate_id();
        $normalized['created_at'] = date('c');
        $normalized['updated_at'] = $normalized['created_at'];
        $pages[] = $normalized;
    }

    return $normalized;
}

function alphabit_model_pages_delete(array &$pages, string $targetId): bool
{
    $targetId = trim($targetId);
    if ($targetId === '') {
        return false;
    }

    foreach ($pages as $index => $page) {
        if (!is_array($page)) {
            continue;
        }
        if (($page['id'] ?? '') === $targetId) {
            unset($pages[$index]);
            $pages = array_values($pages);
            return true;
        }
    }

    return false;
}

function alphabit_model_pages_is_safe_url(string $url): bool
{
    $url = trim($url);
    if ($url === '') {
        return false;
    }

    if (strpos($url, '/') === 0 || strpos($url, '#') === 0) {
        return true;
    }

    $parsed = parse_url($url);
    if (!is_array($parsed)) {
        return false;
    }

    $scheme = strtolower((string) ($parsed['scheme'] ?? ''));
    return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
}

function alphabit_model_pages_sanitize_class_attr(string $classAttr): string
{
    $classAttr = trim((string) preg_replace('/\s+/', ' ', $classAttr));
    if ($classAttr === '') {
        return '';
    }

    $tokens = preg_split('/\s+/', $classAttr);
    if (!is_array($tokens)) {
        return '';
    }

    $safeTokens = [];
    foreach ($tokens as $token) {
        $token = trim((string) $token);
        if ($token === '') {
            continue;
        }
        if (!preg_match('/^[a-zA-Z0-9_-]{1,64}$/', $token)) {
            continue;
        }
        $safeTokens[$token] = true;
    }

    if (count($safeTokens) === 0) {
        return '';
    }

    return implode(' ', array_slice(array_keys($safeTokens), 0, 20));
}

function alphabit_model_pages_sanitize_style_attr(string $styleAttr): string
{
    $styleAttr = trim($styleAttr);
    if ($styleAttr === '') {
        return '';
    }

    if (preg_match('/expression|javascript:|vbscript:|data:|url\s*\(|@import|behavior\s*:|-moz-binding/i', $styleAttr)) {
        return '';
    }

    $allowedProps = [
        'color', 'background-color', 'opacity',
        'width', 'height', 'max-width', 'max-height', 'min-width', 'min-height',
        'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
        'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
        'display', 'position', 'top', 'right', 'bottom', 'left',
        'border', 'border-width', 'border-style', 'border-color', 'border-radius',
        'font-size', 'font-weight', 'font-style', 'line-height', 'letter-spacing',
        'text-align', 'text-decoration', 'text-transform', 'white-space',
        'object-fit', 'float', 'clear',
    ];

    $safeDecl = [];
    foreach (explode(';', $styleAttr) as $decl) {
        $decl = trim($decl);
        if ($decl === '' || strpos($decl, ':') === false) {
            continue;
        }

        [$prop, $value] = explode(':', $decl, 2);
        $prop = strtolower(trim((string) $prop));
        $value = trim((string) $value);
        if ($prop === '' || $value === '') {
            continue;
        }
        if (!preg_match('/^[a-z-]{1,40}$/', $prop)) {
            continue;
        }
        if (!in_array($prop, $allowedProps, true)) {
            continue;
        }
        if (preg_match('/expression|javascript:|vbscript:|data:|url\s*\(|@import|behavior\s*:|-moz-binding/i', $value)) {
            continue;
        }
        if (!preg_match('/^[a-zA-Z0-9\s#%(),.\/_+\-:"\']{1,200}$/', $value)) {
            continue;
        }

        $safeDecl[] = $prop . ': ' . $value;
    }

    return implode('; ', $safeDecl);
}

function alphabit_model_pages_sanitize_html(string $html): string
{
    $html = trim($html);
    if ($html === '') {
        return '';
    }

    $html = (string) preg_replace('/<!--[\s\S]*?-->/', '', $html);
    $html = (string) preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $html);
    $html = (string) preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $html);

    $allowedTags = [
        'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'strong', 'b', 'em', 'i', 'u',
        'ul', 'ol', 'li',
        'a', 'img',
        'blockquote', 'code', 'pre',
        'br', 'hr',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'div', 'span',
    ];

    if (!class_exists('DOMDocument')) {
        $stripped = strip_tags($html, '<' . implode('><', $allowedTags) . '>');
        $sanitized = preg_replace_callback('/<\s*(\/?)\s*([a-z0-9]+)([^>]*)>/i', static function (array $match) use ($allowedTags): string {
            $isClosing = trim((string) $match[1]) === '/';
            $tag = strtolower(trim((string) $match[2]));
            $attrRaw = (string) $match[3];

            if (!in_array($tag, $allowedTags, true)) {
                return '';
            }

            if ($isClosing) {
                return '</' . $tag . '>';
            }

            if (in_array($tag, ['br', 'hr'], true)) {
                return '<' . $tag . '>';
            }

            $attributes = [];
            if (preg_match_all('/([a-zA-Z0-9:_-]+)\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s"\'=<>`]+))/', $attrRaw, $attrMatches, PREG_SET_ORDER)) {
                foreach ($attrMatches as $attrMatch) {
                    $name = strtolower((string) $attrMatch[1]);
                    $value = (string) ($attrMatch[3] !== '' ? $attrMatch[3] : ($attrMatch[4] !== '' ? $attrMatch[4] : $attrMatch[5]));
                    $attributes[$name] = $value;
                }
            }

            $classAttr = alphabit_model_pages_sanitize_class_attr((string) ($attributes['class'] ?? ''));
            $styleAttr = alphabit_model_pages_sanitize_style_attr((string) ($attributes['style'] ?? ''));

            if ($tag === 'a') {
                $href = trim((string) ($attributes['href'] ?? ''));
                $title = trim((string) ($attributes['title'] ?? ''));
                $target = strtolower(trim((string) ($attributes['target'] ?? '')));

                $parts = ['<a'];
                if ($href !== '' && alphabit_model_pages_is_safe_url($href)) {
                    $parts[] = 'href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '"';
                }
                if ($title !== '') {
                    $parts[] = 'title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '"';
                }
                if ($target === '_blank') {
                    $parts[] = 'target="_blank"';
                    $parts[] = 'rel="noopener noreferrer"';
                }
                if ($classAttr !== '') {
                    $parts[] = 'class="' . htmlspecialchars($classAttr, ENT_QUOTES, 'UTF-8') . '"';
                }
                if ($styleAttr !== '') {
                    $parts[] = 'style="' . htmlspecialchars($styleAttr, ENT_QUOTES, 'UTF-8') . '"';
                }
                $parts[] = '>';
                return implode(' ', $parts);
            }

            if ($tag === 'img') {
                $src = trim((string) ($attributes['src'] ?? ''));
                if ($src === '' || !alphabit_model_pages_is_safe_url($src)) {
                    return '';
                }

                $parts = ['<img', 'src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '"'];
                $alt = trim((string) ($attributes['alt'] ?? ''));
                $title = trim((string) ($attributes['title'] ?? ''));
                $width = trim((string) ($attributes['width'] ?? ''));
                $height = trim((string) ($attributes['height'] ?? ''));

                if ($alt !== '') {
                    $parts[] = 'alt="' . htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') . '"';
                }
                if ($title !== '') {
                    $parts[] = 'title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '"';
                }
                if (preg_match('/^\d{1,4}$/', $width)) {
                    $parts[] = 'width="' . $width . '"';
                }
                if (preg_match('/^\d{1,4}$/', $height)) {
                    $parts[] = 'height="' . $height . '"';
                }
                if ($classAttr !== '') {
                    $parts[] = 'class="' . htmlspecialchars($classAttr, ENT_QUOTES, 'UTF-8') . '"';
                }
                if ($styleAttr !== '') {
                    $parts[] = 'style="' . htmlspecialchars($styleAttr, ENT_QUOTES, 'UTF-8') . '"';
                }
                $parts[] = 'loading="lazy"';
                $parts[] = '>';
                return implode(' ', $parts);
            }

            $parts = ['<' . $tag];
            if ($classAttr !== '') {
                $parts[] = 'class="' . htmlspecialchars($classAttr, ENT_QUOTES, 'UTF-8') . '"';
            }
            if ($styleAttr !== '') {
                $parts[] = 'style="' . htmlspecialchars($styleAttr, ENT_QUOTES, 'UTF-8') . '"';
            }
            $parts[] = '>';
            return implode(' ', $parts);
        }, $stripped);

        return trim((string) $sanitized);
    }

    $allowedAttrs = [
        'a' => ['href', 'title', 'target', 'rel', 'class', 'style'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'loading', 'class', 'style'],
        'th' => ['colspan', 'rowspan'],
        'td' => ['colspan', 'rowspan'],
    ];
    $globalAllowedAttrs = ['class', 'style'];

    $wrapperId = 'alphabit-html-root';
    $dom = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $dom->loadHTML(
        '<!DOCTYPE html><html><body><div id="' . $wrapperId . '">' . $html . '</div></body></html>',
        LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED
    );
    libxml_clear_errors();

    $root = $dom->getElementById($wrapperId);
    if (!$root instanceof DOMElement) {
        return '';
    }

    $sanitizeNode = function (DOMNode $node) use (&$sanitizeNode, $allowedTags, $allowedAttrs, $globalAllowedAttrs): void {
        $children = [];
        foreach ($node->childNodes as $childNode) {
            $children[] = $childNode;
        }

        foreach ($children as $child) {
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);
                if (!in_array($tag, $allowedTags, true)) {
                    while ($child->firstChild instanceof DOMNode) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }

                $attrs = [];
                foreach ($child->attributes as $attribute) {
                    $attrs[] = $attribute;
                }

                foreach ($attrs as $attribute) {
                    $name = strtolower($attribute->name);
                    $value = trim((string) $attribute->value);

                    if (strpos($name, 'on') === 0) {
                        $child->removeAttributeNode($attribute);
                        continue;
                    }

                    $allowed = in_array($name, $allowedAttrs[$tag] ?? [], true) || in_array($name, $globalAllowedAttrs, true);
                    if (!$allowed) {
                        $child->removeAttributeNode($attribute);
                        continue;
                    }

                    if ($name === 'class') {
                        $safeClass = alphabit_model_pages_sanitize_class_attr($value);
                        if ($safeClass === '') {
                            $child->removeAttributeNode($attribute);
                            continue;
                        }
                        $child->setAttribute('class', $safeClass);
                        continue;
                    }

                    if ($name === 'style') {
                        $safeStyle = alphabit_model_pages_sanitize_style_attr($value);
                        if ($safeStyle === '') {
                            $child->removeAttributeNode($attribute);
                            continue;
                        }
                        $child->setAttribute('style', $safeStyle);
                        continue;
                    }

                    if (($tag === 'a' && $name === 'href') || ($tag === 'img' && $name === 'src')) {
                        if (!alphabit_model_pages_is_safe_url($value)) {
                            $child->removeAttributeNode($attribute);
                            continue;
                        }
                    }
                }

                if ($tag === 'a') {
                    $href = trim($child->getAttribute('href'));
                    if ($href === '') {
                        $child->removeAttribute('href');
                    }

                    $target = trim($child->getAttribute('target'));
                    if ($target !== '' && strtolower($target) !== '_blank') {
                        $child->removeAttribute('target');
                    }
                    if (strtolower($target) === '_blank') {
                        $child->setAttribute('rel', 'noopener noreferrer');
                    }
                }

                if ($tag === 'img') {
                    $src = trim($child->getAttribute('src'));
                    if ($src === '') {
                        $node->removeChild($child);
                        continue;
                    }
                    if (!$child->hasAttribute('loading')) {
                        $child->setAttribute('loading', 'lazy');
                    }
                }
            }

            $sanitizeNode($child);
        }
    };

    $sanitizeNode($root);

    $sanitizedHtml = '';
    foreach ($root->childNodes as $childNode) {
        $sanitizedHtml .= $dom->saveHTML($childNode);
    }

    return trim($sanitizedHtml);
}
