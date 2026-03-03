<?php
$servername = 'localhost';
$db_username = 'root';
$db_password = '72hFig28JGo0K';
$database = 'alphabit';

$record_file = @fopen('/var/www/html/record_index.txt', 'a');
if ($record_file) {
    fwrite($record_file, "signup\n");
    fclose($record_file);
}

session_start();
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === 'userLoggedIn') {
    header('Location: /');
    exit;
}

$conn = new mysqli($servername, $db_username, $db_password, $database);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$season_cookie = isset($_COOKIE['season_choice']) ? $_COOKIE['season_choice'] : 'IntoTheDeep';
$season_path = ($season_cookie === 'Decode') ? 'decode' : 'intothedeep';

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$user_password = isset($_POST['password']) ? trim($_POST['password']) : '';
$teamname = isset($_POST['teamname']) ? trim($_POST['teamname']) : '';
$error = '';
$success = '';

if ($email !== '' && $user_password !== '' && $teamname !== '') {
    $check_stmt = $conn->prepare('SELECT email FROM users WHERE email = ? LIMIT 1');
    if ($check_stmt) {
        $check_stmt->bind_param('s', $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = 'An account with this e-mail already exists.';
        }
        $check_stmt->close();
    } else {
        $error = 'Registration is temporarily unavailable. Please try again.';
    }

    if ($error === '') {
        $insert_stmt = $conn->prepare('INSERT INTO users (password, email, teamname) VALUES (?, ?, ?)');
        if ($insert_stmt) {
            $insert_stmt->bind_param('sss', $user_password, $email, $teamname);
            if ($insert_stmt->execute()) {
                header('Location: /login');
                exit;
            }
            $error = 'Unable to create your account right now.';
            $insert_stmt->close();
        } else {
            $error = 'Registration is temporarily unavailable. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - AlphaBit OpenML</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/auth.css?v=20260304">
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
        </nav>

        <div class="navbar-actions">
            <a class="nav-link nav-link-soft is-active" href="/register">Sign Up</a>
            <a class="nav-link nav-link-accent" href="/login">Login</a>
        </div>
    </header>

    <main class="auth-page">
        <section class="auth-card" aria-labelledby="register-title">
            <h1 id="register-title">Create Account</h1>
            <p class="auth-subtitle">Register your FTC team and start using OpenML resources.</p>

            <?php if ($error !== ''): ?>
                <p class="auth-message error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($success !== ''): ?>
                <p class="auth-message success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <form id="register-form" action="/register" method="post" novalidate>
                <label for="email">E-mail Address</label>
                <input id="email" type="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="email" required>

                <label for="passwordn">Password</label>
                <input id="passwordn" type="password" autocomplete="new-password" required>

                <label for="confirmPassword">Repeat Password</label>
                <input id="confirmPassword" type="password" name="password" autocomplete="new-password" required>

                <label for="teamname">Team Name</label>
                <input id="teamname" type="text" name="teamname" value="<?php echo htmlspecialchars($teamname, ENT_QUOTES, 'UTF-8'); ?>" required>

                <p id="message" class="auth-message"></p>

                <button type="submit">Create Account</button>
            </form>

            <div class="auth-links">
                <a href="/login">Already have an account?</a>
                <a href="/">Back to home</a>
            </div>
        </section>
    </main>

    <script>
        const password = document.getElementById('passwordn');
        const confirmPassword = document.getElementById('confirmPassword');
        const message = document.getElementById('message');
        const form = document.getElementById('register-form');

        function checkMatch() {
            if (password.value === '' && confirmPassword.value === '') {
                message.textContent = '';
                message.className = 'auth-message';
                return;
            }

            if (password.value === confirmPassword.value) {
                message.textContent = 'Passwords match.';
                message.className = 'auth-message success';
                return;
            }

            message.textContent = 'Passwords do not match.';
            message.className = 'auth-message error';
        }

        password.addEventListener('input', checkMatch);
        confirmPassword.addEventListener('input', checkMatch);

        form.addEventListener('submit', function (event) {
            if (password.value !== confirmPassword.value) {
                event.preventDefault();
                message.textContent = 'Passwords do not match.';
                message.className = 'auth-message error';
            }
        });
    </script>
</body>
</html>
