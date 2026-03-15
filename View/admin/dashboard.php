<?php
$step = 1;
$error = '';
$success = '';
$h = fn($v) => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');

include __DIR__ . '/../layout/admin_header.php';
?>

<div class="admin-page">


  <div class="admin-button-grid">

    <a href="index_admin.php?action=edit_classrooms" class="admin-btn-box">
      <span class="admin-btn-icon">✎</span>
      <span>Editar Salón</span>
    </a>

    <a href="#" class="admin-btn-box">
      <span class="admin-btn-icon">＋</span>
      <span>Añadir Salón</span>
    </a>

    <a href="#" class="admin-btn-box">
      <span class="admin-btn-icon">👥</span>
      <span>Añadir Administrador</span>
    </a>

    <a href="#" class="admin-btn-box">
      <span class="admin-btn-icon">🗂</span>
      <span>Añadir Archivo</span>
    </a>

  </div>

  <div class="pending-section">
  <br>
    <h2 class="pending-title">Reservaciones Pendientes</h2>

    <div class="pending-empty">
      No hay reservaciones pendientes
    </div>

  </div>

</div>

<?php include __DIR__ . '/../layout/admin_footer.php'; ?>