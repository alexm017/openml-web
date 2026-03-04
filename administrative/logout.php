<?php
session_start();

if (isset($_COOKIE['PHPSESSID']) === true) {
    $_SESSION['teamname'] = 'null';
    $_SESSION['loggedIn'] = 'null';
    unset($_SESSION['user_email'], $_SESSION['is_admin']);
    unset($_COOKIE['PHPSESSID']);
    header('Location: /');
} else {
    header('Location: /login');
}
?>
