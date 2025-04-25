<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio</title>
  <link rel="stylesheet" href="../../css/asistenciasAlumno.css?v=1.1">
</head>
<body>
<header>
    <div class="logo">
      <img src="../../img/Logo.png" alt="Logo">
    </div>
    <nav>
      <a href="Asistencias.php" class="<?= $pagina == 'Asistencias.php' ? 'active' : '' ?>">Asistencias</a>
      <a href="Calificaciones.php" class="<?= $pagina == 'Calificaciones.php' ? 'active' : '' ?>">Calificaciones</a>
      <a href="Horario.php" class="<?= $pagina == 'Horario.php' ? 'active' : '' ?>">Horario</a>
    </nav>
    <div class="user-section" id="userToggle">
      <div>
        <a>
          <img class="user-icon" src="../../img/logoUser.png" alt="Logo">
        </a>
      </div>
      <div class="dropdown" id="dropdownMenu">
        <a class="cursor" href="editar_datos.php">Editar datos</a>
        <a class="cursor" href="../../index.php">Cerrar sesión</a>
      </div>
    </div>
  </header>
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
                    h.DIA_SEMANA,
                    SUM(h.ASISTENCIAS) AS ASISTENCIAS_DIA,
                    mat.ASISTENCIAS_TOTALES
                  FROM horario h
                  JOIN materia m ON h.NRC_MATERIA = m.NRC
                  JOIN alumno a ON h.MATRICULA_ESTUDIANTE = a.MATRICULA
                  JOIN maestro ma ON h.NO_COLABORADOR = ma.NO_COLABORADOR
                  JOIN materia mat ON h.NRC_MATERIA = mat.NRC AND h.MATRICULA_ESTUDIANTE = mat.MATRICULA_ESTUDIANTE
                  WHERE h.MATRICULA_ESTUDIANTE = ?
                  GROUP BY m.NOMBRE, h.DIA_SEMANA, mat.ASISTENCIAS_TOTALES
                  ORDER BY m.NOMBRE, FIELD(h.DIA_SEMANA, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes')";
      
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
