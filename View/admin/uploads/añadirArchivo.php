<?php
$step = 1;
$error = '';
$success = '';
$h = fn($v) => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');

include(__DIR__ . '/../../layout/admin_header.php');
?>

<div class="upload-container">
    <h1 class="classroom-title"> Añadir Archivo</h1>

    <form action="index_admin.php?action=uploadCSV" method="POST" enctype="multipart/form-data">
        
        <label class="upload-box" for="fileInput">
            <div class="upload-content">
                <div class="icon">☁️</div>
                <p>Selecciona el archivo que va a subir</p>
                <small>archivo .csv</small>

                 <p id="fileName" class="file-name">Ningún archivo seleccionado</p>

                <button type="button" class="select-btn">Seleccione el archivo</button>
            </div>
            <input type="file" id="fileInput" name="csv_file" accept=".csv" required hidden>
        </label>

        <div class="buttons">
            <a href="index_admin.php" class="cancel-btn">Cancelar</a>
            <button type="submit" class="upload-btn">Subir</button>
        </div>

    </form>
</div>

<script>
    const fileInput = document.getElementById("fileInput");
    const selectBtn = document.querySelector(".select-btn");
    const fileNameText = document.getElementById("fileName");

    selectBtn.addEventListener("click", () => {
        fileInput.click();
    });

    // 👇 Mostrar nombre del archivo
    fileInput.addEventListener("change", () => {
        if (fileInput.files.length > 0) {
            fileNameText.textContent = fileInput.files[0].name;
        } else {
            fileNameText.textContent = "Ningún archivo seleccionado";
        }
    });

</script>
