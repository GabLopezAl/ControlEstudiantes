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
  <title>Horario</title>
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

         $sql = "
        SELECT 
            h.DIA_SEMANA,
            m.NOMBRE AS MATERIA,
            h.HORA_INICIO,
            h.HORA_FIN
        FROM horario h
        INNER JOIN materia m ON h.NRC_MATERIA = m.NRC
        WHERE h.MATRICULA_ESTUDIANTE = ?
        ORDER BY 
            FIELD(h.DIA_SEMANA, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'),
            h.HORA_INICIO ASC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $resultado = $stmt->get_result();

        // 4. Organizar los datos por día
        $horario = [
            'Lunes' => [],
            'Martes' => [],
            'Miércoles' => [],
            'Jueves' => [],
            'Viernes' => []
        ];

        while ($fila = $resultado->fetch_assoc()) {
            $dia = $fila['DIA_SEMANA'];
            $horario[$dia][] = [
                'MATERIA' => $fila['MATERIA'],
                'HORA_INICIO' => $fila['HORA_INICIO'],
                'HORA_FIN' => $fila['HORA_FIN']
            ];
        }
?>

  <h1 class="welcome">Horario</h1>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Día</th>
          <th>Materia</th>
          <th>HoraInicio</th>
          <th>HoraFin</th>
        </tr>
      </thead>
      <tbody>
        <?php
            foreach ($horario as $dia => $materias) {
                if (count($materias) > 0) {
                    foreach ($materias as $index => $materia) {
                        echo "<tr>";
                        // Solo mostrar el día en la primera fila
                        if ($index == 0) {
                            echo "<td rowspan='" . count($materias) . "'>" . htmlspecialchars($dia) . "</td>";
                        }
                        echo "<td>" . htmlspecialchars($materia['MATERIA']) . "</td>";
                        echo "<td>" . htmlspecialchars($materia['HORA_INICIO']) . "</td>";
                        echo "<td>" . htmlspecialchars($materia['HORA_FIN']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    // Día sin materias
                    echo "<tr><td>" . htmlspecialchars($dia) . "</td><td colspan='3'></td></tr>";
                }
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