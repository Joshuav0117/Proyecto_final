<?php
session_start();

/**
 * Reservación de salones (PHP + HTML + CSS)
 * Step 1: Selección de salón + fecha + hora + cantidad + notas
 * Step 2: Datos personales
 * Step 3: Revisión y confirmación
 */

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
if ($step < 1 || $step > 3) $step = 1;

if (!isset($_SESSION['booking'])) {
  $_SESSION['booking'] = [
    'room' => 'Salon A',
    'date' => '',
    'time' => '',
    'students' => 1,
    'notes' => '',
    'full_name' => '',
    'email' => '',
    'phone' => '',
    'department' => '',
  ];
}

$booking = &$_SESSION['booking'];
$error = '';
$success = '';

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function is_valid_date($d){
  $t = DateTime::createFromFormat('Y-m-d', $d);
  return $t && $t->format('Y-m-d') === $d;
}

function is_valid_time($t){
  // Formato HH:MM (24h)
  return (bool)preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $t);
}

$salones = [
  'Salon A',
  'Salon B',
  'Salon C',
  'Laboratorio 101',
  'Laboratorio 202',
  'Salon de Conferencias'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? 'next';

  if ($step === 1) {
    $booking['room'] = trim($_POST['room'] ?? 'Salon A');
    $booking['date'] = trim($_POST['date'] ?? '');
    $booking['time'] = trim($_POST['time'] ?? '');
    $booking['students'] = max(1, min(150, (int)($_POST['students'] ?? 1)));
    $booking['notes'] = trim($_POST['notes'] ?? '');

    if (!in_array($booking['room'], $salones, true)) {
      $error = 'Selecciona un salón válido.';
    } elseif (!is_valid_date($booking['date'])) {
      $error = 'Selecciona una fecha válida.';
    } elseif (!is_valid_time($booking['time'])) {
      $error = 'Selecciona una hora válida (HH:MM).';
    }

    if (!$error) {
      header("Location: index.php?step=2");
      exit;
    }
  }

  if ($step === 2) {
    $booking['full_name'] = trim($_POST['full_name'] ?? '');
    $booking['email'] = trim($_POST['email'] ?? '');
    $booking['phone'] = trim($_POST['phone'] ?? '');
    $booking['department'] = trim($_POST['department'] ?? '');

    if ($booking['full_name'] === '' || $booking['email'] === '') {
      $error = 'Nombre completo y email son requeridos.';
    } elseif (!filter_var($booking['email'], FILTER_VALIDATE_EMAIL)) {
      $error = 'El email no parece válido.';
    }

    if (!$error) {
      header("Location: index.php?step=3");
      exit;
    }
  }

  if ($step === 3) {
    if ($action === 'confirm') {
      // Demo: aquí guardarías en BD (MySQL) o enviarías email.
      $success = '¡Reserva confirmada! (Demo) Puedes volver a empezar cuando quieras.';

      // Reset demo
      $_SESSION['booking'] = [
        'room' => 'Salon A',
        'date' => '',
        'time' => '',
        'students' => 1,
        'notes' => '',
        'full_name' => '',
        'email' => '',
        'phone' => '',
        'department' => '',
      ];
      $booking = &$_SESSION['booking'];
    } else {
      header("Location: index.php?step=2");
      exit;
    }
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Proyecto Final - Reservación de Salones</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <div class="split">
    <section class="left" aria-label="Imagen">
      <div class="overlay">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
          <div class="brand">
            <div class="logo" aria-hidden="true">
              <svg viewBox="0 0 24 24" role="img" aria-label="logo">
                <path d="M12 2 1 21h22L12 2zm0 4.3 7.3 13.4H4.7L12 6.3z"/>
              </svg>
            </div>
            <div class="name">Universidad</div>
          </div>

          <div class="social" aria-label="social">
            <a href="#" title="Facebook">f</a>
            <a href="#" title="Twitter">t</a>
            <a href="#" title="Instagram">i</a>
          </div>
        </div>

        <div class="hero">
          <div class="tag">Reservación de Salones</div>
          <p>Selecciona el salón, fecha y hora para tu actividad.</p>
        </div>
      </div>
    </section>

    <section class="right" aria-label="Formulario">
      <div class="topbar">
        <button class="burger" type="button" aria-label="Menu">
          <span></span>
        </button>
      </div>

      <div class="card">
        <div class="step"><?php echo $step; ?>/3</div>
        <h1>
          <?php if($step===1) echo "Detalles de la Reservación"; ?>
          <?php if($step===2) echo "Datos del Solicitante"; ?>
          <?php if($step===3) echo "Revisar y Confirmar"; ?>
        </h1>

        <div class="form">
          <?php if($error): ?>
            <div class="error"><?php echo h($error); ?></div>
          <?php endif; ?>
          <?php if($success): ?>
            <div class="success"><?php echo h($success); ?></div>
          <?php endif; ?>

          <?php if($step === 1): ?>
            <form method="post" action="index.php?step=1">

              <!-- 1) LISTA DE SALONES PRIMERO -->
              <div class="row">
                <div class="field">
                  <label>Lista de salones</label>
                  <select name="room">
                    <?php foreach($salones as $s): ?>
                      <option value="<?php echo h($s); ?>" <?php echo ($booking['room']===$s?'selected':''); ?>>
                        <?php echo h($s); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <div class="icon" aria-hidden="true">🏫</div>
                </div>
              </div>

              <!-- 2) FECHA Y HORA -->
              <div class="row two">
                <div class="field">
                  <label>Fecha</label>
                  <input type="date" name="date" value="<?php echo h($booking['date']); ?>" required />
                  <div class="icon" aria-hidden="true">📅</div>
                </div>

                <div class="field">
                  <label>Hora</label>
                  <input type="time" name="time" value="<?php echo h($booking['time']); ?>" required />
                  <div class="icon" aria-hidden="true">⏰</div>
                </div>
              </div>

              <!-- 3) ESTUDIANTES -->
              <div class="row">
                <div class="field">
                  <label>Cantidad de estudiantes</label>
                  <div class="counter">
                    <button type="button" data-dec="students">−</button>
                    <input type="number" id="students" name="students" min="1" max="150" value="<?php echo h($booking['students']); ?>" />
                    <button type="button" data-inc="students">+</button>
                  </div>
                </div>
              </div>

              <!-- 4) NOTAS -->
              <div class="row">
                <div class="field">
                  <label>Notas</label>
                  <textarea name="notes" placeholder="Ej: Examen, reunión, laboratorio, presentación..."><?php echo h($booking['notes']); ?></textarea>
                </div>
              </div>

              <div class="actions">
                <div></div>
                <button class="btn primary" type="submit">Next</button>
              </div>

              <div class="note">Tip: cambia la imagen en <b>assets/img/hero.jpg</b> para poner una de tu universidad.</div>
            </form>

          <?php elseif($step === 2): ?>
            <form method="post" action="index.php?step=2">
              <div class="row">
                <div class="field">
                  <label>Nombre completo</label>
                  <input type="text" name="full_name" value="<?php echo h($booking['full_name']); ?>" placeholder="Tu nombre" required />
                </div>
              </div>

              <div class="row two">
                <div class="field">
                  <label>Email</label>
                  <input type="email" name="email" value="<?php echo h($booking['email']); ?>" placeholder="correo@ejemplo.com" required />
                </div>
                <div class="field">
                  <label>Teléfono (opcional)</label>
                  <input type="tel" name="phone" value="<?php echo h($booking['phone']); ?>" placeholder="787-000-0000" />
                </div>
              </div>

              <div class="row">
                <div class="field">
                  <label>Departamento / Curso (opcional)</label>
                  <input type="text" name="department" value="<?php echo h($booking['department']); ?>" placeholder="Ej: COMP 3001 / Enfermería" />
                </div>
              </div>

              <div class="actions">
                <a class="btn ghost" href="index.php?step=1">Back</a>
                <button class="btn primary" type="submit">Next</button>
              </div>
            </form>

          <?php else: ?>
            <div class="row">
              <div class="note" style="margin-top:0">
                Revisa tu información antes de confirmar.
              </div>
            </div>

            <div class="row two">
              <div class="field">
                <label>Salón</label>
                <input type="text" value="<?php echo h($booking['room']); ?>" readonly />
              </div>
              <div class="field">
                <label>Fecha y hora</label>
                <input type="text" value="<?php echo h($booking['date']); ?> — <?php echo h($booking['time']); ?>" readonly />
              </div>
            </div>

            <div class="row two">
              <div class="field">
                <label>Estudiantes</label>
                <input type="text" value="<?php echo h($booking['students']); ?>" readonly />
              </div>
              <div class="field">
                <label>Departamento/Curso</label>
                <input type="text" value="<?php echo h($booking['department']); ?>" readonly />
              </div>
            </div>

            <div class="row">
              <div class="field">
                <label>Nombre</label>
                <input type="text" value="<?php echo h($booking['full_name']); ?>" readonly />
              </div>
            </div>

            <div class="row two">
              <div class="field">
                <label>Email</label>
                <input type="text" value="<?php echo h($booking['email']); ?>" readonly />
              </div>
              <div class="field">
                <label>Teléfono</label>
                <input type="text" value="<?php echo h($booking['phone']); ?>" readonly />
              </div>
            </div>

            <div class="row">
              <div class="field">
                <label>Notas</label>
                <textarea readonly><?php echo h($booking['notes']); ?></textarea>
              </div>
            </div>

            <form method="post" action="index.php?step=3">
              <div class="actions">
                <a class="btn ghost" href="index.php?step=2">Back</a>
                <button class="btn primary" type="submit" name="action" value="confirm">Confirm</button>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <div class="footerlinks">
        <div class="leftlinks">
          <a href="#">Salones</a>
          <a href="#">Contacto</a>
          <a href="#">Proyecto Final</a>
        </div>
        <div>© <?php echo date('Y'); ?> Universidad</div>
      </div>
    </section>
  </div>

  <script>
    // + / - counters
    (function(){
      function clamp(n, min, max){ return Math.max(min, Math.min(max, n)); }

      document.querySelectorAll('[data-inc]').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          const id = btn.getAttribute('data-inc');
          const input = document.getElementById(id);
          const min = parseInt(input.min || "0", 10);
          const max = parseInt(input.max || "10", 10);
          input.value = clamp((parseInt(input.value || "0", 10) + 1), min, max);
        });
      });

      document.querySelectorAll('[data-dec]').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          const id = btn.getAttribute('data-dec');
          const input = document.getElementById(id);
          const min = parseInt(input.min || "0", 10);
          const max = parseInt(input.max || "10", 10);
          input.value = clamp((parseInt(input.value || "0", 10) - 1), min, max);
        });
      });
    })();
  </script>
</body>
</html>