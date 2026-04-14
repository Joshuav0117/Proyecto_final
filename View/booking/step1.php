<!-- Página 1/3 para poder reservar un salon -->

<form method="post" action="index_usuario.php?step=1">
  <div class="field">
      <label>Lista de salones</label>
      <select name="room" id="roomSelect">
        <option value="">-- Selecciona un salón --</option>
        <?php foreach($salones as $s): ?>
          <option value="<?php echo $h($s); ?>" <?php echo ($booking['room'] === $s ? 'selected' : ''); ?>>
            <?php echo $h($s); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div class="icon" aria-hidden="true"><i class="fa-regular fa-building"></i></div>
    </div>
  </div>

  <div class="field">
    <label>Fecha (inicio)</label>
    <input id="date_start" type="date" name="date_start" value="<?php echo $h($booking['date_start']); ?>" required />
    <button type="button" class="icon-btn" data-open="date_start" aria-label="Abrir calendario (inicio)"><i class="fa-solid fa-calendar"></i></button>
  </div>

  <!-- <div class="row two">
    <div class="field">
      <label>Fecha (inicio)</label>
      <input id="date_start" type="date" name="date_start" value="<?php echo $h($booking['date_start']); ?>" required />
      <button type="button" class="icon-btn" data-open="date_start" aria-label="Abrir calendario (inicio)">📅</button>
    </div>

    <div class="field">
      <label>Fecha (fin)</label>
      <input id="date_end" type="date" name="date_end" value="<?php echo $h($booking['date_end']); ?>" required />
      <button type="button" class="icon-btn" data-open="date_end" aria-label="Abrir calendario (fin)">📅</button>
    </div>
  </div> -->

  <div class="row two">
    <div class="field">
      <label>Hora (inicio)</label>
      <input id="time_start" type="time" name="time_start" step="1800" value="<?php echo $h($booking['time_start']); ?>" required />
    <button type="button" class="icon-btn" data-open="time_start" aria-label="Abrir reloj (inicio)">
      <button type="button" class="icon-btn" data-open="time_start" step="1800" aria-label="Abrir reloj (inicio)"><i class="fa-solid fa-clock" style="color: rgb(14, 120, 11);"></i></button>
    </div>

    <div class="field">
      <label>Hora (fin)</label>
      <input id="time_end" type="time" name="time_end" value="<?php echo $h($booking['time_end']); ?>" required />
      <button type="button" class="icon-btn" data-open="time_end" aria-label="Abrir reloj (fin)"><i class="fa-solid fa-clock" style="color: rgb(153, 9, 9);"></i></button>
    </div>
  </div>

  <!-- <div class="row">
    <div class="field">
      <label>Cantidad de estudiantes</label>
      <div class="counter">
        <button type="button" data-dec="students">−</button>
        <input type="number" id="students" name="students" min="1" max="150" value="<?php echo $h($booking['students']); ?>" />
        <button type="button" data-inc="students">+</button>
      </div>
    </div>
  </div> -->

  <div class="row">
    <div class="field">
      <label>Notas</label>
      <textarea name="notes" placeholder="Ej: Examen, reunión, laboratorio, presentación..."><?php echo $h($booking['notes']); ?></textarea>
    </div>
  </div>

  <div class="actions">
    <div></div>
    <button class="btn primary" type="submit">Next</button>
  </div>
</form>
<div class="admin-divider"><br>
 <?php if ($step === 1): ?>
            <div class="card">
              <h1>Reservaciones Realizadas</h1>

              <div class="pending-container">

                <?php if (!empty($pendientes)): ?>

                  <?php foreach ($pendientes as $row): ?>

                    <div class="card" onclick="toggleCard(this)">

                      <div class="card-header">
                        <div>
                          <strong>Nombre: <?= $h($row['r_nombre']) ?></strong><br>
                          <small><?= $h($row['r_email']) ?></small>

                          <small>
                            <?= match($row['r_aprobacion']){
                              1 => '<i class="fa-solid fa-circle-exclamation" style="color: rgb(255, 212, 59);"></i>',
                              2 => '<i class="fa-solid fa-circle-check" style="color: rgb(14, 120, 11);"></i>',
                              default => '<i class="fa-solid fa-circle-xmark" style="color: rgb(254, 0, 0);"></i>'
                            } ?>
                          </small>
                        </div>

                        <div class="card-time">
                          <small>Día: <b><?= $h($row['r_dia']) ?></b></small><br>
                          <small>Horario: <b><?= date("g:i A", strtotime($row['r_hora_inicio'])) ?> - <?= date("g:i A", strtotime($row['r_hora_final'])) ?></b></small>
                        </div>
                      </div>

                      <div class="card-body">
                        <p><strong>Salón:</strong> <?= $h($row['s_id']) ?></p>
                        <p><strong>Organización:</strong> <?= $h($row['r_organizacion']) ?></p>
                        <p><strong>Descripción:</strong> <?= $h($row['r_descripcion']) ?></p>

                        <!-- borrar reservacion -->
                        <div class="actions">
                          <button class="btn-deny" onclick="accion(event, <?= $row['r_id'] ?>, 0)">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                        </div>
                      </div>

                    </div>

                  <?php endforeach; ?>

                <?php else: ?>
                  <div class="pending-empty">
                    No hay reservaciones pendientes
                  </div>
                <?php endif; ?>

              </div>
            </div>
          <?php endif; ?>
  </div>

<script>
document.getElementById("tablaDisponibilidad").style.display = "block";

document.addEventListener('DOMContentLoaded', function() {
  const select = document.getElementById('roomSelect');
  const tabla = document.getElementById('tablaDisponibilidad');
  const dateInput = document.getElementById('date_start');

  function cargarTabla() {
    const room = select.value;
    const date = dateInput.value;

    if (!room || !date) {
      tabla.innerHTML = '';
      return;
    }

    fetch(`index_usuario.php?ajax=disponibilidad&room=${encodeURIComponent(room)}&date=${encodeURIComponent(date)}`)
      .then(res => res.text())
      .then(html => {
        tabla.innerHTML = html;
      })
      .catch(err => {
        console.error(err);
        tabla.innerHTML = '<p>Error al cargar</p>';
      });
  }

  select.addEventListener('change', cargarTabla);
  dateInput.addEventListener('change', cargarTabla);
});
</script>