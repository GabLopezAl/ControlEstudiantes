<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
require '../../PHP/Headers/Admin.php';
?>

<div class="contenedorBotones">
  <button class="botonAsistencias" onclick="document.getElementById('formularioMateria').style.display='block'">Crear materia</button>
  <button class="botonAsistencias" onclick="document.getElementById('formularioAsignar').style.display='block'">Asignar materia</button>
  <button class="botonAsistencias" onclick="document.getElementById('formularioEliminar').style.display='block'">Eliminar materia</button>
</div>


<!-- Formulario oculto inicialmente -->
<div id="formularioMateria" style="display:none; margin-top:20px;" class="container form-wrapper">
    <form method="post">
        <label>NRC:</label><br>
        <input class="cajasTexto" type="text" name="nrc" required><br><br>

        <label>Nombre de la materia:</label><br>
        <input class="cajasTexto" type="text" name="nombre" required><br><br>

        <label>Créditos:</label><br>
        <input class="cajasTexto" type="number" name="creditos" required><br><br>

        <label>Asistencias Totales:</label><br>
        <input class="cajasTexto" type="number" name="asistencias_totales" required><br><br>
        <div class="contenedorBotones">
            <input type="submit" name="registrar_materia" value="Registrar" class="botonAsistencias">
        </div>
    </form>
</div>

<!-- Formulario para asignar maestro a materia -->
<div id="formularioAsignar" style="display:none; margin-top:20px;" class="container form-wrapper">
    <form method="post">
        <label>Seleccionar Maestro:</label><br>
        <select class="cajasTexto" name="no_colaborador" required>
            <option value="">-- Selecciona un maestro --</option>
            <?php
            $maestrosSinMateria = $conn->query("
                SELECT m.NO_COLABORADOR, m.NOMBRE 
                FROM maestro m 
            ");
            while ($row = $maestrosSinMateria->fetch_assoc()) {
                echo "<option value='{$row['NO_COLABORADOR']}'>{$row['NOMBRE']}</option>";
            }
            ?>
        </select><br><br>

        <label>Seleccionar Materia:</label><br>
        <select class="cajasTexto" name="nrc" required>
            <option value="">-- Selecciona una materia --</option>
            <?php
            $materiasSinMaestro = $conn->query("
                SELECT NRC, NOMBRE FROM materia 
                WHERE NO_COLABORADOR IS NULL
            ");
            while ($row = $materiasSinMaestro->fetch_assoc()) {
                echo "<option value='{$row['NRC']}'>{$row['NOMBRE']}</option>";
            }
            ?>
        </select><br><br>

        <div class="contenedorBotones">
            <input type="submit" name="asignar_materia" value="Asignar" class="botonAsistencias">
        </div>
    </form>
</div>

<!-- Formulario para eliminar materia -->
<div id="formularioEliminar" style="display:none; margin-top:20px;" class="container form-wrapper">
    <form method="post">
        <label>Seleccionar Materia a Eliminar:</label><br>
        <select class="cajasTexto" name="nrc_eliminar" required>
            <option value="">-- Selecciona una materia --</option>
            <?php
            $materiasTodas = $conn->query("SELECT NRC, NOMBRE FROM materia");
            while ($row = $materiasTodas->fetch_assoc()) {
                echo "<option value='{$row['NRC']}'>{$row['NOMBRE']}</option>";
            }
            ?>
        </select><br><br>

        <div class="contenedorBotones">
            <input type="submit" name="eliminar_materia" value="Eliminar" class="botonAsistencias">
        </div>
    </form>
</div>


<?php
// Procesar el formulario si se envió
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if(isset($_POST['registrar_materia'])){
        $nrc = $_POST['nrc'];
        $nombre = $_POST['nombre'];
        $creditos = $_POST['creditos'];
        $asistencias_totales = $_POST['asistencias_totales'];
        $no_colaborador = NULL;

        $query = "INSERT INTO materia (NRC, NO_COLABORADOR, NOMBRE, CREDITOS, ASISTENCIAS_TOTALES) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssid", $nrc, $no_colaborador, $nombre, $creditos, $asistencias_totales);

        if ($stmt->execute()) {
            echo "<p style='color: green; text-align: center;'>¡Materia registrada correctamente!</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>Error al registrar la materia: " . $stmt->error . "</p>";
        }
    }

    if (isset($_POST['asignar_materia'])) {
        $nrc = $_POST['nrc'];
        $no_colaborador = $_POST['no_colaborador'];

        $update = $conn->prepare("UPDATE materia SET NO_COLABORADOR = ? WHERE NRC = ?");
        $update->bind_param("ss", $no_colaborador, $nrc);

        if ($update->execute()) {
            echo "<p style='color: green; text-align: center;'>¡Materia asignada correctamente!</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>Error al asignar la materia: " . $update->error . "</p>";
        }
    }

    if (isset($_POST['eliminar_materia'])) {
        $nrc = $_POST['nrc_eliminar'];

        // Primero elimina relaciones dependientes
        $conn->begin_transaction();

        try {
            // Borra de asistencia
            $stmt1 = $conn->prepare("DELETE FROM asistencia WHERE NRC_MATERIA = ?");
            $stmt1->bind_param("s", $nrc);
            $stmt1->execute();

            // Borra de horario
            $stmt2 = $conn->prepare("DELETE FROM horario WHERE NRC_MATERIA = ?");
            $stmt2->bind_param("s", $nrc);
            $stmt2->execute();

            // Borra de calificacion
            $stmt3 = $conn->prepare("DELETE FROM calificacion WHERE NRC_MATERIA = ?");
            $stmt3->bind_param("s", $nrc);
            $stmt3->execute();

            // Finalmente, borra de materia
            $stmt4 = $conn->prepare("DELETE FROM materia WHERE NRC = ?");
            $stmt4->bind_param("s", $nrc);
            $stmt4->execute();

            $conn->commit();
            echo "<p style='color: green; text-align: center;'>¡Materia y registros relacionados eliminados correctamente!</p>";

        } catch (Exception $e) {
            $conn->rollback();
            echo "<p style='color: red; text-align: center;'>Error al eliminar: {$e->getMessage()}</p>";
        }
    }



}
?>

  <script src="../../js/dropdown.js"></script>
  <script src="../../js/ocultarMensaje.js"></script>
</body>
</html>