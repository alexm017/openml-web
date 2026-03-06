<?php
$servername = 'localhost';
$db_username = 'root';
$db_password = '72hFig28JGo0K';
$database = 'alphabit';

$record_file = @fopen('/var/www/html/record_index.txt', 'a');
if ($record_file) {
    fwrite($record_file, "login\n");
    fclose($record_file);
}

session_start();
require_once __DIR__ . '/../assets/includes/admin_access.php';

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === 'userLoggedIn') {
    header('Location: /');
    exit;
}

$conn = new mysqli($servername, $db_username, $db_password, $database);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$season_cookie = isset($_COOKIE['season_choice']) ? $_COOKIE['season_choice'] : 'Decode';
$season_path = ($season_cookie === 'Decode') ? 'decode' : 'intothedeep';

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$user_password = isset($_POST['password']) ? trim($_POST['password']) : '';
$error = '';

if ($email !== '' && $user_password !== '') {
    $stmt = $conn->prepare('SELECT teamname FROM users WHERE email = ? AND password = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('ss', $email, $user_password);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($teamname);
            $stmt->fetch();
            $_SESSION['loggedIn'] = 'userLoggedIn';
            $_SESSION['teamname'] = $teamname;
            $_SESSION['user_email'] = alphabit_normalize_email($email);
            $_SESSION['is_admin'] = alphabit_is_admin_email($email) ? '1' : '0';
            setcookie(session_name(), session_id(), time() + 86400, '/');
            header('Location: /');
            exit;
        }

        $error = 'Invalid email or password.';
        $stmt->close();
    } else {
        $error = 'Login is temporarily unavailable. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AlphaBit OpenML</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/auth.css?v=20260306a">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/images/alphabit.ico">
</head>
<body>
    <header class="site-navbar">
        <a class="brand-link" href="/">
            <span>AlphaBit OpenML</span>
            <img class="brand-logo" src="/assets/images/ai_star_alpha.png" alt="AlphaBit logo">
        </a>

        <nav class="navbar-links" aria-label="Primary">
            <a class="nav-link" href="/model/<?php echo $season_path; ?>/training">Training Data</a>
            <a class="nav-link" href="/model/<?php echo $season_path; ?>/overview">ML Model</a>
            <a class="nav-link" href="/model/<?php echo $season_path; ?>/online_training_ml">Online Training ML</a>
        </nav>

        <div class="navbar-actions">
            <a class="nav-link nav-link-soft" href="/register">Sign Up</a>
            <a class="nav-link nav-link-accent is-active" href="/login">Login</a>
        </div>
    </header>

    <main class="auth-page">
        <section class="auth-card" aria-labelledby="login-title">
            <h1 id="login-title">Login</h1>
            <p class="auth-subtitle">Access your team dashboard and model resources.</p>

            <?php if ($error !== ''): ?>
                <p class="auth-message error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <form id="login-form" action="/login" method="post" novalidate>
                <label for="email">E-mail Address</label>
                <input id="email" type="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="email" required>

                <label for="password">Password</label>
                <input id="password" type="password" name="password" autocomplete="current-password" required>

                <button type="submit">Sign In</button>
            </form>

            <div class="auth-links">
                <a href="/register">Create account</a>
                <a href="/">Back to home</a>
            </div>
        </section>
    </main>
    <?php include_once __DIR__ . '/../assets/includes/season_switcher.php'; ?>
</body>
</html>
