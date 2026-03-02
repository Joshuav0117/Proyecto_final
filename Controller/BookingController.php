<?php

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

    $salones = [
      'Salon A',
      'Salon B',
      'Salon C',
      'Laboratorio 101',
      'Laboratorio 202',
      'Salon de Conferencias'
    ];

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
        $booking['date_end']   = trim($_POST['date_end'] ?? '');
        $booking['time_start'] = trim($_POST['time_start'] ?? '');
        $booking['time_end']   = trim($_POST['time_end'] ?? '');
        $booking['students']   = max(1, min(150, (int)($_POST['students'] ?? 1)));
        $booking['notes']      = trim($_POST['notes'] ?? '');

        if (!in_array($booking['room'], $salones, true)) {
          $error = 'Selecciona un salón válido.';
        } elseif (!$is_valid_date($booking['date_start']) || !$is_valid_date($booking['date_end'])) {
          $error = 'Selecciona fechas válidas.';
        } elseif (!$is_valid_time($booking['time_start']) || !$is_valid_time($booking['time_end'])) {
          $error = 'Selecciona horas válidas (HH:MM).';
        } else {
          $startDT = DateTime::createFromFormat('Y-m-d H:i', $booking['date_start'].' '.$booking['time_start']);
          $endDT   = DateTime::createFromFormat('Y-m-d H:i', $booking['date_end'].' '.$booking['time_end']);

          if (!$startDT || !$endDT) {
            $error = 'Hubo un problema con la fecha/hora. Intenta de nuevo.';
          } elseif ($endDT <= $startDT) {
            $error = 'La fecha/hora de fin debe ser después de la fecha/hora de inicio.';
          }
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
      if ($step === 3) {
        if ($action === 'confirm') {
          $success = '¡Reserva confirmada! (Demo) Puedes volver a empezar cuando quieras.';
          $_SESSION['booking'] = $this->emptyBooking();
          $booking = &$_SESSION['booking'];
        } else {
          header("Location: index_usuario.php?step=2");
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