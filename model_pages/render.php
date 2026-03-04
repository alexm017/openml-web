<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../assets/includes/model_pages_store.php';
require_once __DIR__ . '/../assets/includes/model_routes.php';

$record_file = @fopen('/var/www/html/record_index.txt', 'a');
if ($record_file) {
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown-agent';
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown-ip';
    $date = date('m/d/Y h:i:s a');
    fwrite($record_file, "custom-model-page\n");
    fwrite($record_file, 'custom-model-page ' . $user_agent . ' ' . $ip . ' ' . $date . "\n");
    fclose($record_file);
}

$season = strtolower(trim((string) ($_GET['season'] ?? '')));
$slug = alphabit_model_pages_slugify((string) ($_GET['slug'] ?? ''));

if (!alphabit_model_pages_is_valid_season($season) || $slug === '') {
    http_response_code(404);
    echo 'Page not found.';
    exit;
}

$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
if ($lang !== 'ro') {
    $lang = 'en';
}

$pages = alphabit_model_pages_load_all();
$page = alphabit_model_pages_find($pages, $season, $slug, true);

if (!is_array($page)) {
    http_response_code(404);
    $page = [
        'title_en' => 'Page not found',
        'title_ro' => 'Pagina nu a fost gasita',
        'content_en' => '<p>This page does not exist or is currently inactive.</p>',
        'content_ro' => '<p>Aceasta pagina nu exista sau este inactiva momentan.</p>',
    ];
}

$season_year = ($season === 'decode') ? '2026' : '2025';
$season_label = ($season === 'decode') ? 'Decode' : 'IntoTheDeep';

$titleEn = trim((string) ($page['title_en'] ?? 'Custom Page'));
$titleRo = trim((string) ($page['title_ro'] ?? ''));
$title = ($lang === 'ro' && $titleRo !== '') ? $titleRo : $titleEn;

$contentEn = trim((string) ($page['content_en'] ?? ''));
$contentRo = trim((string) ($page['content_ro'] ?? ''));
$selectedContent = ($lang === 'ro' && $contentRo !== '') ? $contentRo : $contentEn;
$contentHtml = alphabit_model_pages_sanitize_html($selectedContent);

if ($contentHtml === '') {
    $contentHtml = ($lang === 'ro')
        ? '<p>Pagina este creata, dar nu are continut inca.</p>'
        : '<p>This page is created, but content has not been added yet.</p>';
}

$sidebarText = [
    'en' => [
        'setup' => 'Setup',
        'training' => 'Training ML',
        'examples' => 'Examples',
        'decode_april' => 'AprilTag Detection',
        'decode_auto' => 'Autonomous Control',
        'decode_aim' => 'Auto Artifact Pick-up (beta)',
        'custom' => 'Custom Page',
    ],
    'ro' => [
        'setup' => 'Configurare',
        'training' => 'Antrenare ML',
        'examples' => 'Exemple',
        'decode_april' => 'Detectie AprilTag',
        'decode_auto' => 'Control Autonom',
        'decode_aim' => 'Auto Artifact Pick-up (beta)',
        'custom' => 'Pagina Custom',
    ],
];
$sx = $sidebarText[$lang];

$setupLinks = [
    ['label_en' => 'Overview', 'label_ro' => 'Prezentare Generala', 'path' => '/model/' . $season . '/overview'],
    ['label_en' => 'Getting Started', 'label_ro' => 'Initializare Device', 'path' => '/model/' . $season . '/prerequisites'],
    ['label_en' => 'Resources', 'label_ro' => 'Resurse', 'path' => '/model/' . $season . '/resources'],
];

$trainingLinks = [
    ['label_en' => 'Training Dataset', 'label_ro' => 'Set de Date Antrenament', 'path' => '/model/' . $season . '/training'],
    ['label_en' => 'Training Structure', 'label_ro' => 'Structura Antrenare', 'path' => '/model/' . $season . '/training_structure'],
    ['label_en' => 'Label Images Tool', 'label_ro' => 'LabelImg Etichetare', 'path' => '/model/' . $season . '/label_tool'],
    ['label_en' => 'Python Code For Training', 'label_ro' => 'Cod Python Antrenare', 'path' => '/model/' . $season . '/training_ml'],
    ['label_en' => 'Online Training ML', 'label_ro' => 'Antrenare ML Online', 'path' => '/model/' . $season . '/online_training_ml'],
];

$exampleLinks = [
    ['label_en' => 'Python Code For Detection', 'label_ro' => 'Cod Python pentru Detectie', 'path' => '/model/' . $season . '/pythonml'],
    ['label_en' => 'Android Studio Implementation', 'label_ro' => 'Implementare Android Studio', 'path' => '/model/' . $season . '/android_studio'],
    ['label_en' => 'Robot Control', 'label_ro' => 'Control Colectare cu OpenML', 'path' => '/model/' . $season . '/robot_control'],
];

$decodeAprilLinks = [
    ['label' => 'Getting Started', 'path' => '/model/decode/apriltag'],
    ['label' => 'AprilTag Code Sample', 'path' => '/model/decode/apriltag_code_sample'],
];

$decodeAutoLinks = [
    ['label' => 'Getting Started', 'path' => '/model/decode/autonomous'],
    ['label' => 'Odometry Pods', 'path' => '/model/decode/odometry'],
    ['label' => 'Road Runner 0.5.6', 'path' => '/model/decode/road_runner_056'],
    ['label' => 'Road Runner 1.0', 'path' => '/model/decode/road_runner_10'],
    ['label' => 'Pedro Pathing', 'path' => '/model/decode/pedro_pathing'],
];

$decodeAimLinks = [
    ['label' => 'Getting Started', 'path' => '/model/decode/auto_aiming_getting_started'],
    ['label' => 'Gyroscope Only', 'path' => '/model/decode/gyroscope_only'],
    ['label' => 'Camera Only', 'path' => '/model/decode/camera_only'],
    ['label' => 'Gyroscope + Camera', 'path' => '/model/decode/gyroscope_and_camera'],
];

$builtinSlugs = [];
foreach (alphabit_model_builtin_for_season($season) as $route) {
    $routeSlug = (string) ($route['slug'] ?? '');
    if ($routeSlug !== '') {
        $builtinSlugs[$routeSlug] = true;
    }
}

$customSectionPages = [];
foreach ($pages as $entry) {
    if (!is_array($entry)) {
        continue;
    }

    $normalized = alphabit_model_pages_normalize_record($entry);
    if (($normalized['season'] ?? '') !== $season || empty($normalized['is_active'])) {
        continue;
    }

    $entrySlug = (string) ($normalized['slug'] ?? '');
    if ($entrySlug === '' || isset($builtinSlugs[$entrySlug])) {
        continue;
    }

    $entryTitle = (string) ($lang === 'ro' && trim((string) ($normalized['title_ro'] ?? '')) !== ''
        ? $normalized['title_ro']
        : $normalized['title_en']);
    if ($entryTitle === '') {
        $entryTitle = $entrySlug;
    }

    $customSectionPages[] = [
        'title' => $entryTitle,
        'url' => '/model/' . $season . '/' . $entrySlug,
        'active' => ($entrySlug === $slug),
    ];
}

usort($customSectionPages, static function (array $a, array $b): int {
    return strcmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? ''));
});
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?> - AlphaBit OpenML</title>
    <link rel="stylesheet" href="/assets/css/model_style.css?v=20260304">
    <link rel="stylesheet" href="/assets/css/overview_theme.css?v=20260304">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/images/alphabit.ico">
    <style>
        .custom-page-meta {
            color: #b8b8b8;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .custom-page-content {
            max-width: 930px;
            line-height: 1.7;
            color: #d3d3d3;
            font-size: 16px;
        }

        .custom-page-content h1,
        .custom-page-content h2,
        .custom-page-content h3,
        .custom-page-content h4,
        .custom-page-content h5,
        .custom-page-content h6 {
            color: #f5f5f4;
            margin-top: 22px;
            margin-bottom: 10px;
        }

        .custom-page-content a {
            color: #f5f5f4;
            text-underline-offset: 3px;
        }

        .custom-page-content ul,
        .custom-page-content ol {
            padding-left: 20px;
        }

        .custom-page-content img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            border: 1px solid #2a2a2a;
            margin: 12px 0;
        }

        .custom-page-content pre {
            background: #090909;
            border: 1px solid #2a2a2a;
            border-radius: 10px;
            padding: 12px;
            overflow: auto;
        }

        .custom-page-content code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
            font-size: 0.92em;
        }
    </style>
</head>

<body>
    <div class="background-container">
        <div class="alphabit-topleft">
            <a href="/">AlphaBit OpenML</a>
        </div>
        <div class="before_docs"><?php echo htmlspecialchars($season_year, ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="ai-star-logo">
            <img src="/assets/images/ai_star_alpha.png" width="50" alt="AlphaBit logo">
        </div>
        <div class="docs">Documentation</div>

        <div class="rbox">
            <div class="title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="text-container">
                <div class="custom-page-meta">Season: <?php echo htmlspecialchars($season_label, ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="custom-page-content"><?php echo $contentHtml; ?></div>
                <div class="endLine"></div>
                <div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
                <div class="end"></div>
            </div>
        </div>

        <div class="docs-container">
            <div class="setup"><?php echo htmlspecialchars($sx['setup'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php foreach ($setupLinks as $link): ?>
                <div class="sub-section">
                    <a href="<?php echo htmlspecialchars($link['path'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($lang === 'ro' ? $link['label_ro'] : $link['label_en'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </div>
            <?php endforeach; ?>
            <div class="docsLine"></div>

            <?php if ($season === 'intothedeep'): ?>
                <div class="setup"><?php echo htmlspecialchars($sx['training'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php foreach ($trainingLinks as $link): ?>
                    <div class="sub-section">
                        <a href="<?php echo htmlspecialchars($link['path'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($lang === 'ro' ? $link['label_ro'] : $link['label_en'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
                <div class="docsLine"></div>

                <div class="setup"><?php echo htmlspecialchars($sx['examples'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php foreach ($exampleLinks as $link): ?>
                    <div class="sub-section">
                        <a href="<?php echo htmlspecialchars($link['path'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($lang === 'ro' ? $link['label_ro'] : $link['label_en'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
                <div class="docsLine"></div>
            <?php else: ?>
                <div class="setup"><?php echo htmlspecialchars($sx['decode_april'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php foreach ($decodeAprilLinks as $link): ?>
                    <div class="sub-section">
                        <a href="<?php echo htmlspecialchars($link['path'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
                <div class="docsLine"></div>

                <div class="setup"><?php echo htmlspecialchars($sx['decode_auto'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php foreach ($decodeAutoLinks as $link): ?>
                    <div class="sub-section">
                        <a href="<?php echo htmlspecialchars($link['path'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
                <div class="docsLine"></div>

                <div class="setup"><?php echo htmlspecialchars($sx['decode_aim'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php foreach ($decodeAimLinks as $link): ?>
                    <div class="sub-section">
                        <a href="<?php echo htmlspecialchars($link['path'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
                <div class="docsLine"></div>
            <?php endif; ?>

            <?php if (count($customSectionPages) > 0): ?>
                <div class="docsLine"></div>
                <div class="setup"><?php echo htmlspecialchars($sx['custom'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php foreach ($customSectionPages as $customLink): ?>
                    <div class="sub-section">
                        <?php if (!empty($customLink['active'])): ?>
                            <p style="color:#c67171;"><?php echo htmlspecialchars((string) $customLink['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars((string) $customLink['url'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars((string) $customLink['title'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/includes/chat_widget.php'; ?>
</body>

</html>
