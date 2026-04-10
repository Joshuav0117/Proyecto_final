<?php
$h = fn($v) => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
include __DIR__ . '/../layout/admin_header.php';
?>

<div class="admin-page">

  <h2 class="admin-title">Añadir Administrador</h2>

  <div class="form-card">

    <?php if (!empty($error)): ?>
      <div class="error"><?php echo $h($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="success"><?php echo $h($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="index_admin.php?action=save_admin">

      <div class="form-row">
        <input type="text" name="nombre" placeholder="Nombre" required>
      </div>

      <div class="form-row">
        <input type="text" name="inicial" placeholder="Inicial">
      </div>

      <div class="form-row">
        <input type="text" name="apellido" placeholder="Primer Apellido" required>
      </div>

      <div class="form-row">
        <input type="text" name="segundo_apellido" placeholder="Segundo Apellido">
      </div>

      <div class="form-row">
        <input type="email" name="email" placeholder="Email" required>
      </div>

      <div class="form-row">
        <select name="departamento" required>
          <option value="">Selecciona un departamento</option>
          <?php foreach ($departments as $code => $name): ?>
            <option value="<?php echo $h($code); ?>">
              <?php echo $h($name); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-row">
        <select name="rol" required>
          <option value="">Selecciona un rol</option>
          <option value="Administrador">Administrador</option>
          <option value="Director">Director</option>
        </select>
      </div>

      <button type="submit" class="btn-submit">Guardar</button>

      <a href="index_admin.php" class="btn-back">Volver al panel</a>

    </form>
  </div>

  <!-- Administradores -->
  <div class="admin-divider"></div>
  <h3 class="admin-section-title">Administradores</h3>

  <div class="admin-user-grid">
    <?php foreach ($administradores as $admin): ?>
      <div class="admin-user-box">
        <div class="admin-user-name">
          <?php echo $h($admin['a_nombre']); ?>
          <?php echo $h($admin['a_primer_apellido']); ?>
          <?php echo $h($admin['a_segundo_apellido']); ?>
        </div>

        <div class="admin-user-info">
          <p><strong>Correo:</strong> <?php echo $h($admin['a_email']); ?></p>
        </div>

        <div class="admin-user-actions">
          <form method="post" action="index_admin.php?action=update_admin_role" class="admin-role-form">
            <input type="hidden" name="a_id" value="<?php echo (int)$admin['a_id']; ?>">

            <select name="new_role" class="admin-role-select" onchange="this.form.submit()">
              <option value="Administrador" <?php echo ($admin['a_rol'] === 'Administrador') ? 'selected' : ''; ?>>
                Administrador
              </option>
              <option value="Director" <?php echo ($admin['a_rol'] === 'Director') ? 'selected' : ''; ?>>
                Director
              </option>
            </select>
          </form>
        </div>

        <div class="admin-user-actions">
          <div class="admin-status-text-wrap">
            <p>
              <strong>Estado:</strong>
              <?php echo ((int)$admin['a_estado'] === 1) ? 'Activo' : 'Inactivo'; ?>
            </p>
          </div>

          <form method="post" action="index_admin.php?action=toggle_admin_status" class="admin-status-form">
            <input type="hidden" name="a_id" value="<?php echo (int)$admin['a_id']; ?>">
            <input type="hidden" name="current_status" value="<?php echo (int)$admin['a_estado']; ?>">

            <button
              type="submit"
              class="status-switch <?php echo ((int)$admin['a_estado'] === 1) ? 'is-active' : 'is-inactive'; ?>"
              aria-label="<?php echo ((int)$admin['a_estado'] === 1) ? 'Desactivar usuario' : 'Activar usuario'; ?>"
            >
              <span class="status-switch-circle"></span>
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Directores -->
  <div class="admin-divider"></div>
  <h3 class="admin-section-title">Directores</h3>

  <div class="admin-user-grid">
    <?php foreach ($directores as $director): ?>
      <div class="admin-user-box">
        <div class="admin-user-name">
          <?php echo $h($director['a_nombre']); ?>
          <?php echo $h($director['a_primer_apellido']); ?>
          <?php echo $h($director['a_segundo_apellido']); ?>
        </div>

        <div class="admin-user-info">
          <p><strong>Correo:</strong> <?php echo $h($director['a_email']); ?></p>
          <p><strong>Departamento:</strong> <?php echo $h($director['a_departamento']); ?></p>
        </div>

        <div class="admin-user-actions">
          <form method="post" action="index_admin.php?action=update_admin_role" class="admin-role-form">
            <input type="hidden" name="a_id" value="<?php echo (int)$director['a_id']; ?>">

            <select name="new_role" class="admin-role-select" onchange="this.form.submit()">
              <option value="Administrador" <?php echo ($director['a_rol'] === 'Administrador') ? 'selected' : ''; ?>>
                Administrador
              </option>
              <option value="Director" <?php echo ($director['a_rol'] === 'Director') ? 'selected' : ''; ?>>
                Director
              </option>
            </select>
          </form>
        </div>

        <div class="admin-user-actions">
          <div class="admin-status-text-wrap">
            <p>
              <strong>Estado:</strong>
              <?php echo ((int)$director['a_estado'] === 1) ? 'Activo' : 'Inactivo'; ?>
            </p>
          </div>

          <form method="post" action="index_admin.php?action=toggle_admin_status" class="admin-status-form">
            <input type="hidden" name="a_id" value="<?php echo (int)$director['a_id']; ?>">
            <input type="hidden" name="current_status" value="<?php echo (int)$director['a_estado']; ?>">

            <button
              type="submit"
              class="status-switch <?php echo ((int)$director['a_estado'] === 1) ? 'is-active' : 'is-inactive'; ?>"
              aria-label="<?php echo ((int)$director['a_estado'] === 1) ? 'Desactivar usuario' : 'Activar usuario'; ?>"
            >
              <span class="status-switch-circle"></span>
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<?php include __DIR__ . '/../layout/admin_footer.php'; ?>