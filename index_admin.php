<?php
session_start();

require_once __DIR__ . '/Controller/AdminController.php';

$controller = new AdminController();
$controller->index();
?>