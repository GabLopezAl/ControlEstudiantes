<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
require '../../PHP/Headers/Admin.php';
?>
<div class="centrado">
<?php
// Eliminar alumno si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['matricula'])) {
    $matricula = $_POST['matricula'];

    // Eliminar registros en todas las tablas relacionadas (orden correcto)
    $conn->query("DELETE FROM asistencia WHERE MATRICULA_ESTUDIANTE = '$matricula'");
    $conn->query("DELETE FROM calificacion WHERE MATRICULA_ESTUDIANTE = '$matricula'");
    $conn->query("DELETE FROM horario WHERE MATRICULA_ESTUDIANTE = '$matricula'");
    $conn->query("UPDATE materia SET MATRICULA_ESTUDIANTE = NULL WHERE MATRICULA_ESTUDIANTE = '$matricula'");
    $conn->query("DELETE FROM alumno WHERE MATRICULA = '$matricula'");

    echo "<p>Alumno con matrícula $matricula eliminado correctamente.</p>";
}

?>

<form method="POST" action="">
    <label for="matricula">Selecciona un alumno:</label>
    <select name="matricula" required>
        <option value="">-- Selecciona --</option>
        <?php
        $resultado = $conn->query("SELECT MATRICULA, NOMBRE, APELLIDO_PATERNO FROM alumno");
        while ($fila = $resultado->fetch_assoc()) {
            $matricula = $fila['MATRICULA'];
            $nombre = $fila['NOMBRE'] . " " . $fila['APELLIDO_PATERNO'];
            echo "<option value='$matricula'>$matricula - $nombre</option>";
        }
        ?>
    </select>
    <button type="submit" class="botonAsistencias">Eliminar</button>
</form>
</div>

  <script src="../../js/dropdown.js"></script>
</body>
</html>