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
  <title>Editar</title>
  <link rel="stylesheet" href="../../css/calificacionesAlumno.css?v=1.1">
  <link rel="stylesheet" href="../../css/registroUsuarios.css?v=1.1">
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
        <a class="cursor" href="../../index.php">Cerrar sesión</a>
      </div>
    </div>
  </header>

  <?php
        $matricula = $_SESSION['MATRICULA'];
        $query = "SELECT * FROM alumno WHERE MATRICULA = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();
        $alumno = $result->fetch_assoc();
  ?>

  <h1 class="welcome">Editar Datos</h1>
  <div class="form-container">
      <form action="../../PHP/Actualizar/actualizarAlumno.php" method="POST">

          <label for="matricula">Matricula</label>
          <input type="text" id="matricula" name="matricula" value="<?= htmlspecialchars($alumno['MATRICULA']) ?>" readonly required>

          <label for="nombre">Nombre</label>
          <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($alumno['NOMBRE']) ?>" required>

          <label for="apellidoP">Apellido Paterno</label>
          <input type="text" id="apellidoP" name="apellidoP" value="<?= htmlspecialchars($alumno['APELLIDO_PATERNO']) ?>" required>

          <label for="apellidoM">Apellido Materno</label>
          <input type="text" id="apellidoM" name="apellidoM" value="<?= htmlspecialchars($alumno['APELLIDO_MATERNO']) ?>" required>

          <label for="correo">Correo</label>
          <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($alumno['CORREO']) ?>" required>

          <label for="fechaNacimiento">Fecha de nacimiento</label>
          <input type="date" id="fechaNacimiento" name="fechaNacimiento" value="<?= htmlspecialchars($alumno['FECHA_NACIMIENTO']) ?>" required>

          <label for="edad">Edad</label>
          <input type="text" id="edad" name="edad" value="<?= htmlspecialchars($alumno['EDAD']) ?>" required>

          <label for="password">Contraseña</label>
          <div style="position: relative; width: 100%;">
              <input type="password" id="password" name="password" value="<?= htmlspecialchars($alumno['CONTRASEÑA']) ?>" required style="
                width: 100%;
                padding: 10px 40px 10px 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                box-sizing: border-box;
              ">
              <span id="togglePassword" onclick="togglePassword()" style="
                position: absolute;
                right: 5px;
                top: 38%;
                transform: translateY(-50%);
                cursor: pointer;
                font-size: 20px;
              ">🔒</span>
          </div>

          <div class="button-container">
              <button type="reset" class="btn btn-clear">Borrar</button>
              <button type="submit" class="btn btn-register">Actualizar</button>
          </div>
      </form>

  </div>

  
  <script src="../../js/dropdown.js"></script>
  <script src="../../js/eyeIcon.js"></script>
</body>
</html>