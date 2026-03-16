<?php

require_once __DIR__ . '/dbConnect.php';

class RoomModel {

    public function getSalones() {

        global $pdo;

        $sql = "SELECT s_id FROM Salon";
        $stmt = $pdo->query($sql);

        $salones = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $salones[] = $row['s_id'];
        }

        return $salones;
    }
}