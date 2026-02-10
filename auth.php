<?php
$config = require __DIR__ . '/config.php';

session_start();

if (isset($_POST['login'])) {
    if (($_POST['username'] ?? '') === $config['admin_user']
        && ($_POST['password'] ?? '') === $config['admin_pass']) {
        $_SESSION['admin'] = true;
    } else {
        $error = "Неверный логин или пароль";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
