
<link rel="stylesheet" href="assets/css/admin.css">

<h1 class="classroom-title"> Editar datos antes de guardar</h1>
<form action="index_admin.php?action=saveCSV" method="POST">
<table class="styled-table">
    <thead>
        <tr>
            <?php foreach ($fieldMap as $label => $name): ?>
                <th><?= $label ?></th>
            <?php endforeach; ?>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $index => $row): ?>

        <?php
        $hasError = isset($errors[$index+1]);
        $hasEmpty = in_array('', $row);

        if ($hasError) {
            $color = "#f53535"; // rojo suave
            $status = "❌ Error";
        } elseif ($hasEmpty) {
            $color = "#e2d240"; // amarillo suave
            $status = "⚠ Check";
        } else {
            $color = "#3ad63a"; // verde suave
            $status = "✔ OK";
        }
        ?>

        <tr style="background-color: <?= $color ?>">
            <?php foreach ($fieldMap as $label => $name): ?>
                <td>
                    <input type="text" name="data[<?= $index ?>][<?= $name ?>]" value="<?= $row[$name] ?>" class="table-input">
                </td>
            <?php endforeach; ?>
            <td><?= $status ?></td>
        </tr>

        <?php endforeach; ?>
    </tbody>
</table>

<button type="submit" class="save-btn">Guardar archivo</button>
</form>