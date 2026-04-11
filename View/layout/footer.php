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