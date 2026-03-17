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
      <div class="icon" aria-hidden="true">🏫</div>
    </div>
  </div>

  <div class="field">
    <label>Fecha (inicio)</label>
    <input id="date_start" type="date" name="date_start" value="<?php echo $h($booking['date_start']); ?>" required />
    <button type="button" class="icon-btn" data-open="date_start" aria-label="Abrir calendario (inicio)">📅</button>
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
      <input id="time_start" type="time" name="time_start" value="<?php echo $h($booking['time_start']); ?>" required />
      <button type="button" class="icon-btn" data-open="time_start" aria-label="Abrir reloj (inicio)">⏰</button>
    </div>

    <div class="field">
      <label>Hora (fin)</label>
      <input id="time_end" type="time" name="time_end" value="<?php echo $h($booking['time_end']); ?>" required />
      <button type="button" class="icon-btn" data-open="time_end" aria-label="Abrir reloj (fin)">⏰</button>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  const select = document.getElementById('roomSelect');
  const tabla = document.getElementById('tablaDisponibilidad');

  select.addEventListener('change', function() {
    const room = this.value;

    if (!room) {
      tabla.innerHTML = ''; // limpia la tabla si no hay salón seleccionado
      return;
    }

    fetch('index_usuario.php?ajax=disponibilidad&room=' + encodeURIComponent(room))
      .then(res => res.text())
      .then(html => {
        tabla.innerHTML = html; // inserta la tabla en la sección izquierda
      })
      .catch(err => {
        console.error('Error al cargar disponibilidad:', err);
        tabla.innerHTML = '<p>Error al cargar disponibilidad</p>';
      });
  });
});
</script>