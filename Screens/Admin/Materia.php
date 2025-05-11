<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
require '../../PHP/Headers/Admin.php';
?>

<!-- Botón para mostrar el formulario -->
<button class="botonAsistencias" onclick="document.getElementById('formularioMateria').style.display='block'">Crear materia</button>
<button class="botonAsistencias">Asignar materia</button>

<!-- Formulario oculto inicialmente -->
<div id="formularioMateria" style="display:none; margin-top:20px;">
    <form method="post">
        <label>NRC:</label><br>
        <input class="cajasTexto" type="text" name="nrc" required><br><br>

        <label>Nombre de la materia:</label><br>
        <input class="cajasTexto" type="text" name="nombre" required><br><br>

        <label>Créditos:</label><br>
        <input class="cajasTexto" type="number" name="creditos" required><br><br>

        <label>Asistencias Totales:</label><br>
        <input class="cajasTexto" type="number" name="asistencias_totales" required><br><br>

        <input type="submit" name="registrar_materia" value="Registrar materia" class="botonAsistencias">
    </form>
</div>
<?php
// Procesar el formulario si se envió
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['registrar_materia'])) {
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
        echo "<p>Materia registrada correctamente.</p>";
    } else {
        echo "<p>Error al registrar la materia: " . $stmt->error . "</p>";
    }
}
?>

  <script src="../../js/dropdown.js"></script>
  <script src="../../js/ocultarMensaje.js"></script>
</body>
</html>