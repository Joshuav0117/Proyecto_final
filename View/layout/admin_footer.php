</div> <!-- /.card -->

<div class="footerlinks">
  <!--
<div class="leftlinks">
    <a href="#">Admin</a>
    <a href="#">Salones</a>
    <a href="#">Usuarios</a>
</div>
-->
  <div>© <?php echo date('Y'); ?> Universidad</div>
</div>
</section>
</div>

<!-- Filtra los salones mientras el usuario escribe-->
<script>
document.addEventListener("DOMContentLoaded", function(){

  const searchInput = document.getElementById("classroomSearch");
  const noResults = document.getElementById("noResultsMessage");

  if (!searchInput) return;

  if (noResults) {
    noResults.style.display = "none";
  }

  searchInput.addEventListener("keyup", function(){

    const value = searchInput.value.toLowerCase();
    let visibleCount = 0;

    document.querySelectorAll(".classroom-box").forEach(box => {

      const name = box.querySelector(".classroom-name").textContent.toLowerCase();

      if (name.includes(value)) {
        box.style.display = "block";
        visibleCount++;
      } else {
        box.style.display = "none";
      }

    });

    // Mostrar mensaje si no hay resultados
    if (visibleCount === 0) {
      noResults.style.display = "block";
    } else {
      noResults.style.display = "none";
    }
  });
});
</script>
</body>
</html>