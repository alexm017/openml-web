<?php
session_start();


$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
if ($lang !== 'ro') {
    $lang = 'en';
}

$season_cookie = isset($_COOKIE['season_choice']) ? $_COOKIE['season_choice'] : 'Decode';
$season_path = ($season_cookie === 'Decode') ? 'decode' : 'intothedeep';
$is_logged_in = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === 'userLoggedIn';
$team_name = isset($_SESSION['teamname']) ? $_SESSION['teamname'] : '';
$profile_image_path = isset($_SESSION['profile_image_path']) ? (string) $_SESSION['profile_image_path'] : '/assets/images/user3.png';
if (preg_match('#^/assets/uploads/team_profiles/[a-zA-Z0-9._-]+$#', $profile_image_path) !== 1) {
    $profile_image_path = '/assets/images/user3.png';
}

$text = [
    'en' => [
        'title' => 'Training Datasets',
        'subtitle' => 'Choose a dataset, review its preview section, and download the package.',
        'preview' => 'Preview image',
        'preview_hint' => '',
        'download' => 'Download Dataset',
        'training_data' => 'Training Data',
        'ml_model' => 'ML Model',
        'online_training' => 'Online Training ML',
        'signup' => 'Sign Up',
        'login' => 'Login',
        'hello' => 'Hello',
        'note' => 'Note',
    ],
    'ro' => [
        'title' => 'Training Datasets',
        'subtitle' => 'Alege un set de date, vezi zona de preview și descarcă pachetul.',
        'preview' => 'Preview image',
        'preview_hint' => '',
        'download' => 'Descarca Setul de Date',
        'training_data' => 'Date de Antrenament',
        'ml_model' => 'Model ML',
        'online_training' => 'Antrenare ML Online',
        'signup' => 'Inregistrare',
        'login' => 'Autentificare',
        'hello' => 'Salut',
        'note' => 'Note',
    ],
];

$datasets = [
    [
        'title_en' => 'Dataset 01 - Samples (Medium)',
        'title_ro' => 'Dataset 01 - Samples (Mediu)',
        'desc_en' => 'Balanced starter set for initial model training and quick iteration.',
        'desc_ro' => 'Set echilibrat pentru antrenare initiala si iteratii rapide.',
        'preview' => '/assets/ai/combined.jpeg',
        'download' => '/assets/ai/medium_dataset.rar',
    ],
    [
        'title_en' => 'Dataset 02 - Samples (Large)',
        'title_ro' => 'Dataset 02 - Samples (Extins)',
        'desc_en' => 'Large dataset with more diversity for stronger generalization.',
        'desc_ro' => 'Set mare de date cu diversitate mai mare pentru generalizare mai buna.',
        'preview' => '/assets/ai/combined_large.jpeg',
        'download' => '/assets/ai/large_dataset.rar',
    ],
    [
        'title_en' => 'Dataset 03 - Artifacts (Medium)',
        'title_ro' => 'Dataset 03 - Artifacts (Mediu)',
        'desc_en' => 'Focused split for difficult lighting conditions and color consistency.',
        'desc_ro' => 'Set dedicat conditiilor de lumina dificile si consistentei culorilor.',
        'download' => '/assets/ai/intothedeep_dataset_03.rar',
    ],
    [
        'title_en' => 'Dataset 04 - Artifacts (Large)',
        'title_ro' => 'Dataset 04 - Artifacts (Extins)',
        'desc_en' => 'Reserved validation set for objective model checks before deployment.',
        'desc_ro' => 'Set rezervat pentru validare obiectiva inainte de deploy.',
        'download' => '/assets/ai/intothedeep_dataset_04.rar',
    ],
];

$t = $text[$lang];
$current_year = date('Y');
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8'); ?> - AlphaBit OpenML</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/images/alphabit.ico">
    <style>
        :root {
            --font-main: 'Manrope', 'Montserrat', sans-serif;
            --ink-900: #f5f5f4;
            --ink-700: #d0d0cc;
            --ink-600: #a3a39d;
            --accent: #f5f5f4;
            --accent-2: #ebebe8;
            --surface: rgba(12, 12, 12, 0.9);
            --border: rgba(255, 255, 255, 0.14);
            --control-bg: #f5f5f4;
            --control-bg-hover: #ffffff;
            --control-border: rgba(0, 0, 0, 0.16);
            --control-ink: #0a0a0a;
            --shadow-lg: 0 24px 54px rgba(0, 0, 0, 0.5);
            --shadow-md: 0 14px 34px rgba(0, 0, 0, 0.4);
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

        body::before,
        body::after {
            content: '';
            position: fixed;
            border-radius: 999px;
            pointer-events: none;
            z-index: -1;
        }

        body::before {
            width: 30rem;
            height: 30rem;
            top: -10rem;
            right: -10rem;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0));
        }

        body::after {
            width: 24rem;
            height: 24rem;
            bottom: -8rem;
            left: -8rem;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0));
        }

        .site-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 90;
            width: 100%;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
            align-items: center;
            gap: 1rem;
            padding: 0.78rem clamp(1rem, 3vw, 3.5rem);
            background: rgba(8, 8, 8, 0.84);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: var(--shadow-md);
        }

        .brand-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            justify-self: start;
            text-decoration: none;
            font-weight: 800;
            letter-spacing: 0.01em;
            font-size: clamp(1.05rem, 1.3vw, 1.25rem);
            color: inherit;
        }

        .brand-logo {
            width: clamp(1.85rem, 3vw, 2.25rem);
            height: clamp(1.85rem, 3vw, 2.25rem);
            object-fit: contain;
            transform: translateY(4px);
        }

        .navbar-links,
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .navbar-links {
            justify-self: center;
            justify-content: center;
        }

        .navbar-actions {
            justify-self: end;
            justify-content: flex-end;
            margin-right: 1.1rem;
        }

        .nav-link,
        .profile-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 0.52rem 0.95rem;
            border-radius: 999px;
            border: 1px solid var(--control-border);
            color: var(--control-ink);
            background: var(--control-bg);
            font-weight: 650;
            font-size: 0.92rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .nav-link:hover,
        .profile-chip:hover,
        .download-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.34);
            background: var(--control-bg-hover);
        }

        .profile-chip {
            gap: 0.45rem;
        }

        .profile-chip img {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 999px;
        }

        .training-page {
            width: min(1180px, 100%);
            margin: 0 auto;
            padding: 6.6rem 1rem 2rem;
        }

        .page-intro {
            border-radius: 1.2rem;
            border: 1px solid var(--border);
            background: var(--surface);
            box-shadow: var(--shadow-lg);
            padding: 1.3rem;
            margin-bottom: 1rem;
        }

        .page-intro h1 {
            margin: 0;
            font-size: clamp(1.6rem, 3vw, 2.1rem);
        }

        .page-intro p {
            margin: 0.5rem 0 0;
            color: var(--ink-700);
        }

        .dataset-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .dataset-card {
            border-radius: 1.1rem;
            border: 1px solid var(--border);
            background: var(--surface);
            box-shadow: var(--shadow-md);
            padding: 1rem;
            display: grid;
            gap: 0.8rem;
        }

        .dataset-card h2 {
            margin: 0;
            font-size: 1.12rem;
        }

        .dataset-card p {
            margin: 0;
            color: var(--ink-700);
            font-size: 0.95rem;
        }

        .preview-slot {
            min-height: 180px;
            border-radius: 0.9rem;
            border: 1px dashed rgba(255, 255, 255, 0.3);
            background: rgba(8, 8, 8, 0.92);
            display: grid;
            place-items: center;
            text-align: center;
            padding: 1rem;
            color: var(--ink-600);
            line-height: 1.4;
        }

        .preview-image {
            display: block;
            width: 100%;
            height: 100%;
            min-height: 180px;
            border-radius: 0.7rem;
            object-fit: cover;
        }

        .download-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            border: 1px solid rgba(0, 0, 0, 0.18);
            border-radius: 999px;
            padding: 0.72rem 1.1rem;
            color: var(--control-ink);
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .page-note {
            margin-top: 1rem;
            color: var(--ink-600);
            font-size: 0.92rem;
            text-align: center;
        }

        .site-footer {
            margin-top: 1rem;
            display: flex;
            justify-content: center;
            color: var(--ink-600);
            font-size: 0.9rem;
        }

        @media (max-width: 900px) {
            .dataset-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 780px) {
            .site-navbar {
                display: flex;
                flex-direction: column;
                align-items: stretch;
            }

            .brand-link,
            .navbar-links,
            .navbar-actions {
                justify-content: center;
            }

            .navbar-actions {
                margin-right: 0;
            }

            .navbar-links {
                width: 100%;
            }
        }

        @media (max-width: 560px) {

            .nav-link,
            .profile-chip {
                width: 100%;
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

        <nav class="navbar-links" aria-label="Primary">
            <a class="nav-link"
                href="/model/<?php echo htmlspecialchars($season_path, ENT_QUOTES, 'UTF-8'); ?>/training"><?php echo htmlspecialchars($t['training_data'], ENT_QUOTES, 'UTF-8'); ?></a>
            <a class="nav-link"
                href="/model/<?php echo htmlspecialchars($season_path, ENT_QUOTES, 'UTF-8'); ?>/overview"><?php echo htmlspecialchars($t['ml_model'], ENT_QUOTES, 'UTF-8'); ?></a>
            <a class="nav-link"
                href="/model/<?php echo htmlspecialchars($season_path, ENT_QUOTES, 'UTF-8'); ?>/online_training_ml"><?php echo htmlspecialchars($t['online_training'], ENT_QUOTES, 'UTF-8'); ?></a>
        </nav>

        <div class="navbar-actions">
            <?php if (!$is_logged_in): ?>
                <a class="nav-link" href="/register"><?php echo htmlspecialchars($t['signup'], ENT_QUOTES, 'UTF-8'); ?></a>
                <a class="nav-link" href="/login"><?php echo htmlspecialchars($t['login'], ENT_QUOTES, 'UTF-8'); ?></a>
            <?php else: ?>
                <a class="profile-chip" href="/profile">
                    <img src="<?php echo htmlspecialchars($profile_image_path, ENT_QUOTES, 'UTF-8'); ?>" alt="Profile">
                    <span><?php echo htmlspecialchars($t['hello'] . ', ' . $team_name, ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <main class="training-page">
        <section class="page-intro">
            <h1><?php echo htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <p><?php echo htmlspecialchars($t['subtitle'], ENT_QUOTES, 'UTF-8'); ?></p>
        </section>

        <section class="dataset-grid" aria-label="IntoTheDeep datasets">
            <?php foreach ($datasets as $index => $dataset): ?>
                <article class="dataset-card">
                    <h2><?php echo htmlspecialchars($lang === 'ro' ? $dataset['title_ro'] : $dataset['title_en'], ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                    <p><?php echo htmlspecialchars($lang === 'ro' ? $dataset['desc_ro'] : $dataset['desc_en'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <div class="preview-slot">
                        <?php if (!empty($dataset['preview'])): ?>
                            <img class="preview-image"
                                src="<?php echo htmlspecialchars($dataset['preview'], ENT_QUOTES, 'UTF-8'); ?>"
                                alt="<?php echo htmlspecialchars(($lang === 'ro' ? $dataset['title_ro'] : $dataset['title_en']) . ' preview', ENT_QUOTES, 'UTF-8'); ?>">
                        <?php else: ?>
                            <div>
                                <strong><?php echo htmlspecialchars($t['preview'], ENT_QUOTES, 'UTF-8'); ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a class="download-btn"
                        href="<?php echo htmlspecialchars($dataset['download'], ENT_QUOTES, 'UTF-8'); ?>" download>
                        <?php echo htmlspecialchars($t['download'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </article>
            <?php endforeach; ?>
        </section>

        <footer class="site-footer"><?php echo $current_year; ?> AlphaBit OpenML</footer>
    </main>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/includes/chat_widget.php'; ?>
</body>

</html>
