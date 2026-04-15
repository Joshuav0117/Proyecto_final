<?php
session_start();
require_once __DIR__ . '/Controller/AuthController.php';

/**
 * Reservación de salones (PHP + HTML + CSS)
 * Step 1: Selección de salón + fechas (inicio/fin) + horas (inicio/fin) + cantidad + notas
 * Step 2: Datos personales
 * Step 3: Revisión y confirmación
 * Link para correr en el browser: http://localhost/proyecto_final/
 */

// MANEJO DE LOGOUT
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    AuthController::logout();
}

AuthController::requireRole(['Usuario']);

require_once __DIR__ . '/Controller/BookingController.php';

$controller = new BookingController();
$controller->handle();
?>