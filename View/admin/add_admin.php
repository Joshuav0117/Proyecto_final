<?php include __DIR__ . '/../layout/admin_header.php'; ?>

<div class="admin-page">

  <h2 class="admin-title">Añadir Administrador</h2>

  <div class="form-card">

    <?php if (!empty($error)): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="success"><?php echo $success; ?></div>
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
            <option value="<?php echo htmlspecialchars($code); ?>">
              <?php echo htmlspecialchars($name); ?>
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

      <button type="submit" class="btn-submit">
            Guardar
      </button>

      <a href="index_admin.php" class="btn-back">
    Volver al panel
  </a>

    </form>

  </div>

</div>

<?php include __DIR__ . '/../layout/admin_footer.php'; ?>