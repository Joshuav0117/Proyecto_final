<?php
require_once __DIR__ . '/dbConnect.php';

class ReunionModel {
        private $conn;

        public function __construct() {
                global $pdo;
                $this->conn = $pdo;
        }

        public function getPendientes() {
                $sql = "SELECT * FROM Reunion WHERE r_aprobacion = 1 AND r_estado = 1 ORDER BY r_dia, r_hora_inicio";
                return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getPendientesDirector($departamento) {
                $sql = "SELECT * FROM Reunion R JOIN Salon S ON R.s_id = S.s_id 
                        WHERE R.r_aprobacion = 1 AND S.s_departamento = :a_departamento AND R.r_estado = 1 ORDER BY r_dia, r_hora_inicio";
                                
                // return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                        $stmt = $this->conn->prepare($sql);
                        $stmt->bindParam(':a_departamento', $departamento);
                        $stmt->execute();
                        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function actualizarEstado($id, $estado) {

                // 1. Obtener la reunión actual
                $reserva = $this->getById($id);

                if ($estado == 2) { // SOLO SI SE CONFIRMA

                        // 2. Cancelar otras reuniones que chocan
                        $sql = "
                        UPDATE Reunion
                        SET r_aprobacion = 0
                        WHERE s_id = :room
                        AND r_dia = :fecha
                        AND r_estado = 1
                        AND r_id != :id
                        AND (
                                r_hora_inicio < :end
                                AND r_hora_final > :start
                        )
                        ";

                        $stmt = $this->conn->prepare($sql);
                        $stmt->execute([
                        'room'  => $reserva['s_id'],
                        'fecha' => $reserva['r_dia'],
                        'id'    => $id,
                        'start' => $reserva['r_hora_inicio'],
                        'end'   => $reserva['r_hora_final']
                        ]);
                }

                // 3. Confirmar o cancelar la actual
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