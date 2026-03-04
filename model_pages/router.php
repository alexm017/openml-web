<?php
declare(strict_types=1);

require_once __DIR__ . '/../assets/includes/model_pages_store.php';
require_once __DIR__ . '/../assets/includes/model_routes.php';

$season = strtolower(trim((string) ($_GET['season'] ?? '')));
$rawSlug = (string) ($_GET['slug'] ?? '');
$slug = alphabit_model_pages_slugify($rawSlug);

if (!alphabit_model_pages_is_valid_season($season) || $slug === '') {
    http_response_code(404);
    echo 'Page not found.';
    exit;
}

$forcedLang = strtolower(trim((string) ($_GET['__admin_lang'] ?? '')));
if ($forcedLang === 'en' || $forcedLang === 'ro') {
    $_COOKIE['site_lang'] = $forcedLang;
}

$pages = alphabit_model_pages_load_all();
$customPage = alphabit_model_pages_find($pages, $season, $slug, true);
$customPageSeason = $season;

if (!is_array($customPage)) {
    $requestedBuiltin = alphabit_model_builtin_find($season, $slug);
    if (is_array($requestedBuiltin)) {
        foreach (alphabit_model_pages_allowed_seasons() as $otherSeason) {
            if ($otherSeason === $season) {
                continue;
            }

            $otherCustom = alphabit_model_pages_find($pages, $otherSeason, $slug, true);
            if (!is_array($otherCustom)) {
                continue;
            }

            $otherBuiltin = alphabit_model_builtin_find($otherSeason, $slug);
            if (!is_array($otherBuiltin)) {
                continue;
            }

            $requestedFile = (string) ($requestedBuiltin['file'] ?? '');
            $otherFile = (string) ($otherBuiltin['file'] ?? '');
            if ($requestedFile === '' || $otherFile === '' || $requestedFile !== $otherFile) {
                continue;
            }

            $customPage = $otherCustom;
            $customPageSeason = $otherSeason;
            break;
        }
    }
}

if (is_array($customPage)) {
    $_GET['season'] = $season;
    $_GET['slug'] = $slug;
    if ($customPageSeason !== $season) {
        $_GET['_content_season'] = $customPageSeason;
    }
    require __DIR__ . '/render.php';
    exit;
}

$builtin = alphabit_model_builtin_find($season, $slug);
if (!is_array($builtin)) {
    http_response_code(404);
    echo 'Page not found.';
    exit;
}

$targetFile = dirname(__DIR__) . '/' . ltrim((string) ($builtin['file'] ?? ''), '/');
if (!is_file($targetFile) || !is_readable($targetFile)) {
    http_response_code(404);
    echo 'Page not found.';
    exit;
}

$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
if ($lang !== 'ro') {
    $lang = 'en';
}

$activeCustomPages = [];
$builtinSlugs = [];
foreach (alphabit_model_builtin_for_season($season) as $route) {
    $routeSlug = (string) ($route['slug'] ?? '');
    if ($routeSlug !== '') {
        $builtinSlugs[$routeSlug] = true;
    }
}

foreach ($pages as $entry) {
    if (!is_array($entry)) {
        continue;
    }

    $normalized = alphabit_model_pages_normalize_record($entry);
    if (($normalized['season'] ?? '') !== $season) {
        continue;
    }
    if (empty($normalized['is_active'])) {
        continue;
    }
    if (isset($builtinSlugs[(string) ($normalized['slug'] ?? '')])) {
        continue;
    }

    $title = (string) ($lang === 'ro' && trim((string) ($normalized['title_ro'] ?? '')) !== ''
        ? $normalized['title_ro']
        : $normalized['title_en']);

    if ($title === '') {
        $title = (string) $normalized['slug'];
    }

    $activeCustomPages[] = [
        'title' => $title,
        'url' => '/model/' . $season . '/' . $normalized['slug'],
    ];
}

usort($activeCustomPages, static function (array $a, array $b): int {
    return strcmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? ''));
});

ob_start();
require $targetFile;
$html = (string) ob_get_clean();

if (count($activeCustomPages) > 0 && stripos($html, 'class="docs-container"') !== false) {
    $sectionTitle = ($lang === 'ro') ? 'Pagini Custom' : 'Custom Pages';
    $listJson = json_encode($activeCustomPages, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    if (!is_string($listJson)) {
        $listJson = '[]';
    }
    $titleJson = json_encode($sectionTitle, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    if (!is_string($titleJson)) {
        $titleJson = json_encode('Custom Pages');
    }

    $injectScript = '<script>(function(){'
        . 'var items=' . $listJson . ';'
        . 'if(!Array.isArray(items)||items.length===0){return;}'
        . 'var container=document.querySelector(".docs-container");'
        . 'if(!container){return;}'
        . 'if(container.querySelector("[data-custom-pages=\'1\']")){return;}'
        . 'var line=document.createElement("div");line.className="docsLine";container.appendChild(line);'
        . 'var setup=document.createElement("div");setup.className="setup";setup.setAttribute("data-custom-pages","1");setup.textContent=' . $titleJson . ';container.appendChild(setup);'
        . 'for(var i=0;i<items.length;i++){var item=items[i]||{};if(!item.url){continue;}'
        . 'var wrap=document.createElement("div");wrap.className="sub-section";'
        . 'var link=document.createElement("a");link.href=item.url;link.textContent=item.title||item.url;'
        . 'wrap.appendChild(link);container.appendChild(wrap);}'
        . '})();</script>';

    if (stripos($html, '</body>') !== false) {
        $html = str_ireplace('</body>', $injectScript . '</body>', $html);
    } else {
        $html .= $injectScript;
    }
}

echo $html;
