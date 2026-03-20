<?php
session_start();

require_once __DIR__ . '/Controller/LoginController.php';

$controller = new LoginController();
$controller->handle();
?>