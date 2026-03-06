<?php
session_start();
require_once __DIR__ . '/assets/includes/admin_access.php';

$record_file = @fopen('/var/www/html/record_index.txt', 'a');
if ($record_file) {
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown-agent';
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown-ip';
    $date = date('m/d/Y h:i:s a');
    fwrite($record_file, "index\n");
    fwrite($record_file, 'index ' . $user_agent . ' ' . $ip . ' ' . $date . "\n");
    fclose($record_file);
}

$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
$season_cookie = isset($_COOKIE['season_choice']) ? $_COOKIE['season_choice'] : 'Decode';
$season_path = ($season_cookie === 'Decode') ? 'decode' : 'intothedeep';
$is_logged_in = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === 'userLoggedIn';
$team_name = isset($_SESSION['teamname']) ? $_SESSION['teamname'] : '';
$is_admin = $is_logged_in && alphabit_session_is_admin();
$current_year = date('Y');

$translations = [
    'en' => [
        'news' => 'New release: explore our open source ML model for object detection and autonomy.',
        'training_data' => 'Training Data',
        'ml_model' => 'ML Model',
        'online_training' => 'Online Training ML',
        'menu' => 'Menu',
        'admin_panel' => 'Admin Panel',
        'signup' => 'Sign Up',
        'login' => 'Login',
        'hello' => 'Hello,',
        'hero_kicker' => 'AlphaBit OpenML Platform',
        'hero_title' => 'Where Robotics Meets Machine Learning',
        'hero_description' => 'We build FTC-ready machine learning tools that detect game elements, estimate position and orientation, and support autonomous collection with high reliability.',
        'hero_cta' => 'Explore OpenML Platform',
        'scroll_label' => 'Scroll to learn more',
        'second_news' => 'Try our newest ML stack built for competition robotics.',
        'second_description' => 'Machine learning is not a buzzword for our team. It is a practical engineering layer integrated into the full robot workflow. We use it to detect game elements, pick up artifacts autonomously, and automate as many repeatable actions as possible so drivers can focus on match strategy.',
        'features_section_title' => 'Software Features on This Robot',
        'feature_sensor_title' => 'Sensor Fusion + Vision Reliability',
        'feature_sensor_text' => 'Odometry, IMU and camera tracking are fused for stable pose updates, with camera-based pose refresh to reduce drift during autonomous cycles.',
        'feature_artifact_title' => 'Automated Artifact Cycle',
        'feature_artifact_text' => 'Inverse kinematics auto-aim, autonomous artifact pickup, burst-shot sequencing and geofencing checks keep cycles fast, repeatable and safe in valid scoring zones.',
        'feature_control_title' => 'Driver-First, Fail-Operational Control',
        'feature_control_text' => 'Manual redundancy, safety interlocks and fast override paths keep the robot controllable under pressure even if a sensor pipeline degrades.',
        'limelight_note' => 'For the national stage we switched to Limelight (Raspberry Pi based) to benchmark it against our previous camera stack.',
        'image_info' => 'Our sample detection model averages over 90% accuracy with a robust dataset and iterative training workflow.',
        'robot_kicker' => 'FTC Decode 2026 Robot',
        'robot_title' => 'Built for Reliable Autonomous Scoring',
        'robot_description' => 'This platform robot combines mechanical consistency with an OpenML vision stack tuned for fast artifact detection and controlled shooting sequences. The result is a stable autonomous cycle under competition pressure.',
        'robot_stats_title' => 'Performance Snapshot',
        'robot_stat_detection_label' => 'Detection Accuracy',
        'robot_stat_detection_value' => '90%+',
        'robot_stat_latency_label' => 'Inference Pace',
        'robot_stat_latency_value' => 'Real-time',
        'robot_stat_validation_label' => 'Field Validation',
        'robot_stat_validation_value' => 'Match-tested',
        'robot_stat_season_label' => 'Competition Focus',
        'robot_stat_season_value' => 'Decode 2026',
        'contact_heading' => 'Contact Details',
        'name_label' => 'Name',
        'name_placeholder' => 'Your full name',
        'email_label' => 'E-mail Address',
        'email_placeholder' => 'you@example.com',
        'team_label' => 'Team Name',
        'team_placeholder' => 'Your FTC team',
        'message_label' => 'Message',
        'message_placeholder' => 'Tell us what you want to build.',
        'submit' => 'Send Message',
        'support_email_label' => 'E-mail:',
        'support_address_label' => 'Address:',
        'rights_reserved' => 'All rights reserved.',
        'image_one_alt' => 'Object detection preview',
        'image_two_alt' => 'Model output sample',
        'robot_image_alt' => 'FTC Decode 2026 robot',
        'robot_action_alt' => 'FTC robot shooting artifacts',
        'chat_bubble' => 'Need help with OpenML?',
        'chat_bubble_sub' => 'Ask the AlphaBit assistant',
        'chat_title' => 'AlphaBit AI Assistant',
        'chat_welcome' => 'Hi! I can help with model setup, training data, and robotics ML workflow questions.',
        'chat_placeholder' => 'Type your question...',
        'chat_send' => 'Send',
        'chat_error' => 'Sorry, something went wrong. Please try again.',
        'chat_offline' => 'Could not reach the assistant right now. Please check your connection.',
    ],
    'ro' => [
        'news' => 'Lansare nouă: explorează modelul nostru open source pentru detecție și autonomie.',
        'training_data' => 'Date de Antrenament',
        'ml_model' => 'Model ML',
        'online_training' => 'Antrenare ML Online',
        'menu' => 'Meniu',
        'admin_panel' => 'Panou Admin',
        'signup' => 'Înregistrare',
        'login' => 'Autentificare',
        'hello' => 'Salut,',
        'hero_kicker' => 'Platforma AlphaBit OpenML',
        'hero_title' => 'Unde Robotica Întâlnește Machine Learning-ul',
        'hero_description' => 'Construim soluții ML pentru FTC care detectează elemente de joc, estimează poziția și orientarea și susțin colectarea autonomă cu fiabilitate ridicată.',
        'hero_cta' => 'Obține Modelul OpenML',
        'scroll_label' => 'Derulează pentru detalii',
        'second_news' => 'Testează cel mai nou stack ML creat pentru robotica de competiție.',
        'second_description' => 'Machine learning-ul nu este un termen la modă pentru echipa noastră. Este un strat de inginerie practic, integrat în întregul workflow al robotului. Îl folosim pentru detectarea elementelor de joc, preluarea autonomă a artefactelor și automatizarea cât mai multor acțiuni repetitive, astfel încât driverii să se concentreze pe strategia de meci.',
        'features_section_title' => 'Funcții Software pe Acest Robot',
        'feature_sensor_title' => 'Sensor Fusion + Fiabilitate Vision',
        'feature_sensor_text' => 'Fuziunea dintre odometrie, IMU și cameră oferă actualizări stabile de poziție, iar auto pose refresh reduce drift-ul în ciclurile autonome.',
        'feature_artifact_title' => 'Ciclu Automatizat pentru Artefacte',
        'feature_artifact_text' => 'Auto-aim cu cinematică inversă, preluare autonomă a artefactelor, secvențe burst-shot și verificări geofencing mențin ciclurile rapide, repetitive și sigure în zone valide de scor.',
        'feature_control_title' => 'Control Driver-First, Fail-Operational',
        'feature_control_text' => 'Redundanța manuală, interlock-urile de siguranță și override-ul rapid mențin robotul controlabil chiar dacă o componentă de sensing se degradează.',
        'limelight_note' => 'Pentru etapa națională am trecut pe Limelight (bazat pe Raspberry Pi) pentru a-l compara cu stack-ul nostru anterior de cameră.',
        'image_info' => 'Modelul nostru de detectare a mostrelor depășește în medie 90% acuratețe datorită setului robust de date și antrenării iterative.',
        'robot_kicker' => 'Robot FTC Decode 2026',
        'robot_title' => 'Construit pentru punctaj autonom fiabil',
        'robot_description' => 'Robotul combină consistența mecanică cu un stack OpenML optimizat pentru detectarea rapidă a artefactelor și secvențe controlate de lansare. Rezultatul este un ciclu autonom stabil în condiții de competiție.',
        'robot_stats_title' => 'Statistici de Performanță',
        'robot_stat_detection_label' => 'Acuratețe Detecție',
        'robot_stat_detection_value' => '90%+',
        'robot_stat_latency_label' => 'Viteză Inferență',
        'robot_stat_latency_value' => 'În timp real',
        'robot_stat_validation_label' => 'Validare pe Teren',
        'robot_stat_validation_value' => 'Testat în meci',
        'robot_stat_season_label' => 'Focus Competițional',
        'robot_stat_season_value' => 'Decode 2026',
        'contact_heading' => 'Detalii Contact',
        'name_label' => 'Nume',
        'name_placeholder' => 'Numele tău complet',
        'email_label' => 'Adresă E-mail',
        'email_placeholder' => 'tu@exemplu.com',
        'team_label' => 'Nume Echipă',
        'team_placeholder' => 'Echipa ta FTC',
        'message_label' => 'Mesaj',
        'message_placeholder' => 'Spune-ne ce vrei să construiești.',
        'submit' => 'Trimite Mesajul',
        'support_email_label' => 'E-mail:',
        'support_address_label' => 'Adresă:',
        'rights_reserved' => 'Toate drepturile rezervate.',
        'image_one_alt' => 'Previzualizare detecție obiecte',
        'image_two_alt' => 'Exemplu rezultat model',
        'robot_image_alt' => 'Robot FTC Decode 2026',
        'robot_action_alt' => 'Robot FTC care lansează artefacte',
        'chat_bubble' => 'Ai nevoie de ajutor cu OpenML?',
        'chat_bubble_sub' => 'Intreaba asistentul AlphaBit',
        'chat_title' => 'Asistent AI AlphaBit',
        'chat_welcome' => 'Salut! Te pot ajuta cu setup-ul modelului, date de antrenament și întrebări despre workflow-ul ML în robotică.',
        'chat_placeholder' => 'Scrie întrebarea ta...',
        'chat_send' => 'Trimite',
        'chat_error' => 'A apărut o eroare. Te rog încearcă din nou.',
        'chat_offline' => 'Nu am putut contacta asistentul acum. Verifică conexiunea.',
    ],
];

if (!isset($translations[$lang])) {
    $lang = 'en';
}
$text = $translations[$lang];
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>AlphaBit - OpenML</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/home.css?v=20260306p">
    <link rel="stylesheet" href="assets/css/chat.css?v=20260306c">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/alphabit.ico">
</head>

<body>
    <div id="language-popup" class="language-popup-overlay" style="display: none;">
        <div class="language-popup-content">
            <h2>Choose Language / Alege Limba</h2>
            <div class="language-options">
                <button type="button" onclick="selectLanguage('ro')">Romanian</button>
                <button type="button" onclick="selectLanguage('en')">English</button>
            </div>
        </div>
    </div>

    <noscript>You need to enable JavaScript to run this website.</noscript>

    <section class="background-container">
        <header class="site-navbar" id="site-navbar">
            <a class="brand-link" href="/">
                <span>AlphaBit OpenML</span>
                <img class="brand-logo" src="assets/images/ai_star_alpha.png" alt="AlphaBit logo">
            </a>

            <button
                type="button"
                id="navbar-toggle"
                class="navbar-toggle"
                aria-label="<?php echo htmlspecialchars($text['menu'], ENT_QUOTES, 'UTF-8'); ?>"
                aria-expanded="false"
                aria-controls="navbar-mobile-panel">
                <span class="navbar-toggle-line" aria-hidden="true"></span>
                <span class="navbar-toggle-line" aria-hidden="true"></span>
                <span class="navbar-toggle-line" aria-hidden="true"></span>
            </button>

            <div class="navbar-mobile-panel" id="navbar-mobile-panel">
                <nav class="navbar-links" aria-label="Primary">
                    <a class="nav-link"
                        href="model/<?php echo $season_path; ?>/training"><?php echo htmlspecialchars($text['training_data'], ENT_QUOTES, 'UTF-8'); ?></a>
                    <a class="nav-link"
                        href="model/<?php echo $season_path; ?>/overview"><?php echo htmlspecialchars($text['ml_model'], ENT_QUOTES, 'UTF-8'); ?></a>
                    <a class="nav-link"
                        href="model/<?php echo $season_path; ?>/online_training_ml"><?php echo htmlspecialchars($text['online_training'], ENT_QUOTES, 'UTF-8'); ?></a>
                </nav>

                <div class="navbar-actions">
                    <?php if (!$is_logged_in): ?>
                        <a class="nav-link nav-link-soft"
                            href="/register"><?php echo htmlspecialchars($text['signup'], ENT_QUOTES, 'UTF-8'); ?></a>
                        <a class="nav-link nav-link-accent"
                            href="/login"><?php echo htmlspecialchars($text['login'], ENT_QUOTES, 'UTF-8'); ?></a>
                    <?php else: ?>
                        <?php if ($is_admin): ?>
                            <a class="nav-link nav-link-soft"
                                href="/admin/model-pages"><?php echo htmlspecialchars($text['admin_panel'], ENT_QUOTES, 'UTF-8'); ?></a>
                        <?php endif; ?>
                        <a class="profile-chip" href="/profile">
                            <img src="assets/images/user3.png" alt="Profile picture">
                            <span><?php echo htmlspecialchars($text['hello'] . ' ' . $team_name . '!', ENT_QUOTES, 'UTF-8'); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <p class="alphabit-news"><?php echo htmlspecialchars($text['news'], ENT_QUOTES, 'UTF-8'); ?></p>

        <div class="hero-content">
            <p class="alphabit-fwelcome"><?php echo htmlspecialchars($text['hero_kicker'], ENT_QUOTES, 'UTF-8'); ?></p>
            <h1 class="alphabit-swelcome"><?php echo htmlspecialchars($text['hero_title'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="alphabit-welcome-text">
                <?php echo htmlspecialchars($text['hero_description'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <a class="alphabit-learn"
                href="model/<?php echo $season_path; ?>/overview"><?php echo htmlspecialchars($text['hero_cta'], ENT_QUOTES, 'UTF-8'); ?>
                <span aria-hidden="true"></span></a>
        </div>

        <a class="scroll" href="#why-openml"
            aria-label="<?php echo htmlspecialchars($text['scroll_label'], ENT_QUOTES, 'UTF-8'); ?>"></a>
    </section>

    <section class="fpage" id="why-openml">
        <p class="fpage-news"><?php echo htmlspecialchars($text['second_news'], ENT_QUOTES, 'UTF-8'); ?></p>

        <div class="fpage-grid">
            <article class="ftext-box ftext-left robot-features-box">
                <p class="fpage-ftext"><?php echo htmlspecialchars($text['second_description'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <h3 class="feature-section-title"><?php echo htmlspecialchars($text['features_section_title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <div class="feature-groups">
                    <section class="feature-group">
                        <h4 class="feature-group-title"><?php echo htmlspecialchars($text['feature_sensor_title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p class="feature-group-text"><?php echo htmlspecialchars($text['feature_sensor_text'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </section>
                    <section class="feature-group">
                        <h4 class="feature-group-title"><?php echo htmlspecialchars($text['feature_artifact_title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p class="feature-group-text"><?php echo htmlspecialchars($text['feature_artifact_text'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </section>
                    <section class="feature-group">
                        <h4 class="feature-group-title"><?php echo htmlspecialchars($text['feature_control_title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p class="feature-group-text"><?php echo htmlspecialchars($text['feature_control_text'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </section>
                </div>
                <p class="limelight-note"><?php echo htmlspecialchars($text['limelight_note'], ENT_QUOTES, 'UTF-8'); ?></p>
            </article>

            <article class="fimage-box fimage-top robot-showcase">
                <figure class="robot-photo-frame">
                    <img class="fpage-fimage robot-image" src="assets/images/robot.png"
                        alt="<?php echo htmlspecialchars($text['robot_image_alt'], ENT_QUOTES, 'UTF-8'); ?>">
                </figure>
                <div class="robot-stats-wrap">
                    <p class="robot-stats-title"><?php echo htmlspecialchars($text['robot_stats_title'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="robot-stats-grid">
                        <div class="robot-stat">
                            <span class="robot-stat-label"><?php echo htmlspecialchars($text['robot_stat_detection_label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="robot-stat-value"><?php echo htmlspecialchars($text['robot_stat_detection_value'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="robot-stat">
                            <span class="robot-stat-label"><?php echo htmlspecialchars($text['robot_stat_latency_label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="robot-stat-value"><?php echo htmlspecialchars($text['robot_stat_latency_value'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="robot-stat">
                            <span class="robot-stat-label"><?php echo htmlspecialchars($text['robot_stat_validation_label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="robot-stat-value"><?php echo htmlspecialchars($text['robot_stat_validation_value'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="robot-stat">
                            <span class="robot-stat-label"><?php echo htmlspecialchars($text['robot_stat_season_label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="robot-stat-value"><?php echo htmlspecialchars($text['robot_stat_season_value'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>
                </div>
            </article>

            <figure class="simage-box fimage-bottom robot-action-box">
                <img class="fpage-simage robot-action-gif" src="assets/images/robot_shoot.gif"
                    alt="<?php echo htmlspecialchars($text['robot_action_alt'], ENT_QUOTES, 'UTF-8'); ?>">
            </figure>

            <article class="ftext-box ftext-right robot-about-box">
                <p class="robot-kicker"><?php echo htmlspecialchars($text['robot_kicker'], ENT_QUOTES, 'UTF-8'); ?></p>
                <h3 class="robot-title"><?php echo htmlspecialchars($text['robot_title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="fpage-ftext"><?php echo htmlspecialchars($text['robot_description'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="fimage-info"><?php echo htmlspecialchars($text['image_info'], ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
        </div>
        <div class="fpage-cta-wrap">
            <a class="fpage-learn"
                href="model/<?php echo $season_path; ?>/overview"><?php echo htmlspecialchars($text['hero_cta'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </div>
    </section>

    <section class="cpage" id="contact">
        <div class="contact-layout">
            <div class="contact-box">
                <h2 class="alphabit-contact-details">
                    <?php echo htmlspecialchars($text['contact_heading'], ENT_QUOTES, 'UTF-8'); ?>
                </h2>

                <form id="contact-form" action="index.php" method="post">
                    <label
                        for="contact-name"><?php echo htmlspecialchars($text['name_label'], ENT_QUOTES, 'UTF-8'); ?></label>
                    <input id="contact-name" type="text" name="name"
                        placeholder="<?php echo htmlspecialchars($text['name_placeholder'], ENT_QUOTES, 'UTF-8'); ?>"
                        autocomplete="name" required>

                    <label
                        for="contact-email"><?php echo htmlspecialchars($text['email_label'], ENT_QUOTES, 'UTF-8'); ?></label>
                    <input id="contact-email" type="email" name="email"
                        placeholder="<?php echo htmlspecialchars($text['email_placeholder'], ENT_QUOTES, 'UTF-8'); ?>"
                        autocomplete="email" required>

                    <label
                        for="contact-team"><?php echo htmlspecialchars($text['team_label'], ENT_QUOTES, 'UTF-8'); ?></label>
                    <input id="contact-team" type="text" name="teamname"
                        placeholder="<?php echo htmlspecialchars($text['team_placeholder'], ENT_QUOTES, 'UTF-8'); ?>"
                        required>

                    <label
                        for="contact-message"><?php echo htmlspecialchars($text['message_label'], ENT_QUOTES, 'UTF-8'); ?></label>
                    <textarea id="contact-message" name="message" rows="5"
                        placeholder="<?php echo htmlspecialchars($text['message_placeholder'], ENT_QUOTES, 'UTF-8'); ?>"
                        required></textarea>

                    <button type="submit" name="submit"
                        class="contact-submit"><?php echo htmlspecialchars($text['submit'], ENT_QUOTES, 'UTF-8'); ?></button>
                </form>

                <div class="social-links">
                    <a href="https://www.linkedin.com/in/team-alphabit-b0b0b333a/" class="fa fa-linkedin"
                        aria-label="LinkedIn"></a>
                    <a href="https://www.youtube.com/@alphabit-ro1378" class="fa fa-youtube" aria-label="YouTube"></a>
                    <a href="https://www.facebook.com/AlphaBitPetrosani" class="fa fa-facebook"
                        aria-label="Facebook"></a>
                    <a href="https://www.instagram.com/alphabit137/" class="fa fa-instagram" aria-label="Instagram"></a>
                </div>
            </div>

            <aside class="contact-side">
                <p class="contact-details-email">
                    <?php echo htmlspecialchars($text['support_email_label'], ENT_QUOTES, 'UTF-8'); ?>
                    <a href="mailto:support@alphabit.ro">support@alphabit.ro</a>
                </p>

                <p class="contact-details-address">
                    <?php echo htmlspecialchars($text['support_address_label'], ENT_QUOTES, 'UTF-8'); ?>
                    Romania, Hunedoara, Petrosani, Strada 1 Decembrie 1918 7
                </p>

                <div class="mapouter">
                    <div class="gmap_canvas">
                        <iframe class="gmap_iframe"
                            src="https://maps.google.com/maps?width=650&amp;height=450&amp;hl=en&amp;q=Strada 1 Decembrie 1918 7, Petroșani, Romania&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"
                            loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                            title="AlphaBit location"></iframe>
                    </div>
                </div>
            </aside>
        </div>

        <footer class="site-footer">
            <span class="fa fa-copyright" aria-hidden="true"></span>
            <span
                class="copyright-text"><?php echo htmlspecialchars($current_year . ' AlphaBit. ' . $text['rights_reserved'], ENT_QUOTES, 'UTF-8'); ?></span>
        </footer>
    </section>

    <div id="chat-bubble" class="chat-bubble">
        <span
            class="chat-bubble-title"><?php echo htmlspecialchars($text['chat_bubble'], ENT_QUOTES, 'UTF-8'); ?></span>
        <span
            class="chat-bubble-subtitle"><?php echo htmlspecialchars($text['chat_bubble_sub'], ENT_QUOTES, 'UTF-8'); ?></span>
    </div>
    <button id="chat-toggle-btn" class="chat-toggle-btn"
        aria-label="<?php echo htmlspecialchars($text['chat_title'], ENT_QUOTES, 'UTF-8'); ?>">
        <i class="fas fa-comment-dots" aria-hidden="true"></i>
    </button>

    <div id="chat-window" class="chat-window">
        <div class="chat-header">
            <h3><?php echo htmlspecialchars($text['chat_title'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <button id="chat-close-btn" class="chat-close-btn" aria-label="Close">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
        <div id="chat-messages" class="chat-messages">
            <div class="message ai"><?php echo htmlspecialchars($text['chat_welcome'], ENT_QUOTES, 'UTF-8'); ?></div>
            <div id="typing-indicator" class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>
        <div class="chat-input-area">
            <input type="text" id="chat-input"
                placeholder="<?php echo htmlspecialchars($text['chat_placeholder'], ENT_QUOTES, 'UTF-8'); ?>">
            <button id="chat-send-btn" class="chat-send-btn"
                aria-label="<?php echo htmlspecialchars($text['chat_send'], ENT_QUOTES, 'UTF-8'); ?>">
                <i class="fas fa-paper-plane" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <script>
        window.AlphaBitChatConfig = {
            endpoint: '/api/chat.php',
            apiErrorMessage: <?php echo json_encode($text['chat_error']); ?>,
            networkErrorMessage: <?php echo json_encode($text['chat_offline']); ?>
        };

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
            location.reload();
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (!getCookie('site_lang')) {
                document.getElementById('language-popup').style.display = 'flex';
            }

            var navbar = document.getElementById('site-navbar');
            var navToggle = document.getElementById('navbar-toggle');
            var navPanel = document.getElementById('navbar-mobile-panel');
            if (!navbar || !navToggle || !navPanel) {
                return;
            }

            function setMenuState(isOpen) {
                navbar.classList.toggle('is-open', isOpen);
                navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                if (window.innerWidth <= 980) {
                    navPanel.style.display = isOpen ? 'flex' : 'none';
                } else {
                    navPanel.style.display = '';
                }
            }

            setMenuState(false);

            navToggle.addEventListener('click', function () {
                setMenuState(!navbar.classList.contains('is-open'));
            });

            navPanel.addEventListener('click', function (event) {
                if (event.target && event.target.closest('a')) {
                    setMenuState(false);
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 980) {
                    setMenuState(false);
                }
            });
        });
    </script>
    <script src="assets/js/chat.js?v=20260306"></script>
    <?php include_once __DIR__ . '/assets/includes/season_switcher.php'; ?>
</body>

</html>
