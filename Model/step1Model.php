<?php

require_once __DIR__ . '/dbConnect.php';

class RoomModel {
    private $conn;
    
    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
    }

    public function getPendientesUser($userEmail) {
        $sql = "SELECT * FROM Reunion WHERE r_estado = 1 AND r_email = :r_email ORDER BY r_dia, r_hora_inicio";
                    
        // return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':r_email', $userEmail);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarReserva() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id = $_POST['id'];
        $estado = $_POST['estado'];

        $model = new RoomModel();
        $model->actualizarEstado($id, $estado);

        $reserva = $model->getById($id);

        // Esto es para cuando se envia un email 
            // $mensaje = "Su reservación ha sido ";
            // $mensaje .= ($estado == 1) ? "CONFIRMADA" : "DENEGADA";

            // mail($reserva['r_email'], "Estado de reservación", $mensaje);

        echo "ok";
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

    public function getSalones() {

        global $pdo;

        $sql = "SELECT s_id FROM Salon Where s_estado = 1";
        $stmt = $pdo->query($sql);

        $salones = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $salones[] = $row['s_id'];
        }

        return $salones;
    }

    public function getDisponibilidadByRoom($room) {
        global $pdo;

        $stmt = $pdo->prepare("SELECT * FROM Disponibilidad WHERE s_id = :room and d_estado = 1");
        $stmt->execute(['room' => $room]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getReunionesByRoomAndDate($room, $fecha) {
    global $pdo;

    $sql = "SELECT r_hora_inicio, r_hora_final
            FROM Reunion
            WHERE s_id = :room
            AND r_dia = :fecha
            AND r_estado = 1
            AND r_aprobacion = 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'room' => $room,
        'fecha' => $fecha
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function guardarReserva($data) {
        global $pdo;

        $sql = "INSERT INTO Reunion 
                (s_id, r_dia, r_hora_inicio, r_hora_final, r_descripcion, r_organizacion, r_nombre, r_email, r_telefono, r_aprobacion, r_estado)
                VALUES 
                (:room, :date_start, :time_start, :time_end, :notes, :department, :nombre, :email, :phone, :aprobacion, :estado)";

        $stmt = $pdo->prepare($sql);

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

    public function isDisponible($room, $date, $time_start, $time_end) {
        global $pdo;

        // Convertimos la fecha en número de día de la semana
        // Lunes=1, Domingo=7
        $fecha = new DateTime($date);
        $diaSemana = (int)$fecha->format('N');

        // Obtener disponibilidad del salón para ese día
        $stmt = $pdo->prepare("
            SELECT * 
            FROM Disponibilidad 
            WHERE s_id = :room AND d_dia = :dia AND d_estado = 1
        ");
        $stmt->execute([
            'room' => $room,
            'dia'  => $diaSemana
        ]);
        $disponibilidad = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$disponibilidad) {
            // Si no hay registro de disponibilidad, asumimos que está libre
            return true;
        }

        // Convertimos horas a columnas de disponibilidad
        $franjas = $this->getFranjasEntreHoras($time_start, $time_end);

        foreach ($franjas as $franja) {
            if (!isset($disponibilidad[$franja])) continue; // seguridad
            if ($disponibilidad[$franja] == 1) {
                // La franja está ocupada
                return false;
            }
        }

        // =========================
        // 🔴 VALIDAR REUNIONES
        // =========================
        $stmt = $pdo->prepare("
            SELECT r_hora_inicio, r_hora_final
            FROM Reunion
            WHERE s_id = :room
            AND r_dia = :fecha
            AND r_estado = 1
        ");

        $stmt->execute([
            'room' => $room,
            'fecha' => $date
        ]);

        $reuniones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userStart = strtotime($time_start);
        $userEnd   = strtotime($time_end);

        foreach ($reuniones as $r) {
            $inicio = strtotime($r['r_hora_inicio']);
            $fin    = strtotime($r['r_hora_final']);

            // 🔥 OVERLAP
            if ($userStart < $fin && $userEnd > $inicio) {
                return false; // ❌ ocupado por reunión
            }
        }

        // ✅ SOLO AQUÍ termina
        return true;

    }

    // Función auxiliar para obtener columnas entre horas
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
            if ($found_start) $indices[] = $col;
            if ($hora == $end) break;
        }

        return $indices;
    }
}