<?php

require_once __DIR__ . '/../Model/step1Model.php';

class BookingController
{
  public function handle()
  {
    $step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
    if ($step < 1 || $step > 3) $step = 1;

    // Helpers
    $h = function($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };

    $is_valid_date = function($d){
      $t = DateTime::createFromFormat('Y-m-d', $d);
      return $t && $t->format('Y-m-d') === $d;
    };

    $is_valid_time = function($t){
      return (bool)preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $t);
    };

    $model = new RoomModel();
    $salones = $model->getSalones();

    // ---------- NUEVO: Lógica AJAX ----------
    if (isset($_GET['ajax']) && $_GET['ajax'] === 'disponibilidad') { 
    $room = $_GET['room'] ?? '';

    if (!$room) {
        echo '<p>Selecciona un salón primero.</p>';
        exit;
    }

    $disponibilidad = $model->getDisponibilidadByRoom($room);

    if (!$disponibilidad) {
        echo '<p>No hay disponibilidad para este salón.</p>';
        exit;
    }

   
    // LÓGICA PARA RELLENAR INTERVALOS 
    

    $columnasHoras = [
        'd_7_00','d_7_30','d_8_00','d_8_30',
        'd_9_00','d_9_30','d_10_00','d_10_30',
        'd_11_00','d_11_30','d_12_00','d_12_30',
        'd_13_00','d_13_30','d_14_00','d_14_30',
        'd_15_00','d_15_30','d_16_00','d_16_30',
        'd_17_00','d_17_30','d_18_00','d_18_30',
        'd_19_00','d_19_30','d_20_00','d_20_30',
        'd_21_00','d_21_30','d_22_00'
    ];

    foreach ($disponibilidad as &$u) {

        $inicio = -1;

        foreach ($columnasHoras as $i => $col) {

            if ($u[$col] == 1 && $inicio == -1) {
                $inicio = $i;
            }

            if ($u[$col] == 1 && $inicio != -1 && $i != $inicio) {
                for ($j = $inicio; $j <= $i; $j++) {
                    $u[$columnasHoras[$j]] = 1;
                }
                $inicio = $i;
            }
        }
    }
    unset($u);
   
    // Generar la tabla 

    ?>

    <table class="tabla-Disponibilidad">

        <!-- ENCABEZADO -->
        <tr>
            <th>Hora</th>

            <?php foreach ($disponibilidad as $u): ?>
                <?php
                switch ($u['d_dia']) {
                    case 1: $dia = "Lunes"; break;
                    case 2: $dia = "Martes"; break;
                    case 3: $dia = "Miércoles"; break;
                    case 4: $dia = "Jueves"; break;
                    case 5: $dia = "Viernes"; break;
                    case 6: $dia = "Sábado"; break;
                    case 7: $dia = "Domingo"; break;
                    default: $dia = "Desconocido";
                }
                ?>
                <th><?= $dia ?></th>
            <?php endforeach; ?>
        </tr>

        <?php
        $horas = [
            '7:00' => 'd_7_00','7:30' => 'd_7_30','8:00' => 'd_8_00','8:30' => 'd_8_30',
            '9:00' => 'd_9_00','9:30' => 'd_9_30','10:00' => 'd_10_00','10:30' => 'd_10_30',
            '11:00' => 'd_11_00','11:30' => 'd_11_30','12:00' => 'd_12_00','12:30' => 'd_12_30',
            '13:00' => 'd_13_00','13:30' => 'd_13_30','14:00' => 'd_14_00','14:30' => 'd_14_30',
            '15:00' => 'd_15_00','15:30' => 'd_15_30','16:00' => 'd_16_00','16:30' => 'd_16_30',
            '17:00' => 'd_17_00','17:30' => 'd_17_30','18:00' => 'd_18_00','18:30' => 'd_18_30',
            '19:00' => 'd_19_00','19:30' => 'd_19_30','20:00' => 'd_20_00','20:30' => 'd_20_30',
            '21:00' => 'd_21_00','21:30' => 'd_21_30','22:00' => 'd_22_00'
        ];
        ?>

        <!-- FILAS POR HORA -->
        <?php foreach ($horas as $horaTexto => $columna): ?>
            <tr>
                <td><?= $horaTexto ?></td>

                <?php foreach ($disponibilidad as $u): ?>
                    <td class="<?= $u[$columna] == 1 ? 'ocupado' : '' ?>">
                        <!-- <?= $u[$columna] ?> -->
                    </td>
                <?php endforeach; ?>

            </tr>
        <?php endforeach; ?>

    </table>

    <?php
    exit;
}
    // ---------- FIN AJAX ----------

    // Session init
    if (!isset($_SESSION['booking'])) {
      $_SESSION['booking'] = $this->emptyBooking();
    }

    $booking = &$_SESSION['booking'];
    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $action = $_POST['action'] ?? 'next';

      // STEP 1
      if ($step === 1) {
        $booking['room']       = trim($_POST['room'] ?? 'Salon A');
        $booking['date_start'] = trim($_POST['date_start'] ?? '');
        // $booking['date_end']   = trim($_POST['date_end'] ?? '');
        $booking['time_start'] = trim($_POST['time_start'] ?? '');
        $booking['time_end']   = trim($_POST['time_end'] ?? '');
        // $booking['students']   = max(1, min(150, (int)($_POST['students'] ?? 1)));
        $booking['notes']      = trim($_POST['notes'] ?? '');

        if (!in_array($booking['room'], $salones, true)) {
          $error = 'Selecciona un salón válido.';
        } elseif (!$is_valid_date($booking['date_start']) /*|| !$is_valid_date($booking['date_end'])*/) {
          $error = 'Selecciona fechas válidas.';
        } elseif (!$is_valid_time($booking['time_start']) || !$is_valid_time($booking['time_end'])) {
          $error = 'Selecciona horas válidas (HH:MM).';
        } else {
          $startDT = DateTime::createFromFormat('Y-m-d H:i', $booking['date_start'].' '.$booking['time_start']);
          // $endDT   = DateTime::createFromFormat('Y-m-d H:i', $booking['date_end'].' '.$booking['time_end']);

          // if (!$startDT || !$endDT) {
          //   $error = 'Hubo un problema con la fecha/hora. Intenta de nuevo.';
          // } elseif ($endDT <= $startDT) {
          //   $error = 'La fecha/hora de fin debe ser después de la fecha/hora de inicio.';
          // }
        }

        if (!$error) {
          header("Location: index_usuario.php?step=2");
          exit;
        }
      }

      // STEP 2
      if ($step === 2) {
        $booking['full_name']  = trim($_POST['full_name'] ?? '');
        $booking['email']      = trim($_POST['email'] ?? '');
        $booking['phone']      = trim($_POST['phone'] ?? '');
        $booking['department'] = trim($_POST['department'] ?? '');

        if ($booking['full_name'] === '' || $booking['email'] === '') {
          $error = 'Nombre completo y email son requeridos.';
        } elseif (!filter_var($booking['email'], FILTER_VALIDATE_EMAIL)) {
          $error = 'El email no parece válido.';
        }

        if (!$error) {
          header("Location: index_usuario.php?step=3");
          exit;
        }
      }

      // STEP 3
      if ($step === 3 && $action === 'confirm') {
          // Verificar disponibilidad
          if (!$model->isDisponible($booking['room'], $booking['date_start'], $booking['time_start'], $booking['time_end'])) {
              $error = "El salón seleccionado NO está disponible en el horario elegido. Por favor elige otro.";
          } else {
              // Guardar la reserva
              $model->guardarReserva($booking);

              $_SESSION['success'] = "¡Reserva realizada exitosamente!";
              $_SESSION['booking'] = $this->emptyBooking();
              header("Location: index_usuario.php");
              exit;
          }
      }
    }

    // Render (Layout + View)
    $this->render("booking/step{$step}", [
      'step' => $step,
      'booking' => $booking,
      'salones' => $salones,
      'error' => $error,
      'success' => $success,
      'h' => $h,
    ]);
  }

  private function emptyBooking()
  {
    return [
      'room' => 'Salon A',
      'date_start' => '',
      'date_end'   => '',
      'time_start' => '',
      'time_end'   => '',
      'students' => 1,
      'notes' => '',
      'full_name' => '',
      'email' => '',
      'phone' => '',
      'department' => '',
    ];
  }

  private function render($view, $data = [])
  {
    extract($data);

    require __DIR__ . '/../View/layout/header.php';
    require __DIR__ . '/../View/' . $view . '.php';
    require __DIR__ . '/../View/layout/footer.php';
  }
}