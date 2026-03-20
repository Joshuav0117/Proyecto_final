<?php
$host = "mysql-1d48bef3-upr-8f96.c.aivencloud.com";
$db   = "defaultdb";
$user = "avnadmin";
$pass = "eliminada";
$port = 25148;

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch (PDOException $e) {
    die("Error de conexión SSL: " . $e->getMessage());
}
?>