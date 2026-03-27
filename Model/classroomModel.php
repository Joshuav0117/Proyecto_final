<?php

require_once __DIR__ . '/dbConnect.php';

class ClassroomModel
{
    // Obtener todos los salones
    public function getAllClassrooms()
    {
        global $pdo;

        $sql = "SELECT s_id, s_capacidad, s_estado FROM Salon ORDER BY s_id ASC";
        $stmt = $pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cambiar el estado de un salón (0 = inactivo, 1 = activo)
    public function updateClassroomStatus($s_id, $new_status)
    {
        global $pdo;

        // Actualiza el estado del salón
        $sql = "UPDATE Salon SET s_estado = :estado WHERE s_id = :s_id";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'estado' => $new_status,
            's_id'   => $s_id
        ]);
    }

    // Buscar un salón por su ID
    public function getClassroomById($s_id)
    {
        global $pdo;

        $sql = "SELECT s_id, s_capacidad, s_estado FROM Salon WHERE s_id = :s_id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['s_id' => $s_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertClassroom($s_id, $s_capacidad, $s_departamento)
    {
        global $pdo;

        // Inserta un nuevo salón con valores por defecto
        $sql = "INSERT INTO Salon (s_id, s_capacidad, s_departamento, s_estado)
                VALUES (:id, :capacidad, :departamento, :estado)";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id'            => $s_id,
            'capacidad'     => $s_capacidad,
            'departamento'  => $s_departamento,
            'estado'        => 1
        ]);
    }

    public function classroomExists($s_id)
    {
        global $pdo;

        // Verifica si ya existe un salón con ese ID
        $sql = "SELECT COUNT(*) FROM Salon WHERE s_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $s_id]);

        return $stmt->fetchColumn() > 0;
    }
}