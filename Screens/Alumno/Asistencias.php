<?php session_start(); ?>
<!-- <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SCHOOL SYSTEM - Asistencias</title>

</head>
<body>
  <header>
    <div class="logo">
      <img src="../../img/Logo.png" alt="Logo">
    </div>
    <nav>
      <a href="#">Asistencias</a>
      <a href="#">Calificaciones</a>
      <a href="#">Horario</a>
    </nav>
    <div class="user-section">
      <div class="user-icon"></div>
      <div class="dropdown">
        <a href="#">Editar datos</a>
        <a href="#">Cerrar sesión</a>
      </div>
    </div>
  </header>
  <div class="welcome">
    Bienvenido <strong><?php //echo $_SESSION['NOMBRE'] ?? 'Invitado'; ?></strong>
  </div>
  <div class="container">-->
<!--<?php
    // $conexion = new mysqli('localhost', 'usuario', 'contraseña', 'base_de_datos');
    // $conexion->set_charset("utf8");

    // if ($conexion->connect_error) {
    //   echo "<p>Error de conexión: " . $conexion->connect_error . "</p>";
    // } else {
    //   $query = "SELECT materia, dia, asistencias, total FROM asistencias ORDER BY materia, FIELD(dia, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes')";
    //   $resultado = $conexion->query($query);

    //   $materia_actual = '';
    //   while($fila = $resultado->fetch_assoc()) {
    //     if ($materia_actual !== $fila['materia']) {
    //       if ($materia_actual !== '') echo "</table></div></div>";
    //       $materia_actual = $fila['materia'];
    //       echo "<div><div class='title'>{$materia_actual}</div><div class='table-wrapper'><table><tr><th>Día</th><th>Asistencias</th><th>Asistencias Totales</th></tr>";
    //     }
    //     echo "<tr><td><strong>{$fila['dia']}</strong></td><td>{$fila['asistencias']}</td><td>{$fila['total']}</td></tr>";
    //   }
    //   if ($materia_actual !== '') echo "</table></div></div>";
    //   $conexion->close();
    // }
    ?>
  </div>
</body>
</html> -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio</title>
  <link rel="stylesheet" href="../../css/asistenciasAlumno.css?v=2.2">
</head>
<body>
<header>
    <div class="logo">
      <img src="../../img/Logo.png" alt="Logo">
    </div>
    <nav>
      <a href="#">Asistencias</a>
      <a href="#">Calificaciones</a>
      <a href="#">Horario</a>
    </nav>
    <div class="user-section">
      <div>
        <img class="user-icon" src="../../img/logoUser.png" alt="Logo">
      </div>
      <div class="dropdown">
        <a href="#">Editar datos</a>
        <a href="#">Cerrar sesión</a>
      </div>
    </div>
  </header>
</body>
</html>
