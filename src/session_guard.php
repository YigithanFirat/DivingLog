<?php
session_start();

if (!isset($_SESSION['user_id']) || 
    !isset($_SESSION['user_agent']) || 
    $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 900)) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

$_SESSION['last_activity'] = time();
session_regenerate_id(true);
?>