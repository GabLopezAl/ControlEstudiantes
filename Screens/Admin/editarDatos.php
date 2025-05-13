<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar</title>
    <link rel="stylesheet" href="../../css/styleRegistroOpciones.css?v=1.1">
    <link rel="stylesheet" href="../../css/registroUsuarios.css?v=1.1">
    <link rel="stylesheet" href="../../css/calificacionesAlumno.css?v=2.2">

</head>

<body>
    <header>
    <div class="logo">
      <img src="../../img/Logo.png" alt="Logo">
    </div>
    <nav>
      <a href="Asistencias.php" class="<?= $pagina == 'Asistencias.php' ? 'active' : '' ?>">Asistencias</a>
      <a href="Calificaciones.php" class="<?= $pagina == 'Calificaciones.php' ? 'active' : '' ?>">Calificaciones</a>
      <a href="Horario.php" class="<?= $pagina == 'Horario.php' ? 'active' : '' ?>">Horarios</a>
      <a href="Materia.php" class="<?= $pagina == 'Materia.php' ? 'active' : '' ?>">Materias</a>
      <a href="Alumnos.php" class="<?= $pagina == 'Alumnos.php' ? 'active' : '' ?>">Alumnos</a>
      <a href="Maestros.php" class="<?= $pagina == 'Maestros.php' ? 'active' : '' ?>">Maestros</a>
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
        $id = $_SESSION['ID'];
        $query = "SELECT * FROM administrador WHERE ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
  ?>
  <h1 class="welcome">Editar Datos</h1>
    <div class="form-container">
        <form action="../../PHP/Actualizar/actualizarAdmin.php" method="POST">

            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($admin['NOMBRE']) ?>" required>

            <label for="apellidoP">Apellido Paterno</label>
            <input type="text" id="apellidoP" name="apellidoP" value="<?= htmlspecialchars($admin['APELLIDO_PATERNO']) ?>" required>

            <label for="apellidoM">Apellido Materno</label>
            <input type="text" id="apellidoM" name="apellidoM" value="<?= htmlspecialchars($admin['APELLIDO_MATERNO']) ?>" required>

            <label for="correo">Correo</label>
            <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($admin['CORREO']) ?>" required>

            <label for="fechaNacimiento">Fecha de nacimiento</label>
            <input type="date" id="fechaNacimiento" name="fechaNacimiento" value="<?= htmlspecialchars($admin['FECHA_NACIMIENTO']) ?>" required>

            <label for="password">Contraseña</label>
            <div style="position: relative; width: 100%;">
                <input type="password" id="password" name="password" value="<?= htmlspecialchars($admin['CONTRASENA']) ?>" required style="
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
    <script src="../../js/rolSeleccionado.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/eyeIcon.js"></script>
    <script src="../../js/dropdown.js"></script>
    <script src="../../js/eyeIcon.js"></script>

</body>

</html>