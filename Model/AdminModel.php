<?php
require_once __DIR__ . '/dbConnect.php';

class AdminModel
{
    public function getAllByRole($role)
    {
        global $pdo;

        // Trae todos los usuarios activos o inactivos según su rol
        $sql = "SELECT a_id, a_nombre, a_primer_apellido, a_segundo_apellido, a_email, a_rol, a_departamento, a_estado
                FROM Administrador
                WHERE a_rol = :rol
                ORDER BY a_nombre ASC, a_primer_apellido ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['rol' => $role]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateRole($a_id, $new_role)
    {
        global $pdo;

        // Cambia el rol del usuario
        $sql = "UPDATE Administrador SET a_rol = :rol WHERE a_id = :id";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'rol' => $new_role,
            'id'  => $a_id
        ]);
    }

    public function updateStatus($a_id, $new_status)
    {
        global $pdo;

        // Cambia el estado del usuario: 1 activo, 0 inactivo
        $sql = "UPDATE Administrador SET a_estado = :estado WHERE a_id = :id";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'estado' => $new_status,
            'id'     => $a_id
        ]);
    }
}