<?php
session_start();
require_once __DIR__ . '/Controller/AuthController.php';

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    AuthController::logout();
}

AuthController::requireRole(['Director']);

require_once __DIR__ . '/Controller/DirectorController.php';

$controller = new DirectorController();
$controller->index();
?>