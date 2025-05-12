<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
require '../../PHP/Headers/Admin.php';
?>
<div class="centrado">
<?php
// Eliminar alumno si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['no_colaborador'])) {
    $no_col = $_POST['no_colaborador'];

    // Eliminar registros en todas las tablas relacionadas (orden correcto)
    $conn->query("DELETE FROM asistencia WHERE NO_COLABORADOR = '$no_col'");
    $conn->query("DELETE FROM calificacion WHERE NO_COLABORADOR = '$no_col'");
    $conn->query("DELETE FROM horario WHERE NO_COLABORADOR = '$no_col'");
    $conn->query("UPDATE materia SET NO_COLABORADOR = NULL WHERE NO_COLABORADOR = '$no_col'");
    $conn->query("DELETE FROM maestro WHERE NO_COLABORADOR = '$no_col'");

    echo "<p>Maestro con matrícula $no_col eliminado correctamente.</p>";
}

?>

<form method="POST" action="">
    <label for="no_colaborador">Selecciona un maestro:</label>
    <select name="no_colaborador" required>
        <option value="">-- Selecciona --</option>
        <?php
        $resultado = $conn->query("SELECT NO_COLABORADOR, NOMBRE, APELLIDO_PATERNO FROM maestro");
        while ($fila = $resultado->fetch_assoc()) {
            $no_col = $fila['NO_COLABORADOR'];
            $nombre = $fila['NOMBRE'] . " " . $fila['APELLIDO_PATERNO'];
            echo "<option value='$no_col'>$no_col - $nombre</option>";
        }
        ?>
    </select>
    <button type="submit" class="botonAsistencias">Eliminar</button>
</form>
</div>

  <script src="../../js/dropdown.js"></script>
</body>
</html>