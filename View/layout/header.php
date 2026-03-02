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
            <div class="logo">
              <img src="assets/img/UPRA.png" alt="Logo UPRA">
            </div>
          </div>

          <div class="social" aria-label="social">
            <a href="#" title="Facebook">f</a>
            <a href="#" title="Instagram">i</a>
          </div>
        </div>

        <div class="hero">
          <div class="tag">Reservación de Salones</div>
          <p>Selecciona el salón, fechas y horas para tu actividad.</p>
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
          <?php if(!empty($error)): ?>
            <div class="error"><?php echo $h($error); ?></div>
          <?php endif; ?>
          <?php if(!empty($success)): ?>
            <div class="success"><?php echo $h($success); ?></div>
          <?php endif; ?>