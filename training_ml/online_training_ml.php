<?php
session_start();

$record_file = @fopen('/var/www/html/record_index.txt', 'a');
if ($record_file) {
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown-agent';
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown-ip';
    $date = date('m/d/Y h:i:s a');
    fwrite($record_file, "online-training-ml\n");
    fwrite($record_file, 'online-training-ml ' . $user_agent . ' ' . $ip . ' ' . $date . "\n");
    fclose($record_file);
}

$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
if ($lang !== 'ro') {
    $lang = 'en';
}

$season_cookie = isset($_COOKIE['season_choice']) ? $_COOKIE['season_choice'] : 'Decode';
$season_path = ($season_cookie === 'Decode') ? 'decode' : 'intothedeep';
$season_year = ($season_cookie === 'Decode') ? '2026' : '2025';
$is_logged_in = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === 'userLoggedIn';
$team_name = isset($_SESSION['teamname']) ? $_SESSION['teamname'] : '';
$current_year = date('Y');

$text = [
    'en' => [
        'training_data' => 'Training Data',
        'ml_model' => 'ML Model',
        'online_training' => 'Online Training ML',
        'signup' => 'Sign Up',
        'login' => 'Login',
        'hello' => 'Hello',
        'title' => 'Online Training ML',
        'subtitle' => 'Run cloud training sessions for your OpenML model and export production-ready artifacts.',
        'season_label' => 'Season',
        'mode_label' => 'Mode',
        'mode_value' => 'Online Session',
        'what_title' => 'What This Page Is',
        'what_body' => 'This page is for operating online ML training jobs, not just reading a guide. Use it as your control center for planning runs, selecting training profiles, and deciding model outputs.',
        'profiles_title' => 'Training Profiles',
        'profile_fast' => 'Fast Validation: quick sanity run for dataset checks and baseline metrics.',
        'profile_balanced' => 'Balanced Production: stable run for most teams before competition day.',
        'profile_large' => 'Large Tuning: longer run with stronger optimization for best accuracy.',
        'workflow_title' => 'Online Training Workflow',
        'step_1' => 'Prepare and upload your dataset package from the Training Data section.',
        'step_2' => 'Choose your profile, target format (.pt / .tflite), and classes.',
        'step_3' => 'Launch the online run and monitor loss, mAP, and validation charts.',
        'step_4' => 'Download trained artifacts and deploy to your robot test pipeline.',
        'deliverables_title' => 'Expected Deliverables',
        'deliverable_1' => 'Trained model weights (.pt)',
        'deliverable_2' => 'Optimized deploy model (.tflite)',
        'deliverable_3' => 'Training metrics (loss, precision, recall, mAP)',
        'deliverable_4' => 'Inference quick-test recommendations',
        'cta_data' => 'Open Training Data',
        'cta_structure' => 'Open Training Structure',
        'cta_python' => 'Open Python Training Page',
        'support_note' => 'Need a custom online run or assistance? Use the contact form on the main page and our team will respond as quickly as possible.',
    ],
    'ro' => [
        'training_data' => 'Date de Antrenament',
        'ml_model' => 'Model ML',
        'online_training' => 'Antrenare ML Online',
        'signup' => 'Inregistrare',
        'login' => 'Autentificare',
        'hello' => 'Salut',
        'title' => 'Antrenare ML Online',
        'subtitle' => 'Ruleaza sesiuni de antrenare in cloud pentru modelul OpenML si exporta artefacte gata de productie.',
        'season_label' => 'Sezon',
        'mode_label' => 'Mod',
        'mode_value' => 'Sesiune Online',
        'what_title' => 'Rolul Acestei Pagini',
        'what_body' => 'Aceasta pagina este pentru rularea antrenarii ML online, nu doar pentru citirea unui ghid. O poti folosi pentru planificarea sesiunilor, alegerea profilului de antrenare si definirea output-urilor modelului.',
        'profiles_title' => 'Profiluri de Antrenare',
        'profile_fast' => 'Validare Rapida: rulare scurta pentru verificari de dataset si metrici de baza.',
        'profile_balanced' => 'Productie Echilibrata: rulare stabila pentru majoritatea echipelor inainte de competitie.',
        'profile_large' => 'Tuning Extins: rulare mai lunga cu optimizare mai puternica pentru acuratete maxima.',
        'workflow_title' => 'Workflow Antrenare Online',
        'step_1' => 'Pregateste si incarca pachetul de date din sectiunea Training Data.',
        'step_2' => 'Alege profilul, formatul tinta (.pt / .tflite) si clasele.',
        'step_3' => 'Porneste rularea online si monitorizeaza loss, mAP si graficele de validare.',
        'step_4' => 'Descarca artefactele antrenate si implementeaza-le in pipeline-ul robotului.',
        'deliverables_title' => 'Livrabile Estimate',
        'deliverable_1' => 'Greutati model antrenat (.pt)',
        'deliverable_2' => 'Model optimizat pentru deploy (.tflite)',
        'deliverable_3' => 'Metrici de antrenare (loss, precision, recall, mAP)',
        'deliverable_4' => 'Recomandari de testare rapida pentru inferenta',
        'cta_data' => 'Deschide Training Data',
        'cta_structure' => 'Deschide Structura Antrenare',
        'cta_python' => 'Deschide Pagina Python Training',
        'support_note' => 'Ai nevoie de o rulare online custom sau asistenta? Foloseste formularul de contact de pe pagina principala si echipa noastra va raspunde cat mai rapid.',
    ],
];

$t = $text[$lang];
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
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
        .cta:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.34);
            background: var(--control-bg-hover);
        }

        .nav-link-active {
            background: #ffffff;
        }

        .profile-chip {
            gap: 0.45rem;
        }

        .profile-chip img {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 999px;
        }

        .page-wrap {
            width: min(1180px, 100%);
            margin: 0 auto;
            padding: 6.6rem 1rem 2rem;
            display: grid;
            gap: 1rem;
        }

        .hero,
        .section {
            border-radius: 1.2rem;
            border: 1px solid var(--border);
            background: var(--surface);
            box-shadow: var(--shadow-lg);
            padding: 1.2rem;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(1.7rem, 3.4vw, 2.4rem);
        }

        .hero p {
            margin: 0.55rem 0 0;
            color: var(--ink-700);
        }

        .meta-row {
            margin-top: 0.95rem;
            display: flex;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .meta-chip {
            border: 1px solid rgba(255, 255, 255, 0.17);
            border-radius: 999px;
            padding: 0.4rem 0.72rem;
            font-size: 0.86rem;
            color: var(--ink-700);
        }

        .two-col {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .section h2 {
            margin: 0;
            font-size: 1.1rem;
        }

        .section p {
            margin: 0.55rem 0 0;
            color: var(--ink-700);
            line-height: 1.6;
        }

        .list {
            margin: 0.72rem 0 0;
            padding-left: 1rem;
            color: var(--ink-700);
            display: grid;
            gap: 0.45rem;
            line-height: 1.5;
        }

        .deliverables {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
            margin-top: 0.72rem;
        }

        .deliverable-chip {
            border: 1px solid rgba(255, 255, 255, 0.17);
            border-radius: 0.7rem;
            padding: 0.48rem 0.7rem;
            font-size: 0.9rem;
            color: var(--ink-700);
            background: rgba(10, 10, 10, 0.78);
        }

        .actions {
            margin-top: 0.9rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 0.62rem 1rem;
            border-radius: 999px;
            border: 1px solid rgba(0, 0, 0, 0.18);
            color: var(--control-ink);
            background: var(--control-bg);
            font-weight: 700;
            font-size: 0.9rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .support-note {
            margin-top: 0.75rem;
            color: var(--ink-600);
            font-size: 0.92rem;
        }

        .site-footer {
            margin-top: 0.1rem;
            display: flex;
            justify-content: center;
            color: var(--ink-600);
            font-size: 0.9rem;
        }

        @media (max-width: 900px) {
            .two-col {
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
            <a class="nav-link" href="/model/<?php echo htmlspecialchars($season_path, ENT_QUOTES, 'UTF-8'); ?>/training">
                <?php echo htmlspecialchars($t['training_data'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
            <a class="nav-link" href="/model/<?php echo htmlspecialchars($season_path, ENT_QUOTES, 'UTF-8'); ?>/overview">
                <?php echo htmlspecialchars($t['ml_model'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
            <a class="nav-link nav-link-active" href="/model/<?php echo htmlspecialchars($season_path, ENT_QUOTES, 'UTF-8'); ?>/online_training_ml">
                <?php echo htmlspecialchars($t['online_training'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </nav>

        <div class="navbar-actions">
            <?php if (!$is_logged_in): ?>
                <a class="nav-link" href="/register"><?php echo htmlspecialchars($t['signup'], ENT_QUOTES, 'UTF-8'); ?></a>
                <a class="nav-link" href="/login"><?php echo htmlspecialchars($t['login'], ENT_QUOTES, 'UTF-8'); ?></a>
            <?php else: ?>
                <a class="profile-chip" href="/profile">
                    <img src="/assets/images/user3.png" alt="Profile">
                    <span><?php echo htmlspecialchars($t['hello'] . ', ' . $team_name, ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <main class="page-wrap">
        <section class="hero">
            <h1><?php echo htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <p><?php echo htmlspecialchars($t['subtitle'], ENT_QUOTES, 'UTF-8'); ?></p>
            <div class="meta-row">
                <span class="meta-chip"><?php echo htmlspecialchars($t['season_label'] . ': ' . $season_year, ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="meta-chip"><?php echo htmlspecialchars($t['mode_label'] . ': ' . $t['mode_value'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </section>

        <section class="two-col">
            <article class="section">
                <h2><?php echo htmlspecialchars($t['what_title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($t['what_body'], ENT_QUOTES, 'UTF-8'); ?></p>
            </article>

            <article class="section">
                <h2><?php echo htmlspecialchars($t['profiles_title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <ul class="list">
                    <li><?php echo htmlspecialchars($t['profile_fast'], ENT_QUOTES, 'UTF-8'); ?></li>
                    <li><?php echo htmlspecialchars($t['profile_balanced'], ENT_QUOTES, 'UTF-8'); ?></li>
                    <li><?php echo htmlspecialchars($t['profile_large'], ENT_QUOTES, 'UTF-8'); ?></li>
                </ul>
            </article>
        </section>

        <section class="section">
            <h2><?php echo htmlspecialchars($t['workflow_title'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <ol class="list">
                <li><?php echo htmlspecialchars($t['step_1'], ENT_QUOTES, 'UTF-8'); ?></li>
                <li><?php echo htmlspecialchars($t['step_2'], ENT_QUOTES, 'UTF-8'); ?></li>
                <li><?php echo htmlspecialchars($t['step_3'], ENT_QUOTES, 'UTF-8'); ?></li>
                <li><?php echo htmlspecialchars($t['step_4'], ENT_QUOTES, 'UTF-8'); ?></li>
            </ol>
        </section>

        <section class="section">
            <h2><?php echo htmlspecialchars($t['deliverables_title'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <div class="deliverables">
                <span class="deliverable-chip"><?php echo htmlspecialchars($t['deliverable_1'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="deliverable-chip"><?php echo htmlspecialchars($t['deliverable_2'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="deliverable-chip"><?php echo htmlspecialchars($t['deliverable_3'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="deliverable-chip"><?php echo htmlspecialchars($t['deliverable_4'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>

            <div class="actions">
                <a class="cta" href="/model/<?php echo htmlspecialchars($season_path, ENT_QUOTES, 'UTF-8'); ?>/training">
                    <?php echo htmlspecialchars($t['cta_data'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
                <a class="cta" href="/model/<?php echo htmlspecialchars($season_path, ENT_QUOTES, 'UTF-8'); ?>/training_structure">
                    <?php echo htmlspecialchars($t['cta_structure'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
                <a class="cta" href="/model/<?php echo htmlspecialchars($season_path, ENT_QUOTES, 'UTF-8'); ?>/training_ml">
                    <?php echo htmlspecialchars($t['cta_python'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </div>

            <p class="support-note"><?php echo htmlspecialchars($t['support_note'], ENT_QUOTES, 'UTF-8'); ?></p>
        </section>

        <footer class="site-footer"><?php echo $current_year; ?> AlphaBit OpenML</footer>
    </main>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/includes/chat_widget.php'; ?>
</body>

</html>
