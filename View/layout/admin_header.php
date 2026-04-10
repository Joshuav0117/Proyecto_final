<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panel de Administración</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
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
          <div class="tag">Panel de Administración</div>
          <p>Gestiona salones, usuarios, archivos y reservaciones.</p>
        </div>
      </div>
    </section>

    <section class="right" aria-label="Panel admin">
      <div class="topbar">
        <!-- <button class="burger" type="button" aria-label="Menu">
          <span></span>
        </button> -->
        <a href="/proyecto_final/index_admin.php?action=logout" class="burger" aria-label="Logout">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </a>
      </div>

      <div class="card">