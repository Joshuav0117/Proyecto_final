<?php
session_start();

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = [];
    session_destroy();

    header("Location: index_login.php");
    exit();
}

require_once __DIR__ . '/Controller/AdminController.php';

$controller = new AdminController();
$controller->index();
?>