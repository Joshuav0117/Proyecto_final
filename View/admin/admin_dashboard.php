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
      <span class="admin-btn-icon"><i class="fa-solid fa-pen-to-square"></i></span>
      <span>Editar Salón</span>
    </a>

    <a href="index_admin.php?action=add_classroom" class="admin-btn-box">
      <span class="admin-btn-icon"><i class="fa-solid fa-circle-plus"></i></span>
      <span>Añadir Salón</span>
    </a>

    <a href="index_admin.php?action=add_admin" class="admin-btn-box">
      <span class="admin-btn-icon"><i class="fa-solid fa-user-plus"></i></span>
      <span>Añadir / Modificar Administrador</span>
    </a>

    <a href="index_admin.php?action=añadirArchivo" class="admin-btn-box">
      <span class="admin-btn-icon"><i class="fa-solid fa-file-circle-plus"></i></span>
      <span>Añadir Archivo</span>
    </a>

  </div>

  <div class="pending-section">
  <br>
    <h2 class="pending-title">Reservaciones Pendientes</h2>
    <div class="pending-container"> 
      <?php if (!empty($pendientes)): ?>

        <?php foreach ($pendientes as $row): ?>

          <div class="card" onclick="toggleCard(this)">

            <div class="card-header">
              <div>
                <strong>Nombre: <?= $h($row['r_nombre']) ?></strong><br>
                <small><?= $h($row['r_email']) ?></small>
              </div>

              <div class="card-time">
                <small>Día: <b><?= $h($row['r_dia']) ?></b></small><br>
                <small>Horario: <b><?= $h($row['r_hora_inicio']) ?> - <?= $h($row['r_hora_final']) ?></b></small>
              </div>
            </div>

            <div class="card-body">
              <p><strong>Salón:</strong> <?= $h($row['s_id']) ?></p>
              <p><strong>Organización: <?= $h($row['r_organizacion']) ?></strong></p>
              <p><strong>Teléfono:</strong> <?= $h($row['r_telefono']) ?></p>
              <p><strong>Descripción:</strong> <?= $h($row['r_descripcion']) ?></p>

              <textarea class="nota" placeholder="Añadir nota..." onclick="event.stopPropagation()"></textarea>

              <div class="actions">
                <button class="btn-confirm" onclick="accion(event, <?= $row['r_id'] ?>, 2)">Confirmar <i class="fa-regular fa-circle-check"></i></button>
                <button class="btn-deny" onclick="accion(event, <?= $row['r_id'] ?>, 0)">Denegar <i class="fa-solid fa-x"></i></button>
              </div>
            </div>

          </div>

        <?php endforeach; ?>

      <?php else: ?>

        <div class="pending-empty">
          No hay reservaciones pendientes
        </div>

      <?php endif; ?>

      <!-- <div class="pending-empty">
        No hay reservaciones pendientes
      </div> -->
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleCard(card) {
  card.classList.toggle("active");
}

function accion(e, id, estado) {
  e.stopPropagation();

  let card = e.target.closest('.card');
  let nota = card.querySelector('.nota').value || "";

  fetch('index_admin.php?action=actualizarReserva', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `id=${id}&estado=${estado}&nota=${encodeURIComponent(nota)}`
  })
  .then(res => res.text())
  .then(() => {
    Swal.fire({
      title: '<span class="titulo-exito">¡Éxito!</span>',
      text: 'Acción realizada correctamente',
      icon: 'success',
      confirmButtonText: 'OK',
      background: '#32383a',
      color: 'white',
      confirmButtonColor: '#2bbd0a'
    }).then(() => {
      card.remove();
    });
  });
}
</script>

<?php include __DIR__ . '/../layout/admin_footer.php'; ?>