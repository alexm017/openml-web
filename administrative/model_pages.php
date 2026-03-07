<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../assets/includes/model_pages_store.php';
require_once __DIR__ . '/../assets/includes/model_routes.php';
require_once __DIR__ . '/../assets/includes/admin_access.php';

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== 'userLoggedIn') {
    header('Location: /login');
    exit;
}

if (!alphabit_session_is_admin()) {
    http_response_code(403);
    echo 'Access denied.';
    exit;
}


$season_cookie = isset($_COOKIE['season_choice']) ? $_COOKIE['season_choice'] : 'Decode';
$season_path = ($season_cookie === 'Decode') ? 'decode' : 'intothedeep';
$team_name = isset($_SESSION['teamname']) ? (string) $_SESSION['teamname'] : '';

if (!isset($_SESSION['model_pages_csrf']) || !is_string($_SESSION['model_pages_csrf']) || $_SESSION['model_pages_csrf'] === '') {
    try {
        $_SESSION['model_pages_csrf'] = bin2hex(random_bytes(16));
    } catch (Throwable $exception) {
        $_SESSION['model_pages_csrf'] = hash('sha256', uniqid('model-pages', true));
    }
}
$csrfToken = $_SESSION['model_pages_csrf'];

$flashType = '';
$flashMessage = '';
$uploadedImagePath = '';

$pages = alphabit_model_pages_load_all();

$formValues = [
    'id' => '',
    'season' => $season_path,
    'slug' => '',
    'title_en' => '',
    'title_ro' => '',
    'content_en' => '',
    'content_ro' => '',
    'is_active' => true,
];

function admin_model_pages_redirect(string $query = ''): void
{
    $url = '/admin/model-pages';
    if ($query !== '') {
        $url .= '?' . ltrim($query, '?');
    }
    header('Location: ' . $url);
    exit;
}

function admin_model_pages_set_form_from_page(array $page): array
{
    return [
        'id' => (string) ($page['id'] ?? ''),
        'season' => (string) ($page['season'] ?? 'intothedeep'),
        'slug' => (string) ($page['slug'] ?? ''),
        'title_en' => (string) ($page['title_en'] ?? ''),
        'title_ro' => (string) ($page['title_ro'] ?? ''),
        'content_en' => (string) ($page['content_en'] ?? ''),
        'content_ro' => (string) ($page['content_ro'] ?? ''),
        'is_active' => !empty($page['is_active']),
    ];
}

function admin_model_pages_set_form_from_builtin(array $builtinRoute, ?array $overridePage): array
{
    $season = strtolower(trim((string) ($builtinRoute['season'] ?? 'intothedeep')));
    if (!alphabit_model_pages_is_valid_season($season)) {
        $season = 'intothedeep';
    }
    $slug = alphabit_model_pages_slugify((string) ($builtinRoute['slug'] ?? ''));
    $titleEn = trim((string) ($builtinRoute['title_en'] ?? ''));
    $titleRo = trim((string) ($builtinRoute['title_ro'] ?? ''));

    $form = [
        'id' => '',
        'season' => $season,
        'slug' => $slug,
        'title_en' => $titleEn !== '' ? $titleEn : $slug,
        'title_ro' => $titleRo,
        'content_en' => '',
        'content_ro' => '',
        'is_active' => true,
    ];

    if (is_array($overridePage)) {
        $form = admin_model_pages_set_form_from_page($overridePage);
        if (trim((string) $form['title_en']) === '') {
            $form['title_en'] = $titleEn !== '' ? $titleEn : $slug;
        }
        if (trim((string) $form['title_ro']) === '') {
            $form['title_ro'] = $titleRo;
        }
    }

    return $form;
}

function admin_model_pages_build_list_items(array $pages): array
{
    $customByKey = [];
    foreach ($pages as $page) {
        if (!is_array($page)) {
            continue;
        }

        $normalized = alphabit_model_pages_normalize_record($page);
        $key = alphabit_model_builtin_key((string) $normalized['season'], (string) $normalized['slug']);
        $customByKey[$key] = $normalized;
    }

    $items = [];
    foreach (alphabit_model_builtin_map() as $builtinRoute) {
        $season = (string) ($builtinRoute['season'] ?? 'intothedeep');
        $slug = (string) ($builtinRoute['slug'] ?? '');
        if ($slug === '') {
            continue;
        }

        $key = alphabit_model_builtin_key($season, $slug);
        $override = $customByKey[$key] ?? null;
        if (is_array($override)) {
            unset($customByKey[$key]);
        }

        $title = trim((string) ($override['title_en'] ?? ''));
        if ($title === '') {
            $title = trim((string) ($builtinRoute['title_en'] ?? ''));
        }
        if ($title === '') {
            $title = $slug;
        }

        $items[] = [
            'key' => $key,
            'season' => $season,
            'slug' => $slug,
            'title' => $title,
            'path' => '/model/' . $season . '/' . $slug,
            'type' => 'builtin',
            'has_override' => is_array($override),
            'is_active' => is_array($override) ? !empty($override['is_active']) : true,
            'edit_link' => '/admin/model-pages?season=' . rawurlencode($season) . '&slug=' . rawurlencode($slug),
            'updated_at' => is_array($override) ? (string) ($override['updated_at'] ?? '') : '',
        ];
    }

    foreach ($customByKey as $key => $customPage) {
        $season = (string) ($customPage['season'] ?? 'intothedeep');
        $slug = (string) ($customPage['slug'] ?? '');
        if ($slug === '' || alphabit_model_is_builtin_slug($season, $slug)) {
            continue;
        }

        $title = trim((string) ($customPage['title_en'] ?? ''));
        if ($title === '') {
            $title = $slug;
        }

        $items[] = [
            'key' => $key,
            'season' => $season,
            'slug' => $slug,
            'title' => $title,
            'path' => '/model/' . $season . '/' . $slug,
            'type' => 'custom',
            'has_override' => true,
            'is_active' => !empty($customPage['is_active']),
            'edit_link' => '/admin/model-pages?season=' . rawurlencode($season) . '&slug=' . rawurlencode($slug),
            'updated_at' => (string) ($customPage['updated_at'] ?? ''),
        ];
    }

    usort($items, static function (array $a, array $b): int {
        $seasonA = (string) ($a['season'] ?? '');
        $seasonB = (string) ($b['season'] ?? '');
        if ($seasonA !== $seasonB) {
            return strcmp($seasonA, $seasonB);
        }

        $typeA = ((string) ($a['type'] ?? 'custom') === 'builtin') ? 0 : 1;
        $typeB = ((string) ($b['type'] ?? 'custom') === 'builtin') ? 0 : 1;
        if ($typeA !== $typeB) {
            return ($typeA < $typeB) ? -1 : 1;
        }

        $titleA = strtolower((string) ($a['title'] ?? ''));
        $titleB = strtolower((string) ($b['title'] ?? ''));
        if ($titleA !== $titleB) {
            return strcmp($titleA, $titleB);
        }

        return strcmp((string) ($a['slug'] ?? ''), (string) ($b['slug'] ?? ''));
    });

    return $items;
}

function admin_model_pages_sort_pages(array &$pages): void
{
    usort($pages, static function (array $a, array $b): int {
        $seasonA = (string) ($a['season'] ?? '');
        $seasonB = (string) ($b['season'] ?? '');
        if ($seasonA !== $seasonB) {
            return strcmp($seasonA, $seasonB);
        }

        $updatedA = strtotime((string) ($a['updated_at'] ?? '1970-01-01'));
        $updatedB = strtotime((string) ($b['updated_at'] ?? '1970-01-01'));
        if ($updatedA !== $updatedB) {
            return ($updatedA > $updatedB) ? -1 : 1;
        }

        return strcmp((string) ($a['slug'] ?? ''), (string) ($b['slug'] ?? ''));
    });
}

function admin_model_pages_delete_by_route(array &$pages, string $season, string $slug): bool
{
    $season = strtolower(trim($season));
    $slug = alphabit_model_pages_slugify($slug);
    $removed = false;
    $updated = [];

    foreach ($pages as $page) {
        if (!is_array($page)) {
            continue;
        }
        $normalized = alphabit_model_pages_normalize_record($page);
        if (($normalized['season'] ?? '') === $season && ($normalized['slug'] ?? '') === $slug) {
            $removed = true;
            continue;
        }
        $updated[] = $normalized;
    }

    if ($removed) {
        $pages = $updated;
    }

    return $removed;
}

function admin_model_pages_format_content_for_php_branch(string $html, int $indentLevel = 5): string
{
    $html = trim(str_replace(['<?', '?>'], ['&lt;?', '?&gt;'], $html));
    if ($html === '') {
        return str_repeat("\t", $indentLevel) . '<div class="stext"></div>';
    }

    $lines = preg_split('/\R/', $html);
    if (!is_array($lines) || count($lines) === 0) {
        return str_repeat("\t", $indentLevel) . $html;
    }

    $indent = str_repeat("\t", $indentLevel);
    $out = [];
    foreach ($lines as $line) {
        $out[] = $indent . rtrim((string) $line);
    }

    return implode("\n", $out);
}

function admin_model_pages_extract_footer_blocks(string $html): string
{
    $html = trim($html);
    if ($html === '') {
        return '';
    }

    $pattern = '/((?:<div\s+class\s*=\s*["\']endLine["\'][^>]*>\s*<\/div>\s*)?(?:<div\s+class\s*=\s*["\']endD["\'][^>]*>[\s\S]*?<\/div>\s*)(?:<div\s+class\s*=\s*["\']end["\'][^>]*>\s*<\/div>\s*)?)$/i';
    if (!preg_match($pattern, $html, $matches)) {
        return '';
    }

    return trim((string) ($matches[1] ?? ''));
}

function admin_model_pages_preserve_footer_blocks(string $existingHtml, string $incomingHtml): string
{
    $incomingHtml = trim($incomingHtml);
    $footer = admin_model_pages_extract_footer_blocks($existingHtml);
    if ($footer === '') {
        return $incomingHtml;
    }

    $lineBlock = '';
    if (preg_match('/<div\s+class\s*=\s*["\']endLine["\'][^>]*>\s*<\/div>/i', $footer, $lineMatch)) {
        $lineBlock = trim((string) ($lineMatch[0] ?? ''));
    }

    $endBlock = '';
    if (preg_match('/<div\s+class\s*=\s*["\']end["\'][^>]*>\s*<\/div>/i', $footer, $endMatch)) {
        $endBlock = trim((string) ($endMatch[0] ?? ''));
    }

    if ($incomingHtml !== '' && preg_match('/class\s*=\s*["\']endD["\']/i', $incomingHtml)) {
        $hasLine = preg_match('/class\s*=\s*["\']endLine["\']/i', $incomingHtml) === 1;
        $hasEnd = preg_match('/class\s*=\s*["\']end["\']/i', $incomingHtml) === 1;

        if (!$hasLine && $lineBlock !== '') {
            $withLine = preg_replace(
                '/(<div\s+class\s*=\s*["\']endD["\'][^>]*>[\s\S]*?<\/div>)/i',
                $lineBlock . "\n" . '$1',
                $incomingHtml,
                1
            );
            if (is_string($withLine) && $withLine !== '') {
                $incomingHtml = $withLine;
            }
        }

        if (!$hasEnd && $endBlock !== '') {
            $incomingHtml = rtrim($incomingHtml) . "\n" . $endBlock;
        }

        return trim($incomingHtml);
    }

    if ($incomingHtml === '') {
        return $footer;
    }

    return rtrim($incomingHtml) . "\n" . $footer;
}

function admin_model_pages_update_builtin_source_file(string $relativeFile, string $contentRo, string $contentEn): bool
{
    $relativeFile = ltrim(trim($relativeFile), '/');
    if ($relativeFile === '') {
        return false;
    }

    $absoluteFile = dirname(__DIR__) . '/' . $relativeFile;
    if (!is_file($absoluteFile) || !is_readable($absoluteFile) || !is_writable($absoluteFile)) {
        return false;
    }

    $raw = @file_get_contents($absoluteFile);
    if (!is_string($raw) || $raw === '') {
        return false;
    }

    $pattern = '/(<div class="text-container">\s*<\?php[\s\S]*?if\s*\(\$lang\s*==\s*[\'"]ro[\'"]\)\s*:\s*\?>)([\s\S]*?)(<\?php\s*else:\s*\?>)([\s\S]*?)(<\?php\s*endif;\s*\?>)/i';
    $matches = [];
    if (!preg_match($pattern, $raw, $matches)) {
        return false;
    }

    $existingRo = (string) ($matches[2] ?? '');
    $existingEn = (string) ($matches[4] ?? '');
    $contentRo = admin_model_pages_preserve_footer_blocks($existingRo, $contentRo);
    $contentEn = admin_model_pages_preserve_footer_blocks($existingEn, $contentEn);

    $roBlock = admin_model_pages_format_content_for_php_branch($contentRo, 5);
    $enBlock = admin_model_pages_format_content_for_php_branch($contentEn, 5);

    $updated = preg_replace(
        $pattern,
        '$1' . "\n" . $roBlock . "\n" . '$3' . "\n" . $enBlock . "\n" . '$5',
        $raw,
        1
    );

    if (!is_string($updated) || $updated === '') {
        return false;
    }

    if ($updated === $raw) {
        return true;
    }

    return @file_put_contents($absoluteFile, $updated, LOCK_EX) !== false;
}

if (isset($_GET['saved'])) {
    $flashType = 'success';
    $flashMessage = 'Page saved successfully.';
} elseif (isset($_GET['deleted'])) {
    $flashType = 'success';
    $flashMessage = 'Page deleted successfully.';
} elseif (isset($_GET['error'])) {
    $flashType = 'error';
    $flashMessage = trim((string) $_GET['error']);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $postedToken = (string) ($_POST['csrf_token'] ?? '');
    if (!hash_equals($csrfToken, $postedToken)) {
        $flashType = 'error';
        $flashMessage = 'Invalid form token. Please refresh and try again.';
    } else {
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'save_page') {
            $targetId = trim((string) ($_POST['id'] ?? ''));
            $season = strtolower(trim((string) ($_POST['season'] ?? 'intothedeep')));
            $slugInput = trim((string) ($_POST['slug'] ?? ''));
            $titleEn = trim((string) ($_POST['title_en'] ?? ''));
            $titleRo = trim((string) ($_POST['title_ro'] ?? ''));
            $contentEn = trim((string) ($_POST['content_en'] ?? ''));
            $contentRo = trim((string) ($_POST['content_ro'] ?? ''));
            $isActive = ((string) ($_POST['is_active'] ?? '0')) === '1';

            if (!alphabit_model_pages_is_valid_season($season)) {
                $season = 'intothedeep';
            }

            $slug = alphabit_model_pages_slugify($slugInput);
            if ($slug === '') {
                $slug = alphabit_model_pages_slugify($titleEn !== '' ? $titleEn : $titleRo);
            }

            if ($targetId === '' && $slug !== '') {
                $existingSameRoute = alphabit_model_pages_find($pages, $season, $slug, false);
                if (is_array($existingSameRoute) && isset($existingSameRoute['id'])) {
                    $targetId = (string) $existingSameRoute['id'];
                }
            }

            if ($slug === '') {
                $flashType = 'error';
                $flashMessage = 'Please provide a slug or at least one title.';
            } elseif ($titleEn === '') {
                $flashType = 'error';
                $flashMessage = 'English title is required.';
            } else {
                $contentEnSanitized = alphabit_model_pages_sanitize_html($contentEn);
                $contentRoSanitized = alphabit_model_pages_sanitize_html($contentRo);
                $builtinRoute = alphabit_model_builtin_find($season, $slug);

                if (is_array($builtinRoute)) {
                    $builtinFile = (string) ($builtinRoute['file'] ?? '');
                    if ($builtinFile === '') {
                        $flashType = 'error';
                        $flashMessage = 'Built-in route file is missing.';
                    } elseif (!admin_model_pages_update_builtin_source_file($builtinFile, $contentRoSanitized, $contentEnSanitized)) {
                        $flashType = 'error';
                        $flashMessage = 'Could not update the built-in source file. Please check file permissions.';
                    } else {
                        admin_model_pages_delete_by_route($pages, $season, $slug);
                        if (!alphabit_model_pages_save_all($pages)) {
                            $flashType = 'error';
                            $flashMessage = 'Built-in file was updated, but cleanup of legacy override storage failed.';
                        } else {
                            admin_model_pages_redirect('saved=1&season=' . rawurlencode($season) . '&slug=' . rawurlencode($slug));
                        }
                    }
                } else {
                $duplicate = false;
                foreach ($pages as $existingPage) {
                    if (!is_array($existingPage)) {
                        continue;
                    }
                    if (($existingPage['season'] ?? '') !== $season) {
                        continue;
                    }
                    if (($existingPage['slug'] ?? '') !== $slug) {
                        continue;
                    }
                    if ($targetId !== '' && ($existingPage['id'] ?? '') === $targetId) {
                        continue;
                    }
                    $duplicate = true;
                    break;
                }

                if ($duplicate) {
                    $flashType = 'error';
                    $flashMessage = 'Another page already uses this season + slug.';
                } else {
                    $record = [
                        'id' => $targetId,
                        'season' => $season,
                        'slug' => $slug,
                        'title_en' => $titleEn,
                        'title_ro' => $titleRo,
                        'content_en' => $contentEnSanitized,
                        'content_ro' => $contentRoSanitized,
                        'is_active' => $isActive,
                    ];

                    $savedRecord = alphabit_model_pages_upsert($pages, $record, $targetId !== '' ? $targetId : null);
                    if (!alphabit_model_pages_save_all($pages)) {
                        $flashType = 'error';
                        $flashMessage = 'Could not save pages storage file.';
                    } else {
                        admin_model_pages_redirect('saved=1&season=' . rawurlencode($savedRecord['season']) . '&slug=' . rawurlencode($savedRecord['slug']));
                    }
                }
                }
            }

            $formValues = [
                'id' => $targetId,
                'season' => $season,
                'slug' => $slug !== '' ? $slug : $slugInput,
                'title_en' => $titleEn,
                'title_ro' => $titleRo,
                'content_en' => $contentEn,
                'content_ro' => $contentRo,
                'is_active' => $isActive,
            ];
        } elseif ($action === 'delete_page') {
            $targetId = trim((string) ($_POST['id'] ?? ''));
            if ($targetId === '') {
                $flashType = 'error';
                $flashMessage = 'Missing page identifier.';
            } elseif (!alphabit_model_pages_delete($pages, $targetId)) {
                $flashType = 'error';
                $flashMessage = 'Page was not found.';
            } elseif (!alphabit_model_pages_save_all($pages)) {
                $flashType = 'error';
                $flashMessage = 'Could not save pages storage file.';
            } else {
                admin_model_pages_redirect('deleted=1');
            }
        } elseif ($action === 'upload_image') {
            $targetId = trim((string) ($_POST['id'] ?? ''));
            $season = strtolower(trim((string) ($_POST['season'] ?? 'intothedeep')));
            if (!alphabit_model_pages_is_valid_season($season)) {
                $season = 'intothedeep';
            }
            $slug = trim((string) ($_POST['slug'] ?? ''));
            $titleEn = trim((string) ($_POST['title_en'] ?? ''));
            $titleRo = trim((string) ($_POST['title_ro'] ?? ''));
            $contentEn = trim((string) ($_POST['content_en'] ?? ''));
            $contentRo = trim((string) ($_POST['content_ro'] ?? ''));
            $isActive = isset($_POST['is_active']);

            $formValues = [
                'id' => $targetId,
                'season' => $season,
                'slug' => $slug,
                'title_en' => $titleEn,
                'title_ro' => $titleRo,
                'content_en' => $contentEn,
                'content_ro' => $contentRo,
                'is_active' => $isActive,
            ];

            if (!isset($_FILES['image_file']) || !is_array($_FILES['image_file'])) {
                $flashType = 'error';
                $flashMessage = 'No image selected.';
            } else {
                $upload = $_FILES['image_file'];
                $errorCode = (int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE);

                if ($errorCode !== UPLOAD_ERR_OK) {
                    $flashType = 'error';
                    $flashMessage = 'Upload failed with error code: ' . $errorCode . '.';
                } else {
                    $tmpPath = (string) ($upload['tmp_name'] ?? '');
                    $originalName = (string) ($upload['name'] ?? '');
                    $size = (int) ($upload['size'] ?? 0);

                    if ($size <= 0 || $size > 8 * 1024 * 1024) {
                        $flashType = 'error';
                        $flashMessage = 'Image must be between 1 byte and 8 MB.';
                    } elseif (!is_uploaded_file($tmpPath)) {
                        $flashType = 'error';
                        $flashMessage = 'Invalid uploaded file.';
                    } else {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime = $finfo ? (string) finfo_file($finfo, $tmpPath) : '';
                        if ($finfo) {
                            finfo_close($finfo);
                        }

                        $allowedMimes = [
                            'image/jpeg' => 'jpg',
                            'image/png' => 'png',
                            'image/webp' => 'webp',
                            'image/gif' => 'gif',
                        ];

                        if (!isset($allowedMimes[$mime])) {
                            $flashType = 'error';
                            $flashMessage = 'Only JPG, PNG, WEBP, and GIF images are allowed.';
                        } else {
                            $uploadDirRelative = '/assets/uploads/model_pages';
                            $uploadDirAbsolute = dirname(__DIR__) . $uploadDirRelative;
                            if (!is_dir($uploadDirAbsolute)) {
                                @mkdir($uploadDirAbsolute, 0775, true);
                            }

                            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
                            $baseName = alphabit_model_pages_slugify((string) $baseName);
                            if ($baseName === '') {
                                $baseName = 'image';
                            }

                            $finalName = $baseName . '-' . date('YmdHis') . '-' . substr(alphabit_model_pages_generate_id(), 0, 6) . '.' . $allowedMimes[$mime];
                            $destinationAbsolute = $uploadDirAbsolute . '/' . $finalName;
                            $destinationRelative = $uploadDirRelative . '/' . $finalName;

                            if (!move_uploaded_file($tmpPath, $destinationAbsolute)) {
                                $flashType = 'error';
                                $flashMessage = 'Could not move uploaded image.';
                            } else {
                                $uploadedImagePath = $destinationRelative;
                                $flashType = 'success';
                                $flashMessage = 'Image uploaded successfully. Use the generated snippet below.';
                            }
                        }
                    }
                }
            }
        }
    }
}

$seasonFilter = strtolower(trim((string) ($_GET['season'] ?? '')));
if (!alphabit_model_pages_is_valid_season($seasonFilter)) {
    $seasonFilter = '';
}
$slugFilter = alphabit_model_pages_slugify((string) ($_GET['slug'] ?? ''));

if ($formValues['id'] === '' && $seasonFilter !== '' && $slugFilter !== '') {
    $selectedCustomPage = alphabit_model_pages_find($pages, $seasonFilter, $slugFilter, false);
    $selectedBuiltinRoute = alphabit_model_builtin_find($seasonFilter, $slugFilter);

    if (is_array($selectedBuiltinRoute)) {
        $formValues = admin_model_pages_set_form_from_builtin($selectedBuiltinRoute, $selectedCustomPage);
    } elseif (is_array($selectedCustomPage)) {
        $formValues = admin_model_pages_set_form_from_page($selectedCustomPage);
    }
}

$pageListItems = admin_model_pages_build_list_items($pages);
$selectedItemKey = '';
if (alphabit_model_pages_is_valid_season((string) $formValues['season']) && trim((string) $formValues['slug']) !== '') {
    $selectedItemKey = alphabit_model_builtin_key((string) $formValues['season'], (string) $formValues['slug']);
}

$selectedBuiltinRoute = null;
$selectedIsBuiltin = false;
if ($selectedItemKey !== '') {
    $selectedBuiltinRoute = alphabit_model_builtin_find((string) $formValues['season'], (string) $formValues['slug']);
    $selectedIsBuiltin = is_array($selectedBuiltinRoute);
}
$autoImportBuiltin = $selectedIsBuiltin
    && (string) $formValues['id'] === ''
    && trim((string) $formValues['content_en']) === ''
    && trim((string) $formValues['content_ro']) === '';

$previewUrl = '';
if ($formValues['slug'] !== '' && alphabit_model_pages_is_valid_season((string) $formValues['season'])) {
    $previewUrl = '/model/' . $formValues['season'] . '/' . alphabit_model_pages_slugify((string) $formValues['slug']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Model Pages Admin - AlphaBit OpenML</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/images/alphabit.ico">
    <style>
        :root {
            --font-main: 'Manrope', 'Montserrat', sans-serif;
            --ink-900: #f5f5f4;
            --ink-700: #d0d0cc;
            --ink-600: #a3a39d;
            --surface: rgba(12, 12, 12, 0.9);
            --border: rgba(255, 255, 255, 0.14);
            --control-bg: #f5f5f4;
            --control-bg-hover: #ffffff;
            --control-border: rgba(0, 0, 0, 0.16);
            --control-ink: #0a0a0a;
            --danger-bg: #f05f5f;
            --danger-bg-hover: #ff7575;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100svh;
            font-family: var(--font-main);
            color: var(--ink-900);
            background: linear-gradient(165deg, #040404 0%, #090909 52%, #101010 100%);
        }

        .site-navbar {
            position: sticky;
            top: 0;
            z-index: 90;
            width: 100%;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 1rem;
            padding: 0.78rem clamp(1rem, 3vw, 3.5rem);
            background: rgba(8, 8, 8, 0.86);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.09);
        }

        .brand-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-weight: 800;
            letter-spacing: 0.01em;
            font-size: clamp(1.05rem, 1.3vw, 1.2rem);
            color: inherit;
        }

        .brand-logo {
            width: 2rem;
            height: 2rem;
            object-fit: contain;
            transform: translateY(4px);
        }

        .navbar-actions {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 0.5rem 0.9rem;
            border-radius: 999px;
            border: 1px solid var(--control-border);
            color: var(--control-ink);
            background: var(--control-bg);
            font-weight: 650;
            font-size: 0.89rem;
        }

        .admin-layout {
            width: min(1280px, 100%);
            margin: 0 auto;
            padding: 1rem;
            display: grid;
            grid-template-columns: 340px minmax(0, 1fr);
            gap: 1rem;
        }

        .card {
            border-radius: 1rem;
            border: 1px solid var(--border);
            background: var(--surface);
            padding: 1rem;
        }

        .card h1,
        .card h2 {
            margin: 0;
            font-size: 1.12rem;
        }

        .helper {
            margin-top: 0.5rem;
            color: var(--ink-600);
            line-height: 1.5;
            font-size: 0.92rem;
        }

        .flash {
            margin: 0 0 1rem;
            padding: 0.72rem 0.85rem;
            border-radius: 0.75rem;
            font-size: 0.92rem;
            line-height: 1.45;
            border: 1px solid transparent;
        }

        .flash.success {
            background: rgba(88, 179, 114, 0.13);
            border-color: rgba(88, 179, 114, 0.35);
            color: #a8e6b4;
        }

        .flash.error {
            background: rgba(240, 95, 95, 0.13);
            border-color: rgba(240, 95, 95, 0.35);
            color: #ffb3b3;
        }

        .page-list {
            margin-top: 0.85rem;
            display: grid;
            gap: 0.55rem;
            max-height: calc(100vh - 270px);
            overflow: auto;
            padding-right: 0.2rem;
        }

        .page-item {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0.8rem;
            padding: 0.65rem 0.7rem;
            text-decoration: none;
            color: inherit;
            display: grid;
            gap: 0.25rem;
            background: rgba(8, 8, 8, 0.64);
        }

        .page-item:hover {
            border-color: rgba(255, 255, 255, 0.26);
        }

        .page-item.active {
            border-color: rgba(255, 255, 255, 0.46);
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.18) inset;
        }

        .page-item small {
            color: var(--ink-600);
            font-size: 0.78rem;
        }

        .badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 0.2rem 0.5rem;
            font-size: 0.7rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: fit-content;
        }

        .badge.off {
            opacity: 0.7;
        }

        .badge.type-builtin {
            background: rgba(91, 154, 235, 0.14);
            border-color: rgba(91, 154, 235, 0.38);
            color: #d6e6ff;
        }

        .badge.type-custom {
            background: rgba(122, 212, 143, 0.12);
            border-color: rgba(122, 212, 143, 0.34);
            color: #d2f6dc;
        }

        .badge.type-override {
            background: rgba(248, 195, 100, 0.12);
            border-color: rgba(248, 195, 100, 0.36);
            color: #ffe6b0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .field {
            display: grid;
            gap: 0.36rem;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 0.84rem;
            color: var(--ink-700);
            font-weight: 600;
        }

        input[type="text"],
        select,
        textarea {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 0.7rem;
            background: rgba(5, 5, 5, 0.88);
            color: var(--ink-900);
            font: inherit;
            padding: 0.62rem 0.7rem;
            outline: none;
        }

        input[type="text"]:focus,
        select:focus,
        textarea:focus {
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.12);
        }

        textarea {
            min-height: 180px;
            resize: vertical;
            line-height: 1.45;
        }

        .toggle-row {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            margin-top: 0.2rem;
            color: var(--ink-700);
            font-size: 0.9rem;
        }

        .button-row {
            margin-top: 0.9rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid var(--control-border);
            padding: 0.58rem 1rem;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            color: var(--control-ink);
            background: var(--control-bg);
        }

        .btn:hover {
            background: var(--control-bg-hover);
        }

        .btn-danger {
            background: var(--danger-bg);
            border-color: rgba(0, 0, 0, 0.16);
            color: #180101;
        }

        .btn-danger:hover {
            background: var(--danger-bg-hover);
        }

        .upload-card {
            margin-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1rem;
        }

        .snippet {
            margin-top: 0.6rem;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 0.65rem;
            background: rgba(6, 6, 6, 0.9);
            padding: 0.65rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
            font-size: 0.82rem;
            color: #f5f5f4;
            overflow: auto;
            white-space: nowrap;
        }

        .preview-link {
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }

        .preview-link a {
            color: #f5f5f4;
        }

        .import-status {
            margin-top: 0.65rem;
            min-height: 1.2rem;
            color: var(--ink-700);
            font-size: 0.86rem;
        }

        .section-builder {
            margin-top: 0.45rem;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 0.9rem;
            padding: 0.85rem;
            background: rgba(6, 6, 6, 0.7);
            display: grid;
            gap: 0.65rem;
        }

        .section-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.45rem;
        }

        .section-toolbar label {
            margin-right: 0.15rem;
        }

        .section-toolbar select {
            width: auto;
            min-width: 170px;
        }

        .section-list {
            display: grid;
            gap: 0.6rem;
        }

        .section-item {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0.8rem;
            padding: 0.62rem;
            background: rgba(4, 4, 4, 0.72);
            display: grid;
            gap: 0.48rem;
        }

        .section-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.45rem;
        }

        .section-row select,
        .section-row input[type="text"] {
            width: auto;
            min-width: 140px;
            flex: 1 1 180px;
        }

        .section-custom-class {
            display: none;
        }

        .section-field {
            display: none;
            gap: 0.4rem;
        }

        .section-field.active {
            display: grid;
        }

        .section-field textarea {
            min-height: 110px;
        }

        .section-actions {
            justify-content: flex-end;
        }

        .section-empty {
            color: var(--ink-600);
            font-size: 0.87rem;
            border: 1px dashed rgba(255, 255, 255, 0.16);
            border-radius: 0.75rem;
            padding: 0.75rem;
            text-align: center;
        }

        @media (max-width: 980px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .page-list {
                max-height: 380px;
            }
        }
    </style>
</head>

<body>
    <header class="site-navbar">
        <a class="brand-link" href="/">
            <span>AlphaBit OpenML</span>
            <img class="brand-logo" src="/assets/images/ai_star_alpha.png" alt="AlphaBit logo">
        </a>
        <div class="navbar-actions">
            <a class="nav-link" href="/model/<?php echo htmlspecialchars($season_path, ENT_QUOTES, 'UTF-8'); ?>/overview">Model Docs</a>
            <a class="nav-link" href="/profile"><?php echo htmlspecialchars($team_name, ENT_QUOTES, 'UTF-8'); ?></a>
            <a class="nav-link" href="/logout">Logout</a>
        </div>
    </header>

    <main class="admin-layout">
        <section class="card">
            <h1>Model Pages</h1>
            <p class="helper">Edit any existing model route or create a new page for <code>/model/intothedeep/*</code> and <code>/model/decode/*</code>.</p>
            <p class="helper">Active custom pages are auto-listed in a shared <code>Custom Pages</code> section across all model documentation pages.</p>
            <a class="btn" href="/admin/model-pages" style="margin-top:0.5rem; width:100%;">New Blank Page</a>

            <div class="page-list">
                <?php if (count($pageListItems) === 0): ?>
                    <p class="helper">No model pages found.</p>
                <?php else: ?>
                    <?php foreach ($pageListItems as $item): ?>
                        <?php
                        $pageTitle = (string) ($item['title'] ?? '');
                        $pageSlug = (string) ($item['slug'] ?? '');
                        $pageSeason = (string) ($item['season'] ?? 'intothedeep');
                        $isActive = !empty($item['is_active']);
                        $itemType = (string) ($item['type'] ?? 'custom');
                        $hasOverride = !empty($item['has_override']);
                        $editLink = (string) ($item['edit_link'] ?? '/admin/model-pages');
                        $isSelected = ((string) ($item['key'] ?? '') !== '' && (string) ($item['key'] ?? '') === $selectedItemKey);
                        ?>
                        <a class="page-item <?php echo $isSelected ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($editLink, ENT_QUOTES, 'UTF-8'); ?>">
                            <strong><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></strong>
                            <small>/model/<?php echo htmlspecialchars($pageSeason, ENT_QUOTES, 'UTF-8'); ?>/<?php echo htmlspecialchars($pageSlug, ENT_QUOTES, 'UTF-8'); ?></small>
                            <div class="badge-row">
                                <span class="badge <?php echo $isActive ? '' : 'off'; ?>">
                                    <?php echo $isActive ? 'active' : 'inactive'; ?>
                                </span>
                                <?php if ($itemType === 'builtin'): ?>
                                    <span class="badge type-builtin">built-in</span>
                                    <?php if ($hasOverride): ?>
                                        <span class="badge type-override">override</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge type-custom">custom</span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="card">
            <h2>Editor</h2>
            <p class="helper">Allowed HTML tags in content: <code>p, h1-h6, strong, em, ul, ol, li, a, img, code, pre, blockquote</code>.</p>
            <?php if ($selectedIsBuiltin): ?>
                <?php if ((string) $formValues['id'] === ''): ?>
                    <p class="helper">This is a built-in route. Existing content can be imported below; saving writes directly into the source PHP file.</p>
                <?php else: ?>
                    <p class="helper">This built-in route has a legacy override record. Saving now migrates changes into the source PHP file and removes the legacy override.</p>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($flashMessage !== ''): ?>
                <div class="flash <?php echo htmlspecialchars($flashType, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form id="model-page-save-form" method="post" action="/admin/model-pages">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="action" value="save_page">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars((string) $formValues['id'], ENT_QUOTES, 'UTF-8'); ?>">

                <div class="form-grid">
                    <div class="field">
                        <label for="season">Season</label>
                        <select id="season" name="season">
                            <option value="intothedeep" <?php echo ((string) $formValues['season'] === 'intothedeep') ? 'selected' : ''; ?>>IntoTheDeep</option>
                            <option value="decode" <?php echo ((string) $formValues['season'] === 'decode') ? 'selected' : ''; ?>>Decode</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="slug">Slug</label>
                        <input id="slug" type="text" name="slug" maxlength="64" value="<?php echo htmlspecialchars((string) $formValues['slug'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="example: camera-tips">
                    </div>

                    <div class="field">
                        <label for="title_en">Title (English)</label>
                        <input id="title_en" type="text" name="title_en" maxlength="120" value="<?php echo htmlspecialchars((string) $formValues['title_en'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div class="field">
                        <label for="title_ro">Title (Romanian)</label>
                        <input id="title_ro" type="text" name="title_ro" maxlength="120" value="<?php echo htmlspecialchars((string) $formValues['title_ro'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="field full">
                        <label for="content_en">Content (English HTML)</label>
                        <textarea id="content_en" name="content_en"><?php echo htmlspecialchars((string) $formValues['content_en'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div class="field full">
                        <label for="content_ro">Content (Romanian HTML)</label>
                        <textarea id="content_ro" name="content_ro"><?php echo htmlspecialchars((string) $formValues['content_ro'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div class="field full">
                        <label for="section-target">Section Composer</label>
                        <div class="section-builder" id="section-builder" data-last-upload="<?php echo htmlspecialchars((string) $uploadedImagePath, ENT_QUOTES, 'UTF-8'); ?>">
                            <p class="helper">Add content in structured blocks (for example <code>stext</code> / <code>rtext</code>) instead of writing full HTML manually.</p>
                            <div class="section-toolbar">
                                <label for="section-target">Target</label>
                                <select id="section-target">
                                    <option value="en">English Content</option>
                                    <option value="ro">Romanian Content</option>
                                </select>
                                <button type="button" class="btn" id="sections-load">Load Sections From Target</button>
                                <button type="button" class="btn" id="sections-add">+ Add Section</button>
                                <button type="button" class="btn" id="sections-append">Append Sections To Target</button>
                                <button type="button" class="btn" id="sections-apply">Replace Target With Sections</button>
                            </div>
                            <div class="section-list" id="sections-list"></div>
                            <div class="import-status" id="sections-status"></div>
                        </div>
                    </div>
                </div>

                <label class="toggle-row">
                    <input type="checkbox" name="is_active" value="1" <?php echo !empty($formValues['is_active']) ? 'checked' : ''; ?>>
                    Page is active and visible.
                </label>

                <div class="button-row">
                    <button type="submit" class="btn">Save Page</button>
                    <?php if ($selectedIsBuiltin): ?>
                        <button
                            type="button"
                            class="btn"
                            id="import-builtin-content"
                            data-season="<?php echo htmlspecialchars((string) $formValues['season'], ENT_QUOTES, 'UTF-8'); ?>"
                            data-slug="<?php echo htmlspecialchars((string) $formValues['slug'], ENT_QUOTES, 'UTF-8'); ?>"
                            data-auto="<?php echo $autoImportBuiltin ? '1' : '0'; ?>"
                        >
                            Import Existing Content
                        </button>
                    <?php endif; ?>
                </div>
                <?php if ($selectedIsBuiltin): ?>
                    <div class="import-status" id="import-builtin-content-status"></div>
                <?php endif; ?>

                <?php if ($previewUrl !== ''): ?>
                    <p class="preview-link">Preview: <a href="<?php echo htmlspecialchars($previewUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($previewUrl, ENT_QUOTES, 'UTF-8'); ?></a></p>
                <?php endif; ?>
            </form>

            <?php if ((string) $formValues['id'] !== ''): ?>
                <form method="post" action="/admin/model-pages" onsubmit="return confirm('Delete this page?');" style="margin-top:0.9rem;">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="action" value="delete_page">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars((string) $formValues['id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="btn btn-danger"><?php echo $selectedIsBuiltin ? 'Delete Override' : 'Delete Page'; ?></button>
                </form>
            <?php endif; ?>

            <section class="upload-card">
                <h2>Upload Image</h2>
                <p class="helper">Upload to <code>/assets/uploads/model_pages/</code>, then paste the generated HTML snippet into your content. Save the page first if you have unsaved textarea edits.</p>
                <form method="post" action="/admin/model-pages" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="action" value="upload_image">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars((string) $formValues['id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="season" value="<?php echo htmlspecialchars((string) $formValues['season'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="slug" value="<?php echo htmlspecialchars((string) $formValues['slug'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="title_en" value="<?php echo htmlspecialchars((string) $formValues['title_en'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="title_ro" value="<?php echo htmlspecialchars((string) $formValues['title_ro'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="content_en" value="<?php echo htmlspecialchars((string) $formValues['content_en'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="content_ro" value="<?php echo htmlspecialchars((string) $formValues['content_ro'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="is_active" value="<?php echo !empty($formValues['is_active']) ? '1' : '0'; ?>">

                    <div class="button-row">
                        <input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,.gif" required>
                        <button type="submit" class="btn">Upload Image</button>
                    </div>
                </form>

                <?php if ($uploadedImagePath !== ''): ?>
                    <div class="snippet">&lt;img src="<?php echo htmlspecialchars($uploadedImagePath, ENT_QUOTES, 'UTF-8'); ?>" alt=""&gt;</div>
                    <div class="snippet"><?php echo htmlspecialchars($uploadedImagePath, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </section>
        </section>
    </main>
    <script>
        (function () {
            var importButton = document.getElementById('import-builtin-content');
            if (!importButton) {
                return;
            }

            var statusNode = document.getElementById('import-builtin-content-status');
            var contentEnField = document.getElementById('content_en');
            var contentRoField = document.getElementById('content_ro');
            var titleEnField = document.getElementById('title_en');
            var titleRoField = document.getElementById('title_ro');
            var season = (importButton.getAttribute('data-season') || '').trim();
            var slug = (importButton.getAttribute('data-slug') || '').trim();
            var autoImport = importButton.getAttribute('data-auto') === '1';

            if (!season || !slug || !contentEnField || !contentRoField || !titleEnField || !titleRoField) {
                return;
            }

            function setStatus(message, isError) {
                if (!statusNode) {
                    return;
                }
                statusNode.textContent = message;
                statusNode.style.color = isError ? '#ffb3b3' : '#d0d0cc';
            }

            function extractPageParts(rawHtml) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(rawHtml, 'text/html');
                var titleNode = doc.querySelector('.rbox .title');
                var contentNode = doc.querySelector('.rbox .text-container') || doc.querySelector('.custom-page-content');
                if (!contentNode) {
                    return null;
                }

                var removeSelectors = ['.chat-launch-button', '#chat-widget'];
                for (var i = 0; i < removeSelectors.length; i++) {
                    var nodes = contentNode.querySelectorAll(removeSelectors[i]);
                    for (var j = 0; j < nodes.length; j++) {
                        nodes[j].remove();
                    }
                }

                return {
                    title: titleNode ? titleNode.textContent.trim() : '',
                    content: contentNode.innerHTML.trim()
                };
            }

            async function fetchLanguageContent(lang) {
                var slugs = [slug];
                var legacySlug = slug.indexOf('-') >= 0 ? slug.replace(/-/g, '_') : slug;
                if (legacySlug !== slug) {
                    slugs.push(legacySlug);
                }
                var endpoints = [];
                for (var i = 0; i < slugs.length; i++) {
                    var currentSlug = slugs[i];
                    endpoints.push('/model/' + encodeURIComponent(season) + '/' + encodeURIComponent(currentSlug));
                    endpoints.push('/model_pages/router.php?season=' + encodeURIComponent(season) + '&slug=' + encodeURIComponent(currentSlug));
                }

                var lastError = 'HTTP 404';
                for (var j = 0; j < endpoints.length; j++) {
                    var baseUrl = endpoints[j];
                    var joiner = baseUrl.indexOf('?') >= 0 ? '&' : '?';
                    var requestUrl = baseUrl + joiner
                        + '__admin_lang=' + encodeURIComponent(lang)
                        + '&__admin_editor=1'
                        + '&_=' + Date.now();

                    try {
                        var response = await fetch(requestUrl, {
                            method: 'GET',
                            credentials: 'same-origin',
                            cache: 'no-store'
                        });
                        if (!response.ok) {
                            lastError = 'HTTP ' + response.status;
                            continue;
                        }

                        var body = await response.text();
                        var parsed = extractPageParts(body);
                        if (parsed && parsed.content) {
                            return parsed;
                        }

                        lastError = 'Could not extract page content';
                    } catch (requestError) {
                        lastError = requestError && requestError.message ? requestError.message : 'network error';
                    }
                }

                throw new Error(lastError + ' (' + lang + ')');
            }

            async function importContent(forceOverwrite) {
                importButton.disabled = true;
                setStatus('Loading existing page content...', false);

                try {
                    var english = await fetchLanguageContent('en');
                    var romanian = await fetchLanguageContent('ro');

                    if (english.title && (!titleEnField.value.trim() || forceOverwrite)) {
                        titleEnField.value = english.title;
                    }
                    if (romanian.title && (!titleRoField.value.trim() || forceOverwrite)) {
                        titleRoField.value = romanian.title;
                    }
                    if (english.content && (!contentEnField.value.trim() || forceOverwrite)) {
                        contentEnField.value = english.content;
                    }
                    if (romanian.content && (!contentRoField.value.trim() || forceOverwrite)) {
                        contentRoField.value = romanian.content;
                    }

                    setStatus('Existing content imported. Review and click Save Page to keep it as an override.', false);
                    if (typeof document !== 'undefined') {
                        document.dispatchEvent(new CustomEvent('alphabit:model-content-imported'));
                    }
                } catch (error) {
                    setStatus('Import failed: ' + (error && error.message ? error.message : 'unknown error'), true);
                } finally {
                    importButton.disabled = false;
                }
            }

            importButton.addEventListener('click', function () {
                importContent(true);
            });

            if (autoImport) {
                importContent(false);
            }
        })();
    </script>
    <script>
        (function () {
            var builder = document.getElementById('section-builder');
            var sectionList = document.getElementById('sections-list');
            var targetSelect = document.getElementById('section-target');
            var loadButton = document.getElementById('sections-load');
            var addButton = document.getElementById('sections-add');
            var appendButton = document.getElementById('sections-append');
            var applyButton = document.getElementById('sections-apply');
            var statusNode = document.getElementById('sections-status');
            var contentEnField = document.getElementById('content_en');
            var contentRoField = document.getElementById('content_ro');
            var saveForm = document.getElementById('model-page-save-form');

            if (!builder || !sectionList || !targetSelect || !loadButton || !addButton || !appendButton || !applyButton || !statusNode || !contentEnField || !contentRoField || !saveForm) {
                return;
            }

            var knownClasses = ['stext', 'rtext', 'title'];
            var lastUploadedPath = (builder.getAttribute('data-last-upload') || '').trim();
            var builderDirty = false;

            function setStatus(message, isError) {
                statusNode.textContent = message;
                statusNode.style.color = isError ? '#ffb3b3' : '#d0d0cc';
            }

            function escapeHtml(value) {
                return String(value || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            function escapeAttr(value) {
                return String(value || '')
                    .replace(/&/g, '&amp;')
                    .replace(/"/g, '&quot;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            function normalizeClassName(value) {
                return String(value || '').trim().replace(/\s+/g, ' ');
            }

            function getTargetField() {
                return targetSelect.value === 'ro' ? contentRoField : contentEnField;
            }

            function updateEmptyState() {
                if (sectionList.querySelector('.section-item')) {
                    return;
                }

                sectionList.innerHTML = '';
                var emptyNode = document.createElement('div');
                emptyNode.className = 'section-empty';
                emptyNode.textContent = 'No sections yet. Click "+ Add Section" or load from current content.';
                sectionList.appendChild(emptyNode);
            }

            function getClassFromRow(row) {
                var classChoice = row.querySelector('.section-class').value;
                if (classChoice === 'none') {
                    return '';
                }
                if (classChoice === 'custom') {
                    return normalizeClassName(row.querySelector('.section-custom-class').value);
                }
                return classChoice;
            }

            function renderSectionFieldState(row) {
                var wrapper = row.querySelector('.section-wrapper').value;
                var classChoice = row.querySelector('.section-class').value;
                var kind = row.querySelector('.section-kind').value;
                var customClassInput = row.querySelector('.section-custom-class');
                var classSelect = row.querySelector('.section-class');

                classSelect.disabled = wrapper === 'raw';
                customClassInput.style.display = (wrapper === 'div' && classChoice === 'custom') ? 'block' : 'none';

                var fields = row.querySelectorAll('.section-field');
                for (var i = 0; i < fields.length; i++) {
                    fields[i].classList.remove('active');
                }

                var activeField = row.querySelector('.section-field-' + kind);
                if (activeField) {
                    activeField.classList.add('active');
                }
            }

            function createSectionRow(data) {
                var config = data || {};
                var row = document.createElement('div');
                row.className = 'section-item';
                row.innerHTML = ''
                    + '<div class="section-row">'
                    + '<select class="section-wrapper">'
                    + '<option value="div">Div Section</option>'
                    + '<option value="raw">Raw Block</option>'
                    + '</select>'
                    + '<select class="section-class">'
                    + '<option value="stext">stext</option>'
                    + '<option value="rtext">rtext</option>'
                    + '<option value="title">title</option>'
                    + '<option value="none">no class</option>'
                    + '<option value="custom">custom class</option>'
                    + '</select>'
                    + '<input type="text" class="section-custom-class" placeholder="custom class name">'
                    + '<select class="section-kind">'
                    + '<option value="text">Text</option>'
                    + '<option value="html">HTML</option>'
                    + '<option value="image">Image</option>'
                    + '</select>'
                    + '</div>'
                    + '<div class="section-field section-field-text">'
                    + '<textarea class="section-text" placeholder="Text content..."></textarea>'
                    + '</div>'
                    + '<div class="section-field section-field-html">'
                    + '<textarea class="section-html" placeholder="HTML content..."></textarea>'
                    + '</div>'
                    + '<div class="section-field section-field-image">'
                    + '<input type="text" class="section-image-src" placeholder="Image src (example: /assets/uploads/model_pages/img.png)">'
                    + '<input type="text" class="section-image-alt" placeholder="Image alt text">'
                    + '<input type="text" class="section-image-style" placeholder="Optional inline style (example: border-radius:10px; width:60%)">'
                    + '<div class="section-row">'
                    + '<button type="button" class="btn section-use-upload">Use Last Upload</button>'
                    + '</div>'
                    + '</div>'
                    + '<div class="section-row section-actions">'
                    + '<button type="button" class="btn section-up">Up</button>'
                    + '<button type="button" class="btn section-down">Down</button>'
                    + '<button type="button" class="btn btn-danger section-remove">Remove</button>'
                    + '</div>';

                var wrapperSelect = row.querySelector('.section-wrapper');
                var classSelect = row.querySelector('.section-class');
                var customClassInput = row.querySelector('.section-custom-class');
                var kindSelect = row.querySelector('.section-kind');
                var textArea = row.querySelector('.section-text');
                var htmlArea = row.querySelector('.section-html');
                var imageSrc = row.querySelector('.section-image-src');
                var imageAlt = row.querySelector('.section-image-alt');
                var imageStyle = row.querySelector('.section-image-style');

                wrapperSelect.value = config.wrapper === 'raw' ? 'raw' : 'div';

                var classValue = normalizeClassName(config.className || '');
                if (classValue === '') {
                    classSelect.value = 'none';
                } else if (knownClasses.indexOf(classValue) !== -1) {
                    classSelect.value = classValue;
                } else {
                    classSelect.value = 'custom';
                    customClassInput.value = classValue;
                }

                var kindValue = config.kind === 'image' || config.kind === 'html' ? config.kind : 'text';
                kindSelect.value = kindValue;
                textArea.value = String(config.text || '');
                htmlArea.value = String(config.html || '');
                imageSrc.value = String(config.imageSrc || '');
                imageAlt.value = String(config.imageAlt || '');
                imageStyle.value = String(config.imageStyle || '');

                wrapperSelect.addEventListener('change', function () {
                    renderSectionFieldState(row);
                });
                classSelect.addEventListener('change', function () {
                    renderSectionFieldState(row);
                });
                kindSelect.addEventListener('change', function () {
                    renderSectionFieldState(row);
                });

                row.querySelector('.section-use-upload').addEventListener('click', function () {
                    if (!lastUploadedPath) {
                        setStatus('No uploaded image path available in this session yet.', true);
                        return;
                    }
                    imageSrc.value = lastUploadedPath;
                    builderDirty = true;
                    setStatus('Inserted latest uploaded path into this image section.', false);
                });

                row.querySelector('.section-remove').addEventListener('click', function () {
                    row.remove();
                    builderDirty = true;
                    updateEmptyState();
                });

                row.querySelector('.section-up').addEventListener('click', function () {
                    var previous = row.previousElementSibling;
                    if (!previous || previous.classList.contains('section-empty')) {
                        return;
                    }
                    sectionList.insertBefore(row, previous);
                    builderDirty = true;
                });

                row.querySelector('.section-down').addEventListener('click', function () {
                    var next = row.nextElementSibling;
                    if (!next) {
                        return;
                    }
                    sectionList.insertBefore(next, row);
                    builderDirty = true;
                });

                var markDirty = function () {
                    builderDirty = true;
                };
                wrapperSelect.addEventListener('change', markDirty);
                classSelect.addEventListener('change', markDirty);
                customClassInput.addEventListener('input', markDirty);
                kindSelect.addEventListener('change', markDirty);
                textArea.addEventListener('input', markDirty);
                htmlArea.addEventListener('input', markDirty);
                imageSrc.addEventListener('input', markDirty);
                imageAlt.addEventListener('input', markDirty);
                imageStyle.addEventListener('input', markDirty);

                renderSectionFieldState(row);
                return row;
            }

            function rowToHtml(row) {
                var kind = row.querySelector('.section-kind').value;
                var wrapper = row.querySelector('.section-wrapper').value;
                var className = getClassFromRow(row);
                var body = '';

                if (kind === 'text') {
                    var textValue = row.querySelector('.section-text').value;
                    if (!textValue.trim()) {
                        return '';
                    }
                    body = escapeHtml(textValue).replace(/\r\n|\r|\n/g, '<br>');
                } else if (kind === 'html') {
                    var htmlValue = row.querySelector('.section-html').value;
                    if (!htmlValue.trim()) {
                        return '';
                    }
                    body = htmlValue.trim();
                } else if (kind === 'image') {
                    var srcValue = row.querySelector('.section-image-src').value.trim();
                    if (!srcValue) {
                        return '';
                    }
                    var altValue = row.querySelector('.section-image-alt').value.trim();
                    var styleValue = row.querySelector('.section-image-style').value.trim();
                    body = '<img src="' + escapeAttr(srcValue) + '" alt="' + escapeAttr(altValue) + '"';
                    if (styleValue) {
                        body += ' style="' + escapeAttr(styleValue) + '"';
                    }
                    body += '>';
                }

                if (wrapper === 'raw') {
                    return body;
                }

                if (className) {
                    return '<div class="' + escapeAttr(className) + '">' + body + '</div>';
                }

                return '<div>' + body + '</div>';
            }

            function collectSectionsHtml() {
                var rows = sectionList.querySelectorAll('.section-item');
                var output = [];
                for (var i = 0; i < rows.length; i++) {
                    var html = rowToHtml(rows[i]);
                    if (html) {
                        output.push(html);
                    }
                }
                return output.join('\n\n');
            }

            function parseContentToRows(rawHtml) {
                sectionList.innerHTML = '';
                var html = String(rawHtml || '').trim();
                if (!html) {
                    updateEmptyState();
                    return 0;
                }

                var parser = new DOMParser();
                var doc = parser.parseFromString('<div id="alphabit-section-root">' + html + '</div>', 'text/html');
                var root = doc.getElementById('alphabit-section-root');
                if (!root) {
                    updateEmptyState();
                    return 0;
                }

                var count = 0;
                for (var i = 0; i < root.childNodes.length; i++) {
                    var node = root.childNodes[i];
                    if (node.nodeType === Node.TEXT_NODE) {
                        var textNodeValue = node.textContent;
                        if (textNodeValue && textNodeValue.trim()) {
                            sectionList.appendChild(createSectionRow({
                                wrapper: 'raw',
                                kind: 'text',
                                text: textNodeValue.trim()
                            }));
                            count++;
                        }
                        continue;
                    }

                    if (node.nodeType !== Node.ELEMENT_NODE) {
                        continue;
                    }

                    var element = node;
                    var tagName = element.tagName.toLowerCase();
                    var wrapper = tagName === 'div' ? 'div' : 'raw';
                    var className = wrapper === 'div' ? normalizeClassName(element.getAttribute('class') || '') : '';

                    var isImageOnly = false;
                    var imageNode = null;
                    if (tagName === 'img') {
                        isImageOnly = true;
                        imageNode = element;
                    } else if (tagName === 'div' && element.children.length === 1 && element.firstElementChild.tagName.toLowerCase() === 'img' && element.textContent.trim() === '') {
                        isImageOnly = true;
                        imageNode = element.firstElementChild;
                    }

                    if (isImageOnly && imageNode) {
                        sectionList.appendChild(createSectionRow({
                            wrapper: wrapper,
                            className: className,
                            kind: 'image',
                            imageSrc: imageNode.getAttribute('src') || '',
                            imageAlt: imageNode.getAttribute('alt') || '',
                            imageStyle: imageNode.getAttribute('style') || ''
                        }));
                        count++;
                        continue;
                    }

                    if (tagName === 'div') {
                        sectionList.appendChild(createSectionRow({
                            wrapper: 'div',
                            className: className,
                            kind: 'html',
                            html: element.innerHTML.trim()
                        }));
                        count++;
                        continue;
                    }

                    sectionList.appendChild(createSectionRow({
                        wrapper: 'raw',
                        kind: 'html',
                        html: element.outerHTML.trim()
                    }));
                    count++;
                }

                if (count === 0) {
                    updateEmptyState();
                }

                return count;
            }

            function addEmptySection() {
                var placeholder = sectionList.querySelector('.section-empty');
                if (placeholder) {
                    placeholder.remove();
                }
                sectionList.appendChild(createSectionRow({
                    wrapper: 'div',
                    className: 'stext',
                    kind: 'text',
                    text: ''
                }));
            }

            function applySectionsToTarget(replaceTarget, allowEmpty) {
                var targetField = getTargetField();
                var sectionsHtml = collectSectionsHtml();
                if (!sectionsHtml.trim()) {
                    if (replaceTarget && allowEmpty) {
                        targetField.value = '';
                        builderDirty = false;
                        setStatus('Target content replaced with empty section output.', false);
                        return true;
                    }
                    setStatus('No valid section content to apply.', true);
                    return false;
                }

                if (replaceTarget) {
                    targetField.value = sectionsHtml;
                    builderDirty = false;
                    setStatus('Target content replaced with sections.', false);
                    return true;
                }

                var current = targetField.value.trim();
                targetField.value = current ? (current + '\n\n' + sectionsHtml) : sectionsHtml;
                builderDirty = false;
                setStatus('Sections appended to target content.', false);
                return true;
            }

            loadButton.addEventListener('click', function () {
                var sourceField = getTargetField();
                var parsedCount = parseContentToRows(sourceField.value);
                builderDirty = false;
                if (parsedCount > 0) {
                    setStatus('Loaded ' + parsedCount + ' section(s) from target content.', false);
                } else {
                    setStatus('Target content was empty, so no sections were loaded.', false);
                }
            });

            addButton.addEventListener('click', function () {
                addEmptySection();
                builderDirty = true;
                setStatus('Added a new empty section.', false);
            });

            appendButton.addEventListener('click', function () {
                applySectionsToTarget(false, false);
            });

            applyButton.addEventListener('click', function () {
                applySectionsToTarget(true, false);
            });

            document.addEventListener('alphabit:model-content-imported', function () {
                var sourceField = getTargetField();
                var parsedCount = parseContentToRows(sourceField.value);
                if (parsedCount > 0) {
                    setStatus('Imported content loaded into section builder.', false);
                }
            });

            var initialSource = '';
            if (contentEnField.value.trim()) {
                targetSelect.value = 'en';
                initialSource = contentEnField.value;
            } else if (contentRoField.value.trim()) {
                targetSelect.value = 'ro';
                initialSource = contentRoField.value;
            }
            if (initialSource) {
                var initialCount = parseContentToRows(initialSource);
                builderDirty = false;
                if (initialCount > 0) {
                    setStatus('Loaded existing content into section mode. Edit sections and apply to the target field.', false);
                } else {
                    addEmptySection();
                    builderDirty = false;
                    setStatus('Could not auto-parse existing content, but you can build sections manually.', true);
                }
            } else {
                updateEmptyState();
            }

            saveForm.addEventListener('submit', function () {
                if (!builderDirty) {
                    return;
                }

                applySectionsToTarget(true, true);
                setStatus('Section changes were auto-synced to the selected target field before save.', false);
            });
        })();
    </script>
    <?php include_once __DIR__ . '/../assets/includes/season_switcher.php'; ?>
</body>

</html>
