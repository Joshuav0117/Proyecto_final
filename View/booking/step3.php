<!-- Página 3/3 para poder reservar un salon -->

<div class="row">
  <div class="note" style="margin-top:0">
    Revisa tu información antes de confirmar.
  </div>
</div>

<div class="row two">
  <div class="field">
    <label>Salón</label>
    <input type="text" value="<?php echo $h($booking['room']); ?>" readonly />
  </div>
  <div class="field">
    <label>Fechas</label>
    <input type="text" value="<?php echo $h($booking['date_start']); ?> → <?php echo $h($booking['date_end']); ?>" readonly />
  </div>
</div>

<div class="row two">
  <div class="field">
    <label>Horas</label>
    <input type="text" value="<?php echo $h($booking['time_start']); ?> → <?php echo $h($booking['time_end']); ?>" readonly />
  </div>
  <div class="field">
    <label>Estudiantes</label>
    <input type="text" value="<?php echo $h($booking['students']); ?>" readonly />
  </div>
</div>

<div class="row two">
  <div class="field">
    <label>Departamento/Curso</label>
    <input type="text" value="<?php echo $h($booking['department']); ?>" readonly />
  </div>
  <div class="field">
    <label>Nombre</label>
    <input type="text" value="<?php echo $h($booking['full_name']); ?>" readonly />
  </div>
</div>

<div class="row two">
  <div class="field">
    <label>Email</label>
    <input type="text" value="<?php echo $h($booking['email']); ?>" readonly />
  </div>
  <div class="field">
    <label>Teléfono</label>
    <input type="text" value="<?php echo $h($booking['phone']); ?>" readonly />
  </div>
</div>

<div class="row">
  <div class="field">
    <label>Notas</label>
    <textarea readonly><?php echo $h($booking['notes']); ?></textarea>
  </div>
</div>

<form method="post" action="index_usuario.php?step=3">
  <div class="actions">
    <a class="btn ghost" href="index_usuario.php?step=2">Back</a>
    <button class="btn primary" type="submit" name="action" value="confirm">Confirm</button>
  </div>
</form>