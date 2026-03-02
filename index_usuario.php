<?php
session_start();

/**
 * Reservación de salones (PHP + HTML + CSS)
 * Step 1: Selección de salón + fechas (inicio/fin) + horas (inicio/fin) + cantidad + notas
 * Step 2: Datos personales
 * Step 3: Revisión y confirmación
 * Link para correr en el browser: http://localhost/proyecto_final/
 */

require_once __DIR__ . '/Controller/BookingController.php';

$controller = new BookingController();
$controller->handle();

?>


<!-- Cantidad de estudiantes +/- -->
<script>
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
  </script>
</body>
</html>