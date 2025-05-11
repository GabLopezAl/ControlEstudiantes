<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
require '../../PHP/Headers/Admin.php';
?>
<div class="welcome">
    Bienvenido <strong><?php echo isset($_SESSION['NOMBRE']) ? $_SESSION['NOMBRE'] : 'Usuario'; ?></strong>
  </div>

  <?php
  // Obtener alumnos
  $queryAlumnos = "SELECT DISTINCT MATRICULA, NOMBRE FROM alumno"; // Ajusta "alumnos" si tu tabla se llama diferente
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
                      INNER JOIN asistencia a ON a.NRC_MATERIA = m.NRC 
                      WHERE a.MATRICULA_ESTUDIANTE = ?";
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
            <select name="matricula" id="alumno" required onchange="this.form.submit()">
                <option value="">-- Selecciona un alumno --</option>
                <?php foreach($alumnos as $alumno): ?>
                    <option value="<?= $alumno['MATRICULA'] ?>" <?= isset($_POST['matricula']) && $_POST['matricula'] == $alumno['MATRICULA'] ? 'selected' : '' ?>>
                        <?= $alumno['NOMBRE'] ?>
                    </option>

                <?php endforeach; ?>
            </select>

            <label for="materia">Seleccionar Materia:</label>
            <select name="nrc" id="materia" required>
                <option value="">-- Selecciona una materia --</option>
                <?php foreach($materias as $materia): ?>
                    <option value="<?= $materia['NRC'] ?>" <?= isset($_POST['nrc']) && $_POST['nrc'] == $materia['NRC'] ? 'selected' : '' ?>>
                        <?= $materia['NOMBRE'] ?>
                    </option>

                <?php endforeach; ?>
            </select>

            <br><br>
            <button type="submit" name="buscar" class="botonAsistencias">Mostrar Asistencias</button>
        </form>
  </div>

  <?php
      if ($_SERVER["REQUEST_METHOD"] === "POST") {
          $matricula = $_POST['matricula'];
          $nrc = $_POST['nrc'];

          if (isset($_POST['buscar'])) {

            $query = "SELECT DIA_SEMANA, SUM(ASISTENCIA) AS ASISTENCIAS 
                        FROM asistencia 
                        WHERE MATRICULA_ESTUDIANTE = ? 
                        AND NRC_MATERIA = ? 
                        GROUP BY DIA_SEMANA";
              $stmt = $conn->prepare($query);
              $stmt->bind_param("ss", $matricula, $nrc);
              $stmt->execute();
              $resultado = $stmt->get_result();
 
              // Obtener asistencias totales de la materia
              $queryTotal = "SELECT ASISTENCIAS_TOTALES FROM materia WHERE NRC = ?";
              $stmtTotal = $conn->prepare($queryTotal);
              $stmtTotal->bind_param("s", $nrc);
              $stmtTotal->execute();
              $resTotal = $stmtTotal->get_result();
              $totalAsistencias = $resTotal->fetch_assoc()['ASISTENCIAS_TOTALES'];

              echo "<div style='text-align: center; margin-top: 30px;'>";
              echo "<h2>Asistencias por día</h2>";
              echo "<table border='1' style='margin: auto;'>
                      <tr><th>Día</th><th>Asistencias</th><th>Total Posibles</th></tr>";
                      
              while ($row = $resultado->fetch_assoc()) {
                  echo "<tr>
                          <td>{$row['DIA_SEMANA']}</td>
                          <td>{$row['ASISTENCIAS']}</td>
                          <td>{$totalAsistencias}</td>
                        </tr>";
              }
              echo "</table></div>";
          }

      }
   ?>


  <script src="../../js/dropdown.js"></script>
</body>
</html>
