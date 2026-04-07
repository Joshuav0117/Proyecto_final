<?php
require_once __DIR__ . '/dbConnect.php';

class ReunionModel {
    private $conn;

    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
    }

    public function getPendientes() {
            $sql = "SELECT * FROM Reunion WHERE r_aprobacion = 1 ORDER BY r_dia, r_hora_inicio";
            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }

        public function actualizarEstado($id, $estado) {

            $sql = "UPDATE Reunion 
                    SET r_aprobacion = ?
                    WHERE r_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$estado, $id]);
    }

    public function getById($id) {
        $sql = "SELECT * FROM Reunion WHERE r_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}