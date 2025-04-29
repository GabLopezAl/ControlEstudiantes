<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
require '../../PHP/Headers/Alumno.php';
?>

  <?php
        $matricula = $_SESSION['MATRICULA'];

      // 3. Consulta para traer las materias del alumno
        $sql = "SELECT m.NOMBRE, c.CALIFICACION, m.CREDITOS 
                FROM materia m
                INNER JOIN calificacion c ON m.NRC = c.NRC_MATERIA
                WHERE c.MATRICULA_ESTUDIANTE = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $resultado = $stmt->get_result();
  ?>

<div class="contenedor-calificaciones">
  <h1 class="welcome">Calificaciones</h1>
    
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Materia</th>
          <th>Calificación</th>
          <th>Créditos</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($fila['NOMBRE']) . "</td>";
                echo "<td>" . htmlspecialchars($fila['CALIFICACION']) . "</td>";
                echo "<td>" . htmlspecialchars($fila['CREDITOS']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No tienes materias registradas.</td></tr>";
        }

        $stmt->close();
        $conn->close();
        ?>
      </tbody>
    </table>
  </div>
</div>
  
  <script src="../../js/dropdown.js"></script>
</body>
</html>