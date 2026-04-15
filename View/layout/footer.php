    </div> <!-- /.form -->
  </div> <!-- /.card -->

      <div class="footerlinks">
        <div class="leftlinks">
          <a href="#">Salones</a>
          <a href="#">Contacto</a>
          <a href="#">Proyecto Final</a>
        </div>
        <div>© <?php echo date('Y'); ?> Universidad</div>
      </div>
    </section>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function toggleCard(card) {
        card.classList.toggle("active");
      }

    function accion(e, id, estado) {
        e.stopPropagation();

        let card = e.target.closest('.card');

        fetch('index_usuario.php?action=actualizarReserva', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: `id=${id}&estado=${estado}`
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

<!-- Mensaje de éxito cuando un usuario realiza una reservación -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (!empty($success)): ?>
<script>
  Swal.fire({
    title: '<span class="titulo-exito">¡Éxito!</span>',
    text: '<?php echo htmlspecialchars($success, ENT_QUOTES, "UTF-8"); ?>',
    icon: 'success',
    confirmButtonText: 'OK',
    background: '#32383a',
    color: 'white',
    confirmButtonColor: '#2bbd0a'
  });
</script>
<?php endif; ?>

<!-- Cantidad de estudiantes +/- -->
<!-- <script>
    (function(){
      function clamp(n, min, max){ return Math.max(min, Math.min(max, n)); }

      document.querySelectorAll('[data-inc]').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          const id = btn.getAttribute('data-inc');
          const input = document.getElementById(id);
          const min = parseInt(input.min || "0", 10);
          const max = parseInt(input.max || "10", 10);
          input.value = clamp((parseInt(input.value || "0", 10) + 1), min, max);
        });
      });

      document.querySelectorAll('[data-dec]').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          const id = btn.getAttribute('data-dec');
          const input = document.getElementById(id);
          const min = parseInt(input.min || "0", 10);
          const max = parseInt(input.max || "10", 10);
          input.value = clamp((parseInt(input.value || "0", 10) - 1), min, max);
        });
      });
    })();

    (function(){
      document.querySelectorAll('[data-open]').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          const id = btn.getAttribute('data-open');
          const input = document.getElementById(id);
          if (!input) return;

          if (typeof input.showPicker === 'function') input.showPicker();
          else { input.focus(); input.click(); }
        });
      });
    })();
  </script> -->