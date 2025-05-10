<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
require '../../PHP/Headers/Admin.php';
?>

<?php
// Obtener alumnos
$queryAlumnos = "SELECT DISTINCT MATRICULA, NOMBRE FROM alumno";
$resAlumnos = mysqli_query($conn, $queryAlumnos);
$alumnos = [];
while ($row = mysqli_fetch_assoc($resAlumnos)) {
    $alumnos[] = $row;
}

$materias = [];
if (isset($_POST['matricula']) && !empty($_POST['matricula'])) {
    $matricula = $_POST['matricula'];
    $queryMaterias = "SELECT DISTINCT m.NRC, m.NOMBRE 
                      FROM materia m
                      INNER JOIN calificacion c ON c.NRC_MATERIA = m.NRC
                      WHERE c.MATRICULA_ESTUDIANTE = ?";
    $stmtMaterias = $conn->prepare($queryMaterias);
    $stmtMaterias->bind_param("s", $matricula);
    $stmtMaterias->execute();
    $resMaterias = $stmtMaterias->get_result();
    while ($row = $resMaterias->fetch_assoc()) {
        $materias[] = $row;
    }
}
?>

<div style="margin-top: 30px; text-align: center;">
    <form method="POST" action="">
        <label for="alumno">Seleccionar Alumno:</label>
        <select name="matricula" id="alumno" onchange="this.form.submit()" required>
            <option value="">-- Selecciona un alumno --</option>
            <?php foreach($alumnos as $alumno): ?>
                <option value="<?= $alumno['MATRICULA'] ?>" <?= (isset($_POST['matricula']) && $_POST['matricula'] == $alumno['MATRICULA']) ? 'selected' : '' ?>>
                    <?= $alumno['NOMBRE'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="materia">Seleccionar Materia:</label>
        <select name="nrc" id="materia" required>
            <option value="">-- Selecciona una materia --</option>
            <?php foreach($materias as $materia): ?>
                <option value="<?= $materia['NRC'] ?>" <?= (isset($_POST['nrc']) && $_POST['nrc'] == $materia['NRC']) ? 'selected' : '' ?>>
                    <?= $materia['NOMBRE'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>
        <button type="submit" name="mostrar" class="botonAsistencias">Mostrar Calificación</button>
    </form>
</div>


 <?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['mostrar'])) {
    $matricula = $_POST['matricula'];
    $nrc = $_POST['nrc'];

    $query = "SELECT CALIFICACION 
              FROM calificacion 
              WHERE MATRICULA_ESTUDIANTE = ? AND NRC_MATERIA = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $matricula, $nrc);
    $stmt->execute();
    $res = $stmt->get_result();

    echo "<div style='text-align: center; margin-top: 30px;'>";

    if ($row = $res->fetch_assoc()) {
        echo "<h2>Calificación: {$row['CALIFICACION']}</h2>";
    } else {
        echo "<h2>No se encontró calificación para esa materia.</h2>";
    }

    echo "</div>";
}
?>



  <script src="../../js/dropdown.js"></script>
</body>
</html>