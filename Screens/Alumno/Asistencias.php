<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
require '../../PHP/Headers/Alumno.php';
?>


  <div class="welcome">
    Bienvenido <strong><?php echo isset($_SESSION['NOMBRE']) ? $_SESSION['NOMBRE'] : 'Usuario'; ?></strong>
  </div>

  <div class="container">
  <?php
      $matricula = $_SESSION['MATRICULA'];
      if ($conn->connect_error) {
        echo "<p>Error de conexión: " . $conn->connect_error . "</p>";
      } else {
        $query = "SELECT 
                    m.NOMBRE,
                    a.DIA_SEMANA,
                    SUM(a.ASISTENCIA) AS ASISTENCIAS_DIA,
                    m.ASISTENCIAS_TOTALES
                  FROM asistencia a
                  JOIN materia m ON a.NRC_MATERIA = m.NRC
                  WHERE a.MATRICULA_ESTUDIANTE = ?
                  GROUP BY m.NOMBRE, a.DIA_SEMANA, m.ASISTENCIAS_TOTALES
                  ORDER BY m.NOMBRE, FIELD(a.DIA_SEMANA, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes')";
      
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $resultado = $stmt->get_result();
      
        $materia_actual = '';
        while ($fila = $resultado->fetch_assoc()) {
          if ($materia_actual !== $fila['NOMBRE']) {
            if ($materia_actual !== '') echo "</table></div></div>";
            $materia_actual = $fila['NOMBRE'];
            echo "<div><div class='title'>{$materia_actual}<br><br><br></div><div class='table-wrapper'><table><tr><th>Día</th><th>Asistencias</th><th>Asistencias Totales</th></tr>";
          }
          echo "<tr><td><strong>{$fila['DIA_SEMANA']}</strong></td><td>{$fila['ASISTENCIAS_DIA']}</td><td>{$fila['ASISTENCIAS_TOTALES']}</td></tr>";
        }
      
        if ($materia_actual !== '') echo "</table></div></div>";
        $stmt->close();
        $conn->close();
      }
    ?>
  </div>
  
  <script src="../../js/dropdown.js"></script>
</body>
</html>
