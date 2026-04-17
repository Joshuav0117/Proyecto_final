<?php

require_once __DIR__ . '/dbConnect.php';

class RoomModel {
    private $conn;
    
    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
    }

    // Querys bósicos
    public function getPendientesUser($userEmail) {
        $sql = "SELECT * FROM Reunion 
                WHERE r_estado = 1 AND r_email = :r_email 
                ORDER BY r_dia, r_hora_inicio";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':r_email', $userEmail);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarReserva() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id = $_POST['id'];
        $estado = $_POST['estado'];

        $this->actualizarEstado($id, $estado);
        echo "ok";
    }

    public function actualizarEstado($id, $estado) {
        $sql = "UPDATE Reunion 
                SET r_estado = ? 
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

    public function getSalones() {
        $sql = "SELECT s_id FROM Salon WHERE s_estado = 1";
        $stmt = $this->conn->query($sql);

        $salones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $salones[] = $row['s_id'];
        }

        return $salones;
    }

    public function getDisponibilidadByRoom($room) {
        $stmt = $this->conn->prepare("
            SELECT * 
            FROM Disponibilidad 
            WHERE s_id = :room AND d_estado = 1
        ");
        $stmt->execute(['room' => $room]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReunionesByRoomAndDate($room, $fecha) {
        $sql = "SELECT r_hora_inicio, r_hora_final
                FROM Reunion
                WHERE s_id = :room
                AND r_dia = :fecha
                AND r_estado = 1
                AND r_aprobacion = 2";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'room' => $room,
            'fecha' => $fecha
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GUARDAR RESERVA
    public function guardarReserva($data) {
        $sql = "INSERT INTO Reunion 
                (s_id, r_dia, r_hora_inicio, r_hora_final, r_descripcion, r_organizacion, r_nombre, r_email, r_telefono, r_aprobacion, r_estado)
                VALUES 
                (:room, :date_start, :time_start, :time_end, :notes, :department, :nombre, :email, :phone, :aprobacion, :estado)";

        $stmt = $this->conn->prepare($sql);

        $params = [
            'room'        => $data['room'],
            'date_start'  => $data['date_start'],   
            'time_start'  => $data['time_start'],
            'time_end'    => $data['time_end'],
            'notes'       => $data['notes'],
            'department'  => $data['department'],
            'nombre'      => $data['full_name'],
            'email'       => $data['email'],
            'phone'       => $data['phone'],
            'aprobacion'  => 1,
            'estado'      => 1
        ];

        if (!$stmt->execute($params)) {
            print_r($stmt->errorInfo());
            exit;
        }
    }

    // VALIDACIÓN PRINCIPAL
    public function isDisponible($room, $date, $time_start, $time_end) {

        $fecha = new DateTime($date);
        $diaSemana = (int)$fecha->format('N');

        // Validar clases (tabla Disponibilidad)
        if (!$this->validarDisponibilidadBase($room, $diaSemana, $time_start, $time_end)) {
            return false;
        }

        // Validar reuniones
        if ($this->hayConflictoReuniones($room, $date, $time_start, $time_end)) {
            return false;
        }

        return true;
    }

    // VALIDAR DISPONIBILIDAD
   private function validarDisponibilidadBase($room, $diaSemana, $time_start, $time_end) {

        $stmt = $this->conn->prepare("
            SELECT * 
            FROM Disponibilidad 
            WHERE s_id = :room AND d_dia = :dia AND d_estado = 1
        ");

        $stmt->execute([
            'room' => $room,
            'dia'  => $diaSemana
        ]);

        $disp = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$disp) return true;

        // Todas las horas en orden
        $horas = [
            '7:00'=>'d_7_00','7:30'=>'d_7_30','8:00'=>'d_8_00','8:30'=>'d_8_30',
            '9:00'=>'d_9_00','9:30'=>'d_9_30','10:00'=>'d_10_00','10:30'=>'d_10_30',
            '11:00'=>'d_11_00','11:30'=>'d_11_30','12:00'=>'d_12_00','12:30'=>'d_12_30',
            '13:00'=>'d_13_00','13:30'=>'d_13_30','14:00'=>'d_14_00','14:30'=>'d_14_30',
            '15:00'=>'d_15_00','15:30'=>'d_15_30','16:00'=>'d_16_00','16:30'=>'d_16_30',
            '17:00'=>'d_17_00','17:30'=>'d_17_30','18:00'=>'d_18_00','18:30'=>'d_18_30',
            '19:00'=>'d_19_00','19:30'=>'d_19_30','20:00'=>'d_20_00','20:30'=>'d_20_30',
            '21:00'=>'d_21_00','21:30'=>'d_21_30','22:00'=>'d_22_00'
        ];

        // Convertir a timestamps
        $userStart = strtotime($time_start);
        $userEnd   = strtotime($time_end);

        $ocupado = false;
        $inicioBloque = null;

        foreach ($horas as $hora => $col) {

            if ($disp[$col] == 1 && $inicioBloque === null) {
                // empieza bloque
                $inicioBloque = strtotime($hora);
            }

            // detectar fin del bloque
            $siguiente = next($horas);
            $finBloque = null;

            if ($inicioBloque !== null) {

                // si el siguiente es 0 o no existe → termina bloque
                if (!$siguiente || $disp[$siguiente] == 0) {

                    $finBloque = strtotime($hora);

                    // 🔥 VALIDAR SOLAPAMIENTO
                    if ($userStart < $finBloque && $userEnd > $inicioBloque) {
                        return false;
                    }

                    $inicioBloque = null;
                }
            }
        }

        return true;
    }

    // VALIDAR REUNIONES (FIX)
    private function hayConflictoReuniones($room, $date, $time_start, $time_end) {

        $stmt = $this->conn->prepare("
            SELECT r_hora_inicio, r_hora_final
            FROM Reunion
            WHERE s_id = :room
            AND r_dia = :fecha
            AND r_estado = 1
            AND r_aprobacion IN (1,2)
        ");

        $stmt->execute([
            'room' => $room,
            'fecha' => $date
        ]);

        $reuniones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 🔥 FIX IMPORTANTE (fecha + hora)
        $userStart = strtotime($date . ' ' . $time_start);
        $userEnd   = strtotime($date . ' ' . $time_end);

        foreach ($reuniones as $r) {

            $inicio = strtotime($date . ' ' . $r['r_hora_inicio']);
            $fin    = strtotime($date . ' ' . $r['r_hora_final']);

            // OVERLAP REAL
            if ($userStart < $fin && $userEnd > $inicio) {
                return true;
            }
        }

        return false;
    }

    // Franjas horarias
    private function getFranjasEntreHoras($start, $end) {
        $todas = [
            '7:00'=>'d_7_00','7:30'=>'d_7_30','8:00'=>'d_8_00','8:30'=>'d_8_30',
            '9:00'=>'d_9_00','9:30'=>'d_9_30','10:00'=>'d_10_00','10:30'=>'d_10_30',
            '11:00'=>'d_11_00','11:30'=>'d_11_30','12:00'=>'d_12_00','12:30'=>'d_12_30',
            '13:00'=>'d_13_00','13:30'=>'d_13_30','14:00'=>'d_14_00','14:30'=>'d_14_30',
            '15:00'=>'d_15_00','15:30'=>'d_15_30','16:00'=>'d_16_00','16:30'=>'d_16_30',
            '17:00'=>'d_17_00','17:30'=>'d_17_30','18:00'=>'d_18_00','18:30'=>'d_18_30',
            '19:00'=>'d_19_00','19:30'=>'d_19_30','20:00'=>'d_20_00','20:30'=>'d_20_30',
            '21:00'=>'d_21_00','21:30'=>'d_21_30','22:00'=>'d_22_00'
        ];

        $indices = [];
        $found_start = false;

        foreach ($todas as $hora => $col) {
            if ($hora == $start) $found_start = true;

            if ($found_start) {
                $indices[] = $col;
            }

            if ($hora == $end) break;
        }

        return $indices;
    }
}