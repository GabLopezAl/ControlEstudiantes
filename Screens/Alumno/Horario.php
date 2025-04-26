<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
require '../../PHP/Headers/Alumno.php';
?>

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