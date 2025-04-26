<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Asistencias</title>
  <?php if ($pagina == 'Asistencias.php'): ?>
    <link rel="stylesheet" href="../../css/asistenciasAlumno.css?v=1.1">
  <?php elseif ($pagina == 'Calificaciones.php'): ?>
      <link rel="stylesheet" href="../../css/calificacionesAlumno.css?v=1.1">
  <?php elseif ($pagina == 'Horario.php'): ?>
      <link rel="stylesheet" href="../../css/calificacionesAlumno.css?v=1.1">
  <?php endif; ?>
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