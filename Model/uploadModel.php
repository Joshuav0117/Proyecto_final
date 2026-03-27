<?php

class UploadModel {

    private $fieldMap = [
        'Course'           => 'course',
        'Term Code'        => 'term_code',
        'Section'          => 'section',
        'Start Date'       => 'start_date',
        'End Date'         => 'end_date',
        'Enrollment'       => 'enrollment',
        'Meetings Days 1'  => 'meeting_days',
        'Classroom 1'      => 'classroom',
        'Prof. Name 1'     => 'professor'
    ];

    private $dayMap = [
        'L' => 1, 'M' => 2, 'W' => 3, 'J' => 4, 'F' => 5, 'S' => 6, 'D' => 7
    ];

    /**
     * Procesa el CSV y devuelve las filas listas para mostrar en la vista
     */
    public function processCSV($file) {

        $uploadDir = 'View/admin/uploads/files/';
        $rows = [];
        $errors = [];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => "Error al subir archivo"];
        }

        if (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'csv') {
            return ['error' => "Debe ser CSV"];
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filePath = $uploadDir . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $filePath);

        $handle = fopen($filePath, "r");
        $headers = fgetcsv($handle, 1000, ",");

        $missing = array_diff(array_keys($this->fieldMap), $headers);
        if (!empty($missing)) {
            return ['error' => "Faltan columnas: " . implode(", ", $missing)];
        }

        $rowNumber = 1;
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {

            $csvRow = array_combine($headers, $data);
            $row = [];
            foreach ($this->fieldMap as $csv => $internal) {
                $row[$internal] = trim($csvRow[$csv] ?? '');
            }

            $rowErrors = [];
            if ($row['course'] === '') $rowErrors[] = "Curso vacío";
            if ($row['term_code'] === '') $rowErrors[] = "Term Code vacío";
            if ($row['section'] === '') $rowErrors[] = "Section vacío";
            if ($row['start_date'] === '') $rowErrors[] = "Start Date vacío";
            if ($row['end_date'] === '') $rowErrors[] = "End Date vacío";
            if (!is_numeric($row['enrollment'])) $rowErrors[] = "Enrollment inválido";

            if (!empty($rowErrors)) {
                $errors[$rowNumber] = $rowErrors;
            }

            $rows[] = $row;
            $rowNumber++;
        }

        fclose($handle);

        return [
            'rows' => $rows,
            'errors' => $errors,
            'fieldMap' => $this->fieldMap
        ];
    }

    /**
     * Guarda los datos del CSV en la base de datos
     */
    public function saveCSVToDB($rows, $pdo) {

        try {
            $pdo->beginTransaction();

            foreach ($rows as $row) {
                $this->saveSalon($row, $pdo);
                $this->saveCurso($row, $pdo);
                if (!empty($row['meeting_days'])) {
                    $this->saveMeetingDays($row, $pdo);
                }
            }

            $this->updateDisponibilidad($pdo);

            $pdo->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Inserta el salón si no existe
     */
    private function saveSalon($row, $pdo) {
        $stmt = $pdo->prepare("SELECT s_id FROM Salon WHERE s_id = ?");
        $stmt->execute([$row['classroom']]);
        if ($stmt->rowCount() === 0) {
            $stmtInsert = $pdo->prepare("INSERT INTO Salon (s_id, s_capacidad, s_estado) VALUES (?, ?, ?, ?)");
            $stmtInsert->execute([$row['classroom'], 0, 1]);
        }
    }

    /**
     * Inserta o actualiza el curso
     */
    private function saveCurso($row, $pdo) {
        $startDateSQL = $row['start_date'] ? DateTime::createFromFormat('j/n/Y', $row['start_date'])->format('Y-m-d') : null;
        $endDateSQL   = $row['end_date'] ? DateTime::createFromFormat('j/n/Y', $row['end_date'])->format('Y-m-d') : null;

        $stmt = $pdo->prepare("SELECT * FROM Curso WHERE c_id = ? AND c_seccion = ?");
        $stmt->execute([$row['course'], $row['section']]);

        if ($stmt->rowCount() > 0) {
            $stmtUpdate = $pdo->prepare("
                UPDATE Curso SET
                    s_id = ?, c_term = ?, c_dia_inicio = ?, c_dia_final = ?,
                    c_matriculados = ?, c_profesor = ?, c_modalidad = ?, c_estado = ?
                WHERE c_id = ? AND c_seccion = ?
            ");
            $stmtUpdate->execute([
                $row['classroom'], $row['term_code'], $startDateSQL, $endDateSQL,
                $row['enrollment'], $row['professor'], 1, 1,
                $row['course'], $row['section']
            ]);
        } else {
            $stmtInsert = $pdo->prepare("
                INSERT INTO Curso (c_id, c_seccion, s_id, c_term, c_dia_inicio, c_dia_final,
                                   c_matriculados, c_profesor, c_modalidad, c_estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtInsert->execute([
                $row['course'], $row['section'], $row['classroom'], $row['term_code'],
                $startDateSQL, $endDateSQL, $row['enrollment'], $row['professor'], 1, 1
            ]);
        }
    }

    /**
     * Guarda los días de reunión en Dias_Reunion
     */
    private function saveMeetingDays($row, $pdo) {
        $meeting = trim($row['meeting_days'] ?? '');
        if (empty($meeting)) return;

        $meeting = str_replace(['–', '—'], '-', $meeting);
        $meeting = preg_replace('/\s+/', ' ', $meeting);

        if (!preg_match('/^([A-Z]+)\s+(.+)$/', $meeting, $matches)) return;

        $dayLetters = $matches[1];
        $timeParts  = explode('-', $matches[2]);
        if (count($timeParts) !== 2) return;

        $horaInicio = date("H:i:s", strtotime(trim($timeParts[0])));
        $horaFinal  = date("H:i:s", strtotime(trim($timeParts[1])));
        $days = str_split($dayLetters);

        foreach ($days as $letter) {
            if (!isset($this->dayMap[$letter])) continue;
            $dia = $this->dayMap[$letter];

            $stmt = $pdo->prepare("SELECT dr_id FROM Dias_Reunion WHERE c_id = ? AND c_seccion = ? AND dr_dia = ?");
            $stmt->execute([$row['course'], $row['section'], $dia]);

            if ($stmt->fetch()) {
                $stmtUpdate = $pdo->prepare("
                    UPDATE Dias_Reunion SET dr_hora_inicio = ?, dr_hora_final = ?
                    WHERE c_id = ? AND c_seccion = ? AND dr_dia = ?
                ");
                $stmtUpdate->execute([$horaInicio, $horaFinal, $row['course'], $row['section'], $dia]);
            } else {
                $stmtInsert = $pdo->prepare("
                    INSERT INTO Dias_Reunion (c_id, c_seccion, dr_dia, dr_hora_inicio, dr_hora_final)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmtInsert->execute([$row['course'], $row['section'], $dia, $horaInicio, $horaFinal]);
            }
        }
    }

    /**
     * Actualiza la tabla Disponibilidad según Dias_Reunion y Curso
     */
   private function updateDisponibilidad($pdo) {
    // Obtener todos los días de reunión con sus cursos y salones
    $sql = "
        SELECT D.dr_dia, D.dr_hora_inicio, D.dr_hora_final, C.s_id
        FROM Dias_Reunion AS D
        JOIN Curso AS C ON D.c_id = C.c_id AND D.c_seccion = C.c_seccion
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $diasReunion = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mapa de horas a columnas
    $horaMap = [
        "07:00:00"=>"d_7_00","07:30:00"=>"d_7_30","08:00:00"=>"d_8_00","08:30:00"=>"d_8_30",
        "09:00:00"=>"d_9_00","09:30:00"=>"d_9_30","10:00:00"=>"d_10_00","10:30:00"=>"d_10_30",
        "11:00:00"=>"d_11_00","11:30:00"=>"d_11_30","12:00:00"=>"d_12_00","12:30:00"=>"d_12_30",
        "13:00:00"=>"d_13_00","13:30:00"=>"d_13_30","14:00:00"=>"d_14_00","14:30:00"=>"d_14_30",
        "15:00:00"=>"d_15_00","15:30:00"=>"d_15_30","16:00:00"=>"d_16_00","16:30:00"=>"d_16_30",
        "17:00:00"=>"d_17_00","17:30:00"=>"d_17_30","18:00:00"=>"d_18_00","18:30:00"=>"d_18_30",
        "19:00:00"=>"d_19_00","19:30:00"=>"d_19_30","20:00:00"=>"d_20_00","20:30:00"=>"d_20_30",
        "21:00:00"=>"d_21_00","21:30:00"=>"d_21_30","22:00:00"=>"d_22_00"
    ];

    foreach ($diasReunion as $dias) {
        // Normalizar horas a HH:MM:SS
        $horaInitRaw = date("H:i:00", strtotime($dias["dr_hora_inicio"]));
        $horaEndRaw  = date("H:i:00", strtotime($dias["dr_hora_final"]));

        // Buscar columnas en el mapa
        $horaInit = $horaMap[$horaInitRaw] ?? null;
        $horaEnd  = $horaMap[$horaEndRaw] ?? null;

        // Si no se encuentra la hora en el mapa, saltar esta fila
        if (!$horaInit || !$horaEnd) continue;

        // Verificar si ya existe un registro en Disponibilidad
        $stmtCheck = $pdo->prepare("SELECT * FROM Disponibilidad WHERE s_id = ? AND d_dia = ?");
        $stmtCheck->execute([$dias['s_id'], $dias['dr_dia']]);

        if ($stmtCheck->rowCount() > 0) {
            // Actualizar registro existente
            $stmtUpdate = $pdo->prepare("
                UPDATE Disponibilidad SET $horaInit = 1, $horaEnd = 1, d_estado = 1
                WHERE s_id = ? AND d_dia = ?
            ");
            $stmtUpdate->execute([$dias["s_id"], $dias["dr_dia"]]);
        } else {
            // Insertar nuevo registro
            $stmtInsert = $pdo->prepare("
                INSERT INTO Disponibilidad (s_id, d_dia, $horaInit, $horaEnd, d_estado)
                VALUES (?, ?, 1, 1, 1)
            ");
            $stmtInsert->execute([$dias["s_id"], $dias["dr_dia"]]);
        }
    }
}
}