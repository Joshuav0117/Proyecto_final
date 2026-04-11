<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Proyecto Final - Reservación de Salones</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert success">
      <?= $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  
  <div class="split">
    <section class="left" aria-label="Imagen">
      <div class="overlay">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
          <div class="brand">
            <div class="logo">
              <img src="assets/img/UPRA.png" alt="Logo UPRA">
            </div>
          </div>

          <div class="social" aria-label="social">
            <a href="#" title="Facebook">f</a>
            <a href="#" title="Instagram">i</a>
          </div>
        </div>

        <!-- CONTENEDOR DE LA TABLA -->
        <div id="tablaDisponibilidad" class="tabla-disponibilidad"></div>

        <div class="hero">
          <div class="tag">Reservación de Salones</div>
          <p>Selecciona el salón, fechas y horas para tu actividad.</p>
        </div>
      </div>
    </section>

    <section class="right" aria-label="Formulario">
      <div class="topbar">
        <!-- <button class="burger" type="button" aria-label="Menu">
        <!-- <span></span>
         <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </button> -->
        <a href="/proyecto_final/index_usuario.php?action=logout" class="burger" aria-label="Logout">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </a>
      </div>

      <div class="card">
        <div class="step"><?php echo $step; ?>/3</div>
        <!-- Muestra las reservaciones realizadas por ese usaurio -->
          <?php if ($step === 1): ?>
            <div class="card">
              <h1>Reservaciones Realizadas</h1>

              <div class="pending-container">

                <?php if (!empty($pendientes)): ?>

                  <?php foreach ($pendientes as $row): ?>

                    <div class="card" onclick="toggleCard(this)">

                      <div class="card-header">
                        <div>
                          <strong>Nombre: <?= $h($row['r_nombre']) ?></strong><br>
                          <small><?= $h($row['r_email']) ?></small>

                          <small>
                            <?= match($row['r_aprobacion']){
                              1 => '<i class="fa-solid fa-circle-exclamation" style="color: rgb(255, 212, 59);"></i>',
                              2 => '<i class="fa-solid fa-circle-check" style="color: rgb(14, 120, 11);"></i>',
                              default => '<i class="fa-solid fa-circle-xmark" style="color: rgb(254, 0, 0);"></i>'
                            } ?>
                          </small>
                        </div>

                        <div class="card-time">
                          <small>Día: <b><?= $h($row['r_dia']) ?></b></small><br>
                          <small>Horario: <b><?= $h($row['r_hora_inicio']) ?> - <?= $h($row['r_hora_final']) ?></b></small>
                        </div>
                      </div>

                      <div class="card-body">
                        <p><strong>Salón:</strong> <?= $h($row['s_id']) ?></p>
                        <p><strong>Organización:</strong> <?= $h($row['r_organizacion']) ?></p>
                        <p><strong>Descripción:</strong> <?= $h($row['r_descripcion']) ?></p>

                        <!-- borrar reservacion -->
                        <div class="actions">
                          <button class="btn-deny" onclick="accion(event, <?= $row['r_id'] ?>, 0)">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                        </div>
                      </div>

                    </div>

                  <?php endforeach; ?>

                <?php else: ?>
                  <div class="pending-empty">
                    No hay reservaciones pendientes
                  </div>
                <?php endif; ?>

              </div>
            </div>
          <?php endif; ?>
        <h1>
          <?php if($step===1) echo "Detalles de la Reservación"; ?>
          <?php if($step===2) echo "Datos del Solicitante"; ?>
          <?php if($step===3) echo "Revisar y Confirmar"; ?>
        </h1>
        <div class="form">
          <?php if(!empty($error)): ?>
            <div class="error"><?php echo $h($error); ?></div>
          <?php endif; ?>
          <?php if(!empty($success)): ?>
            <div class="success"><?php echo $h($success); ?></div>
          <?php endif; ?>