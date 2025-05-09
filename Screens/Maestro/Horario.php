<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
require '../../PHP/Headers/Maestro.php';
?>

<div class="container">
  <?php
  // Suponiendo que tienes en sesión el NO_COLABORADOR (profesor) que está logueado:
  $no_colaborador = $_SESSION['NO_COLABORADOR']; 

  // Obtener el horario de las materias que imparte el colaborador
  $stmt = $conn->prepare("
      SELECT 
          m.NOMBRE AS NOMBRE_MATERIA,
          h.DIA_SEMANA,
          h.HORA_INICIO,
          h.HORA_FIN
      FROM horario h
      INNER JOIN materia m ON h.NRC_MATERIA = m.NRC
      WHERE h.NO_COLABORADOR = ?
      ORDER BY m.NOMBRE, 
              FIELD(h.DIA_SEMANA, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'), 
              h.HORA_INICIO
  ");
  $stmt->bind_param("i", $no_colaborador);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      echo "<h2 style='text-align:center;'>Horario de materias</h2>";
      
      // Agrupar las materias por nombre
      $materiaActual = "";
      while ($row = $result->fetch_assoc()) {
          if ($row['NOMBRE_MATERIA'] != $materiaActual) {
              // Mostrar el nombre de la nueva materia
              if ($materiaActual != "") {
                  echo "</table><br>";
              }
              echo "<br><br><h3 style='text-align:center;'>" . $row['NOMBRE_MATERIA'] . "</h3>";
              echo "<table style='margin: 20px auto; border-collapse: collapse; width: 80%; text-align: center;'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='padding: 10px; border: 1px solid #ccc;'>Día</th>
                                <th style='padding: 10px; border: 1px solid #ccc;'>Hora Inicio</th>
                                <th style='padding: 10px; border: 1px solid #ccc;'>Hora Fin</th>
                            </tr>
                        </thead>
                        <tbody>";
              $materiaActual = $row['NOMBRE_MATERIA'];
          }

          echo "<tr>
                  <td style='padding: 10px; border: 1px solid #ccc;'>{$row['DIA_SEMANA']}</td>
                  <td style='padding: 10px; border: 1px solid #ccc;'>{$row['HORA_INICIO']}</td>
                  <td style='padding: 10px; border: 1px solid #ccc;'>{$row['HORA_FIN']}</td>
                </tr>";
      }
      echo "</tbody></table>";
  } else {
      echo "<p style='text-align:center; margin-top:20px;'>No hay horario registrado para este colaborador.</p>";
  }
  ?>
</div>

<script src="../../js/dropdown.js"></script>
</body>
</html>
