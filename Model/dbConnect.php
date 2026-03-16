<?php
$host = "mysql-1d48bef3-upr-8f96.c.aivencloud.com";
$db   = "defaultdb";
$user = "avnadmin";
$pass = "Eliminada por seguridad";
$port = 25148;

// // Ruta al certificado ca.pem que descargué de Aiven
// $options = [
//     PDO::MYSQL_ATTR_SSL_CA => __DIR__ . "/ca.pem"
// ];

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
        $user,
        $pass,
        // $options
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return true;
} catch (PDOException $e) {
    die("Error de conexión SSL: " . $e->getMessage());
}
?>