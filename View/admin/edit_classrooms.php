<?php
$step = 1;
$error = '';
$success = '';
$h = fn($v) => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');

include __DIR__ . '/../layout/admin_header.php';
?>

<div class="classroom-page">
  <h1 class="classroom-title">Editar Salones</h1>
  <p class="classroom-subtitle">Activa o inactiva los salones disponibles.</p>

<!-- Barra de busqueda -->
  <div class="classroom-search-wrap">
  <input
    type="text"
    id="classroomSearch"
    placeholder="Buscar salón (ej: AC-106, AC-233B...)"
    class="classroom-search-input"
  >
</div>

<!-- Mensaje si no se encontro nada en la busqueda -->
<div id="noResultsMessage" class="no-results-message">
  No se encontraron salones
</div>

  <div class="classroom-grid">
    <?php foreach ($classrooms as $classroom): ?>
      <div class="classroom-box">
        <div class="classroom-name"><?php echo $h($classroom['s_id']); ?></div>

        <div class="classroom-info">
          <p><strong>Capacidad:</strong> <?php echo $h($classroom['s_capacidad']); ?></p>
          <p><strong>Departamento:</strong> <?php echo $h($classroom['s_departamento']); ?></p>
        </div>

        <div class="classroom-actions">
          <span class="classroom-status-text">
            Estado:
            <strong class="classroom-status-text-value">
              <?php echo ((int)$classroom['s_estado'] === 1) ? 'Activo' : 'Inactivo'; ?>
            </strong>
          </span>

          <form method="post" action="<?php echo $h($panel_base); ?>?action=toggle_classroom_status" class="classroom-status-form">
            <input type="hidden" name="s_id" value="<?php echo $h($classroom['s_id']); ?>">
            <input type="hidden" name="current_status" value="<?php echo (int)$classroom['s_estado']; ?>">

            <button
              type="submit"
              class="status-switch <?php echo ((int)$classroom['s_estado'] === 1) ? 'is-active' : 'is-inactive'; ?>"
              aria-label="<?php echo ((int)$classroom['s_estado'] === 1) ? 'Desactivar salón' : 'Activar salón'; ?>"
            >
              <span class="status-switch-circle"></span>
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="classroom-back-wrap">
    <a href="<?php echo $h($panel_base); ?>" class="classroom-back-btn">Volver al panel</a>
  </div>
</div>

<?php include __DIR__ . '/../layout/admin_footer.php'; ?>