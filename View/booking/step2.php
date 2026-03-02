<!-- Página 2/3 para poder reservar un salon -->
 
<form method="post" action="index_usuario.php?step=2">
  <div class="row">
    <div class="field">
      <label>Nombre completo</label>
      <input type="text" name="full_name" value="<?php echo $h($booking['full_name']); ?>" placeholder="Tu nombre" required />
    </div>
  </div>

  <div class="row two">
    <div class="field">
      <label>Email</label>
      <input type="email" name="email" value="<?php echo $h($booking['email']); ?>" placeholder="correo@ejemplo.com" required />
    </div>
    <div class="field">
      <label>Teléfono (opcional)</label>
      <input type="tel" name="phone" value="<?php echo $h($booking['phone']); ?>" placeholder="787-000-0000" />
    </div>
  </div>

  <div class="row">
    <div class="field">
      <label>Departamento / Curso (opcional)</label>
      <input type="text" name="department" value="<?php echo $h($booking['department']); ?>" placeholder="Ej: COMP 3001 / Enfermería" />
    </div>
  </div>

  <div class="actions">
    <a class="btn ghost" href="index_usuario.php?step=1">Back</a>
    <button class="btn primary" type="submit">Next</button>
  </div>
</form>