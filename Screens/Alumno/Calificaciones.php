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
  <title>Calificaciones</title>
  <link rel="stylesheet" href="../../css/calificacionesAlumno.css?v=1.1">
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
        <a class="cursor" href="editarDatos.php">Editar datos</a>
        <a class="cursor" href="../../index.php">Cerrar sesión</a>
      </div>
    </div>
  </header>

  <?php
        $matricula = $_SESSION['MATRICULA'];

      // 3. Consulta para traer las materias del alumno
        $sql = "SELECT NOMBRE, CALIFICACION, CREDITOS FROM materia WHERE MATRICULA_ESTUDIANTE = ?";
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