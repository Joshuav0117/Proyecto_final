<?php
session_start();

require_once __DIR__ . '/Controller/DirectorController.php';

$controller = new DirectorController();
$controller->index();