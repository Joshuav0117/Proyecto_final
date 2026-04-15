<?php
session_start();
require_once __DIR__ . '/Controller/AuthController.php';

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    AuthController::logout();
}

AuthController::requireRole(['Administrador']);

require_once __DIR__ . '/Controller/AdminController.php';

$controller = new AdminController();
$controller->index();
?>