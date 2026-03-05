<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$pageRecord = isset($pageRecord) ? trim((string) $pageRecord) : 'decode-doc';
$pageTitleEn = isset($pageTitleEn) ? trim((string) $pageTitleEn) : 'Decode Documentation';
$pageTitleRo = isset($pageTitleRo) ? trim((string) $pageTitleRo) : '';
$contentEn = isset($contentEn) ? trim((string) $contentEn) : '';
$contentRo = isset($contentRo) ? trim((string) $contentRo) : '';
$activePage = isset($activePage) ? trim((string) $activePage) : '';

$recordFile = @fopen('/var/www/html/record_index.txt', 'a');
if ($recordFile) {
    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : 'unknown-agent';
    $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown-ip';
    $date = date('m/d/Y h:i:s a', time());
    fwrite($recordFile, $pageRecord . "\n");
    fwrite($recordFile, $pageRecord . ' ' . $userAgent . ' ' . $ip . ' ' . $date . "\n");
    fclose($recordFile);
}

$lang = isset($_COOKIE['site_lang']) ? (string) $_COOKIE['site_lang'] : 'en';
if ($lang !== 'ro') {
    $lang = 'en';
}

$seasonYear = '2026';
$seasonPath = 'decode';

$pageTitle = ($lang === 'ro' && $pageTitleRo !== '') ? $pageTitleRo : $pageTitleEn;
$pageContent = ($lang === 'ro' && $contentRo !== '') ? $contentRo : $contentEn;

$label = [
    'en' => [
        'setup' => 'Setup',
        'overview' => 'Overview',
        'prerequisites' => 'Getting Started',
        'resources' => 'Resources',
        'apriltag' => 'AprilTag Detection',
        'apriltag_start' => 'Getting Started',
        'apriltag_impl' => 'AprilTag Implementation',
        'autonomous' => 'Autonomous Control',
        'autonomous_start' => 'Getting Started',
        'odometry' => 'Odometry Pods',
        'rr056' => 'Road Runner 0.5.6 Implementation',
        'rr10' => 'Road Runner 1.0 Implementation',
        'pedro' => 'Pedro Pathing Implementation',
        'autoaim' => 'Auto Aiming Turret',
        'autoaim_start' => 'Getting Started',
        'imu_only' => 'IMU Only Implementation',
        'webcam_only' => 'Webcam Only Implementation',
        'imu_webcam' => 'IMU & Webcam Implementation',
    ],
    'ro' => [
        'setup' => 'Configurare',
        'overview' => 'Prezentare Generală',
        'prerequisites' => 'Initializare Device',
        'resources' => 'Resurse',
        'apriltag' => 'Detectie AprilTag',
        'apriltag_start' => 'Ghid de initializare',
        'apriltag_impl' => 'Implementare AprilTag',
        'autonomous' => 'Control Autonom',
        'autonomous_start' => 'Ghid de initializare',
        'odometry' => 'Odometrie',
        'rr056' => 'Implementare Road Runner 0.5.6',
        'rr10' => 'Implementare Road Runner 1.0',
        'pedro' => 'Implementare Pedro Pathing',
        'autoaim' => 'Turela de Ochire Automată',
        'autoaim_start' => 'Ghid de initializare',
        'imu_only' => 'Implementare Doar IMU',
        'webcam_only' => 'Implementare Doar Webcam',
        'imu_webcam' => 'Implementare IMU & Webcam',
    ],
];

$t = $label[$lang];

$renderNavItem = static function (string $id, string $href, string $text) use ($activePage): string {
    $safeHref = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
    $safeText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    if ($id === $activePage) {
        return '<div class="sub-section"><p style="color:#c67171;">' . $safeText . '</p></div>';
    }
    return '<div class="sub-section"><a href="' . $safeHref . '">' . $safeText . '</a></div>';
};
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlphaBit - OpenML</title>
    <link rel="stylesheet" href="/assets/css/model_style.css?v=20260305">
    <link rel="stylesheet" href="/assets/css/overview_theme.css?v=20260305">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/images/alphabit.ico" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/styles/atom-one-dark.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/highlight.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.hljs && hljs.highlightAll) {
                hljs.highlightAll();
            }
        });
    </script>
</head>

<body>
    <div id="language-popup" class="language-popup-overlay" style="display: none;">
        <div class="language-popup-content">
            <h2>Choose Language / Alege Limba</h2>
            <div class="language-options">
                <button onclick="selectLanguage('ro')">Română</button>
                <button onclick="selectLanguage('en')">English</button>
            </div>
        </div>
    </div>

    <style>
        .language-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .language-popup-content {
            background-color: #1e1e1e;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid #333;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }

        .language-popup-content h2 {
            color: #fff;
            margin-bottom: 35px;
            font-family: Arial, sans-serif;
        }

        .language-options {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .language-options button {
            padding: 15px 30px;
            font-size: 18px;
            cursor: pointer;
            background-color: #d4d4d4ff;
            color: black;
            border: none;
            border-radius: 8px;
        }

        .language-options button:hover {
            background-color: #ffffffff;
            transform: scale(1.05);
        }
    </style>

    <script>
        function setCookie(name, value, days) {
            var expires = '';
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = name + '=' + (value || '') + expires + '; path=/';
        }

        function getCookie(name) {
            var nameEQ = name + '=';
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ') {
                    c = c.substring(1, c.length);
                }
                if (c.indexOf(nameEQ) === 0) {
                    return c.substring(nameEQ.length, c.length);
                }
            }
            return null;
        }

        function selectLanguage(lang) {
            setCookie('site_lang', lang, 365);
            document.getElementById('language-popup').style.display = 'none';
            window.location.reload();
        }

        document.addEventListener('DOMContentLoaded', function () {
            var selectedLang = getCookie('site_lang');
            if (!selectedLang) {
                document.getElementById('language-popup').style.display = 'flex';
            }
        });
    </script>

    <div class="background-container">
        <div class="alphabit-topleft">
            <a href="/">AlphaBit OpenML</a>
        </div>
        <div class="before_docs"><?php echo htmlspecialchars($seasonYear, ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="ai-star-logo">
            <img src="/assets/images/ai_star_alpha.png" width="50" alt="AlphaBit star">
        </div>
        <div class="docs">Documentation</div>
        <div class="rbox">
            <div class="title"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="text-container">
                <?php echo $pageContent; ?>
                <div class="endLine"></div>
                <div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -&gt; Discord</a></div>
                <div class="end"></div>
            </div>
        </div>
        <div class="docs-container">
            <div class="setup"><?php echo htmlspecialchars($t['setup'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php echo $renderNavItem('overview', '/model/' . $seasonPath . '/overview', $t['overview']); ?>
            <?php echo $renderNavItem('prerequisites', '/model/' . $seasonPath . '/prerequisites', $t['prerequisites']); ?>
            <?php echo $renderNavItem('resources', '/model/' . $seasonPath . '/resources', $t['resources']); ?>
            <div class="docsLine"></div>

            <div class="setup"><?php echo htmlspecialchars($t['apriltag'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php echo $renderNavItem('apriltag_start', '/model/' . $seasonPath . '/apriltag', $t['apriltag_start']); ?>
            <?php echo $renderNavItem('apriltag_impl', '/model/' . $seasonPath . '/apriltag_code_sample', $t['apriltag_impl']); ?>
            <div class="docsLine"></div>

            <div class="setup"><?php echo htmlspecialchars($t['autonomous'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php echo $renderNavItem('autonomous_start', '/model/' . $seasonPath . '/autonomous', $t['autonomous_start']); ?>
            <?php echo $renderNavItem('odometry', '/model/' . $seasonPath . '/odometry', $t['odometry']); ?>
            <?php echo $renderNavItem('rr056', '/model/' . $seasonPath . '/road_runner_056', $t['rr056']); ?>
            <?php echo $renderNavItem('rr10', '/model/' . $seasonPath . '/road_runner_10', $t['rr10']); ?>
            <?php echo $renderNavItem('pedro', '/model/' . $seasonPath . '/pedro_pathing', $t['pedro']); ?>
            <div class="docsLine"></div>

            <div class="setup"><?php echo htmlspecialchars($t['autoaim'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php echo $renderNavItem('autoaim_start', '/model/' . $seasonPath . '/auto_aiming_getting_started', $t['autoaim_start']); ?>
            <?php echo $renderNavItem('imu_only', '/model/' . $seasonPath . '/gyroscope_only', $t['imu_only']); ?>
            <?php echo $renderNavItem('webcam_only', '/model/' . $seasonPath . '/camera_only', $t['webcam_only']); ?>
            <?php echo $renderNavItem('imu_webcam', '/model/' . $seasonPath . '/gyroscope_and_camera', $t['imu_webcam']); ?>
        </div>
    </div>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/includes/chat_widget.php'; ?>
</body>

</html>
