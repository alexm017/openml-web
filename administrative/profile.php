<?php
session_start();
require_once __DIR__ . '/../assets/includes/admin_access.php';

$teamName = isset($_SESSION['teamname']) ? (string) $_SESSION['teamname'] : 'AlphaBit Member';
$isLoggedIn = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === 'userLoggedIn';
$isAdmin = $isLoggedIn && alphabit_session_is_admin();
$seasonCookie = isset($_COOKIE['season_choice']) ? (string) $_COOKIE['season_choice'] : 'Decode';
$seasonPath = ($seasonCookie === 'Decode') ? 'decode' : 'intothedeep';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>AlphaBit - Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/images/alphabit.ico">
    <style>
        :root {
            --pf-ink: #f5f5f4;
            --pf-muted: #d0d0cc;
            --pf-surface: rgba(12, 12, 12, 0.9);
            --pf-border: rgba(255, 255, 255, 0.14);
            --pf-cta-bg: #f1f1ef;
            --pf-cta-bg-hover: #ffffff;
            --pf-cta-ink: #0f0f0f;
            --pf-shadow: 0 18px 38px rgba(0, 0, 0, 0.4);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Manrope', 'Montserrat', sans-serif;
            color: var(--pf-ink);
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
            width: 32rem;
            height: 32rem;
            top: -12rem;
            right: -11rem;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0));
        }

        body::after {
            width: 26rem;
            height: 26rem;
            bottom: -9rem;
            left: -9rem;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0));
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .site-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 90;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.9rem;
            padding: 0.78rem clamp(1rem, 3vw, 3.5rem);
            background: rgba(8, 8, 8, 0.84);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.34);
        }

        .brand-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 800;
            letter-spacing: 0.01em;
            font-size: clamp(1.02rem, 1.3vw, 1.2rem);
        }

        .brand-logo {
            width: 1.85rem;
            height: 1.85rem;
            object-fit: contain;
            transform: translateY(2px);
        }

        .nav-links,
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .nav-link,
        .logout-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.92rem;
            border-radius: 999px;
            border: 1px solid rgba(0, 0, 0, 0.18);
            background: linear-gradient(135deg, var(--pf-cta-bg), #e7e7e4);
            color: var(--pf-cta-ink);
            font-size: 0.9rem;
            font-weight: 700;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        .nav-link:hover,
        .logout-btn:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg, var(--pf-cta-bg-hover), #f0f0ee);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .profile-shell {
            width: min(980px, 100%);
            margin: 0 auto;
            padding: 6.3rem 1rem 2rem;
            display: grid;
            gap: 1rem;
        }

        .profile-card,
        .quick-links {
            border-radius: 1.15rem;
            border: 1px solid var(--pf-border);
            background: var(--pf-surface);
            box-shadow: var(--pf-shadow);
        }

        .profile-card {
            padding: clamp(1rem, 3vw, 1.45rem);
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1rem;
            align-items: center;
        }

        .profile-avatar {
            width: clamp(74px, 13vw, 102px);
            height: clamp(74px, 13vw, 102px);
            border-radius: 999px;
            border: 1px solid var(--pf-border);
            object-fit: cover;
            background: rgba(255, 255, 255, 0.06);
        }

        .profile-name {
            margin: 0;
            font-size: clamp(1.25rem, 2.3vw, 1.65rem);
            line-height: 1.18;
            color: var(--pf-ink);
        }

        .profile-subtext {
            margin: 0.4rem 0 0;
            color: var(--pf-muted);
            font-size: 0.96rem;
            line-height: 1.45;
            max-width: 62ch;
        }

        .quick-links {
            padding: 1rem;
            display: grid;
            gap: 0.65rem;
        }

        .quick-links h2 {
            margin: 0;
            font-size: 1.02rem;
            color: var(--pf-ink);
        }

        .quick-links-row {
            display: flex;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .chip-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 0.8rem;
            border-radius: 999px;
            border: 1px solid var(--pf-border);
            background: rgba(16, 16, 16, 0.9);
            color: var(--pf-muted);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .chip-link:hover {
            color: var(--pf-ink);
            border-color: rgba(255, 255, 255, 0.26);
        }

        @media (max-width: 980px) {
            .site-navbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .nav-links,
            .nav-actions {
                width: 100%;
            }

            .nav-link,
            .logout-btn {
                width: 100%;
            }

            .profile-card {
                grid-template-columns: 1fr;
                text-align: center;
                justify-items: center;
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
        <nav class="nav-links" aria-label="Profile navigation">
            <a class="nav-link" href="/model/<?php echo htmlspecialchars($seasonPath, ENT_QUOTES, 'UTF-8'); ?>/overview">ML Model</a>
            <a class="nav-link" href="/model/<?php echo htmlspecialchars($seasonPath, ENT_QUOTES, 'UTF-8'); ?>/training">Training Data</a>
            <?php if ($isAdmin): ?>
                <a class="nav-link" href="/admin/model-pages">Admin Panel</a>
            <?php endif; ?>
        </nav>
        <div class="nav-actions">
            <a class="logout-btn" href="/logout">Logout</a>
        </div>
    </header>

    <main class="profile-shell">
        <section class="profile-card">
            <img class="profile-avatar" src="/assets/images/user3.png" alt="Profile picture">
            <div>
                <h1 class="profile-name"><?php echo htmlspecialchars($teamName, ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="profile-subtext">
                    This profile page now follows the same AlphaBit visual system. Additional account controls can be added in this panel next.
                </p>
            </div>
        </section>

        <section class="quick-links">
            <h2>Quick Access</h2>
            <div class="quick-links-row">
                <a class="chip-link" href="/model/<?php echo htmlspecialchars($seasonPath, ENT_QUOTES, 'UTF-8'); ?>/overview">Open Documentation</a>
                <a class="chip-link" href="/model/<?php echo htmlspecialchars($seasonPath, ENT_QUOTES, 'UTF-8'); ?>/online_training_ml">Online Training ML</a>
                <a class="chip-link" href="/#contact">Contact</a>
            </div>
        </section>
    </main>

    <?php include_once __DIR__ . '/../assets/includes/season_switcher.php'; ?>
</body>
</html>
