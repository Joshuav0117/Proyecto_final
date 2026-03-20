<?php
$step = 1;
$error = '';
$success = '';
$h = fn($v) => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');

include __DIR__ . '/../layout/director_header.php';
?>

<div class="admin-page director-page">

  <div class="admin-button-grid director-button-grid">

    <a href="index_director.php?action=edit_classrooms" class="admin-btn-box">
      <span class="admin-btn-icon">✎</span>
      <span>Editar Salón</span>
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

<?php include __DIR__ . '/../layout/director_footer.php'; ?>