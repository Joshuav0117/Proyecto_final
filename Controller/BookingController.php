<?php

require_once __DIR__ . '/../Model/step1Model.php';

class BookingController
{
  public function handle()
  {
    // Mostrar las reuniones hechas por el usuario
    $modelo = new RoomModel();
    $userEmail = $_SESSION['user']['email'];
    $pendientes = $modelo->getPendientesUser($userEmail);

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


    // ---------- NUEVO: Lógica AJAX ---------- ************


    if (isset($_GET['ajax']) && $_GET['ajax'] === 'disponibilidad') { 

    $room = $_GET['room'] ?? '';
    $fecha = $_GET['date'] ?? '';

    if (!$room || !$fecha) {
        echo '<p>Selecciona salón y fecha.</p>';
        exit;
    }

    $diaSemana = (int)date('N', strtotime($fecha));

    $disponibilidad = $model->getDisponibilidadByRoom($room);
    $reuniones = $model->getReunionesByRoomAndDate($room, $fecha);

    // MAPA DE HORAS
    $mapHoras = [
        'd_7_00'=>'07:00:00','d_7_30'=>'07:30:00','d_8_00'=>'08:00:00','d_8_30'=>'08:30:00',
        'd_9_00'=>'09:00:00','d_9_30'=>'09:30:00','d_10_00'=>'10:00:00','d_10_30'=>'10:30:00',
        'd_11_00'=>'11:00:00','d_11_30'=>'11:30:00','d_12_00'=>'12:00:00','d_12_30'=>'12:30:00',
        'd_13_00'=>'13:00:00','d_13_30'=>'13:30:00','d_14_00'=>'14:00:00','d_14_30'=>'14:30:00',
        'd_15_00'=>'15:00:00','d_15_30'=>'15:30:00','d_16_00'=>'16:00:00','d_16_30'=>'16:30:00',
        'd_17_00'=>'17:00:00','d_17_30'=>'17:30:00','d_18_00'=>'18:00:00','d_18_30'=>'18:30:00',
        'd_19_00'=>'19:00:00','d_19_30'=>'19:30:00','d_20_00'=>'20:00:00','d_20_30'=>'20:30:00',
        'd_21_00'=>'21:00:00','d_21_30'=>'21:30:00','d_22_00'=>'22:00:00'
    ];

    $horasClases = [];
    $horasReuniones = [];

    // CLASES (FORZAR BLOQUES)
    foreach ($disponibilidad as $d) {

        if ($d['d_dia'] != $diaSemana) continue;

        $horasTemp = [];

        foreach ($mapHoras as $col => $hora) {
            if ($d[$col] == 1) {
                $horasTemp[] = strtotime($hora);
            }
        }

        // SI HAY MÁS DE UNA HORA, RELLENAR ENTRE LA MENOR Y LA MAYOR
        if (!empty($horasTemp)) {

            $inicio = min($horasTemp);
            $fin    = max($horasTemp) + (30 * 60);

            while ($inicio < $fin) {
                $horasClases[] = date('H:i:s', $inicio);
                $inicio += 30 * 60;
            }
        }
    }

    //  REUNIONES
    foreach ($reuniones as $r) {

      $inicio = strtotime($fecha . ' ' . $r['r_hora_inicio']);
      $fin    = strtotime($fecha . ' ' . $r['r_hora_final']);

      // Redondeos
      $inicio = floor($inicio / (30 * 60)) * (30 * 60);
      $fin    = ceil($fin / (30 * 60)) * (30 * 60);

      while ($inicio <= $fin - 30 * 60) {
          $horasReuniones[] = date('H:i:s', $inicio);
          $inicio += 30 * 60;
      }

      // 🔥 incluir el final
      $horasReuniones[] = date('H:i:s', $fin);
  }

    // LIMPIEZA
    $horasClases = array_flip(array_unique($horasClases));
    $horasReuniones = array_flip(array_unique($horasReuniones));

    // Areglar Fecha

    setlocale(LC_TIME, 'es_ES.UTF-8');
    $fechaFormateada = strftime('%d - %B', strtotime($fecha));

    // TABLA

    echo "<table class='tabla-Disponibilidad'>";
    echo "<tr><th>Hora</th><th>$fechaFormateada</th></tr>";

    foreach ($mapHoras as $hora) {

        $clase = '';
        $estado = 'Disponible';

        if (isset($horasClases[$hora])) {
            $clase = 'clase';
            $estado = 'Clase';
        } elseif (isset($horasReuniones[$hora])) {
            $clase = 'reunion';
            $estado = 'Reunión';
        }

        echo "<tr>";
        // echo "<td>" . substr($hora, 0, 5) . "</td>";
        echo "<td>" . date('g:i A', strtotime($hora)) . "</td>";
        echo "<td class='$clase'>$estado</td>";
        echo "</tr>";
    }

    echo "</table>";

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

    // Mensaje de éxito después de guardar la reservación
    if (isset($_SESSION['success'])) {
      $success = $_SESSION['success'];
      unset($_SESSION['success']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'actualizarReserva') {
        $modelo->actualizarReserva();
        exit;
    }
        
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
          // $startDT = DateTime::createFromFormat('Y-m-d H:i', $booking['date_start'].' '.$booking['time_start']);
           // VALIDAR FECHA PASADA
          $fechaActual = date('Y-m-d');

          if ($booking['date_start'] < $fechaActual) {
              $error = 'No puedes seleccionar una fecha pasada.';
          }

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
        // $booking['email']      = trim($_POST['email'] ?? '');
        $booking['email']      = $_SESSION['user']['email'];
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
      'pendientes' => $pendientes
    ]);
  }
  // REDONDEAR HACIA ARRIBA A 30 MIN
  private function redondearArriba($hora) {
      $timestamp = strtotime($hora);

      $minutos = date('i', $timestamp);

      if ($minutos == 0 || $minutos == 30) {
          return date('H:i:s', $timestamp);
      }

      if ($minutos < 30) {
          return date('H:30:00', $timestamp);
      } else {
          return date('H:00:00', strtotime('+1 hour', $timestamp));
      }
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