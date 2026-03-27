<?php
$step = 1;
$h = fn($v) => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');

include __DIR__ . '/../layout/admin_header.php';
?>

<div class="classroom-page">
  <h1 class="classroom-title">Añadir Salón</h1>
  <p class="classroom-subtitle">Completa la información para registrar un nuevo salón.</p>

  <?php if (!empty($error)): ?>
    <div class="error-message"><?php echo $h($error); ?></div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="success-message"><?php echo $h($success); ?></div>
  <?php endif; ?>

  <div class="add-classroom-form-wrap">
    <form method="post" action="index_admin.php?action=save_classroom" class="add-classroom-form">

      <div class="form-group">
        <label for="s_id">Nombre del salón</label>
        <input type="text" name="s_id" id="s_id" required>
      </div>

      <div class="form-group">
        <label for="s_departamento">Departamento</label>
        <select name="s_departamento" id="s_departamento" required>
          <option value="">Selecciona un departamento</option>
          <?php foreach ($departments as $code => $name): ?>
            <option value="<?php echo $h($code); ?>">
              <?php echo $h($name); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="s_capacidad">Capacidad</label>
        <input type="number" name="s_capacidad" id="s_capacidad" min="0" required>
      </div>

      <div class="add-classroom-actions">
        <a href="index_admin.php" class="classroom-back-btn">Volver al panel</a>
        <button type="submit" class="save-classroom-btn">Guardar salón</button>
      </div>

    </form>
  </div>
</div>

<?php include __DIR__ . '/../layout/admin_footer.php'; ?>