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

        // Obtener alumnos de las materias que imparte
        $queryAlumnos = "SELECT DISTINCT a.MATRICULA_ESTUDIANTE, e.NOMBRE
                 FROM asistencia a
                 INNER JOIN alumno e ON a.MATRICULA_ESTUDIANTE = e.MATRICULA
                 WHERE a.NO_COLABORADOR = ?";
        $stmtAlumnos = $conn->prepare($queryAlumnos);
        $stmtAlumnos->bind_param('i', $no_colaborador);
        $stmtAlumnos->execute();
        $resultAlumnos = $stmtAlumnos->get_result();
        $alumnos = $resultAlumnos->fetch_all(MYSQLI_ASSOC);

        // Obtener materias que imparte
        $queryMaterias = "SELECT DISTINCT m.NRC, m.NOMBRE
          FROM materia m
          WHERE m.NO_COLABORADOR = ?";
        $stmtMaterias = $conn->prepare($queryMaterias);
        $stmtMaterias->bind_param('i', $no_colaborador);
        $stmtMaterias->execute();
        $resultMaterias = $stmtMaterias->get_result();
        $materias = $resultMaterias->fetch_all(MYSQLI_ASSOC);

    ?>
    <div style="margin-top: 30px; text-align: center;">
      <form method="POST" action="">
          <label for="alumno">Seleccionar Alumno:</label>
          <select name="matricula" id="alumno" required>
              <option value="">-- Selecciona un alumno --</option>
              <?php foreach($alumnos as $alumno): ?>
                        <option value="<?= $alumno['MATRICULA_ESTUDIANTE'] ?>">
                            <?= $alumno['NOMBRE'] ?>
                        </option>
              <?php endforeach; ?>
          </select>

          <label for="materia">Seleccionar Materia:</label>
          <select name="nrc" id="materia" required>
              <option value="">-- Selecciona una materia --</option>
              <?php foreach($materias as $materia): ?>
                  <option value="<?= $materia['NRC'] ?>">
                      <?= $materia['NOMBRE'] ?>
                  </option>
              <?php endforeach; ?>
          </select>

          <br><br>
          <button type="submit" name="buscar" class="botonAsistencias">Mostrar Calificaciones</button>
          <button type="submit" name="asignar" class="botonAsistencias">Asignar Calificaciones</button>
          <button type="submit" name="editar" class="botonAsistencias">Editar Calificaciones</button>
      </form>
    </div>
    
    <?php
      // Buscar calificación
      if (isset($_POST['buscar'])) {
        $matricula = $_POST['matricula'];
        $nrc = $_POST['nrc'];

        $stmt = $conn->prepare("
            SELECT 
            a.NOMBRE AS NOMBRE_ALUMNO,
            m.NOMBRE AS NOMBRE_MATERIA,
            c.CALIFICACION
            FROM calificacion c
            INNER JOIN alumno a ON c.MATRICULA_ESTUDIANTE = a.MATRICULA
            INNER JOIN materia m ON c.NRC_MATERIA = m.NRC
            WHERE c.MATRICULA_ESTUDIANTE = ? AND c.NRC_MATERIA = ? AND c.NO_COLABORADOR = ?
            ");
        $stmt->bind_param('ssi', $matricula, $nrc, $no_colaborador);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
        //   echo "<p style='text-align:center; margin-top:20px;'>
        //     <strong>Calificación de {$row['NOMBRE_ALUMNO']} en la materia {$row['NOMBRE_MATERIA']}:</strong> {$row['CALIFICACION']}
        //     </p>";
        // } else {
        //   echo "<p style='text-align:center; margin-top:20px;'>No hay calificación registrada para este alumno en esta materia.</p>";
        // }
        echo "
            <br><br>
            <table style='margin: 20px auto; border-collapse: collapse; width: 60%; text-align: center;'>
              <thead>
                <tr style='background-color: #f2f2f2;'>
                  <th style='padding: 10px; border: 1px solid #ccc;'>Alumno</th>
                  <th style='padding: 10px; border: 1px solid #ccc;'>Materia</th>
                  <th style='padding: 10px; border: 1px solid #ccc;'>Calificación</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style='padding: 10px; border: 1px solid #ccc;'>{$row['NOMBRE_ALUMNO']}</td>
                  <td style='padding: 10px; border: 1px solid #ccc;'>{$row['NOMBRE_MATERIA']}</td>
                  <td style='padding: 10px; border: 1px solid #ccc;'>{$row['CALIFICACION']}</td>
                </tr>
              </tbody>
            </table>
          ";
        } else {
          echo "<p style='text-align:center; margin-top:20px;'>No hay calificación registrada para este alumno en esta materia.</p>";
        }
      }

      // Asignar calificación
      if (isset($_POST['asignar'])) {
        $matricula = $_POST['matricula'];
        $nrc = $_POST['nrc'];
        ?>
        <div style="text-align:center; margin-top: 40px;">
          <h2>Asignar Calificación</h2>
          <form method="POST" action="">
            <input type="hidden" name="matricula" value="<?= $matricula ?>">
            <input type="hidden" name="nrc" value="<?= $nrc ?>">
            <label for="calificacion">Calificación:</label>
            <input type="number" name="calificacion" step="0.01" min="0" max="100" required>
            <br><br>
            <button type="submit" name="guardar_calificacion" class="botonAsistencias">Guardar Calificación</button>
          </form>
        </div>
        <?php
      }

      // Guardar nueva calificación
      if (isset($_POST['guardar_calificacion'])) {
        $matricula = $_POST['matricula'];
        $nrc = $_POST['nrc'];
        $calificacion = $_POST['calificacion'];

        $query = "INSERT INTO calificacion (NRC_MATERIA, MATRICULA_ESTUDIANTE, NO_COLABORADOR, CALIFICACION)
                  VALUES (?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE CALIFICACION = VALUES(CALIFICACION)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssis', $nrc, $matricula, $no_colaborador, $calificacion);

        if ($stmt->execute()) {
          echo "<p style='text-align:center; color:green;'>¡Calificación guardada correctamente!</p>";
        } else {
          echo "<p style='text-align:center; color:red;'>Error al guardar la calificación.</p>";
        }
      }

      // Editar calificación
      if (isset($_POST['editar'])) {
        $matricula = $_POST['matricula'];
        $nrc = $_POST['nrc'];

        $stmt = $conn->prepare("SELECT CALIFICACION FROM calificacion WHERE MATRICULA_ESTUDIANTE = ? AND NRC_MATERIA = ? AND NO_COLABORADOR = ?");
        $stmt->bind_param('ssi', $matricula, $nrc, $no_colaborador);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
          ?>
          <div style="text-align:center; margin-top: 40px;">
            <h2>Editar Calificación</h2>
            <form method="POST" action="">
              <input type="hidden" name="matricula" value="<?= $matricula ?>">
              <input type="hidden" name="nrc" value="<?= $nrc ?>">
              <label for="nueva_calificacion">Nueva Calificación:</label>
              <input type="number" name="nueva_calificacion" step="0.01" min="0" max="100" value="<?= $row['CALIFICACION'] ?>" required>
              <br><br>
              <button type="submit" name="guardar_edicion" class="botonAsistencias">Guardar Cambios</button>
            </form>
          </div>
          <?php
        } else {
          echo "<p style='text-align:center; margin-top:20px;'>No hay calificación registrada para editar.</p>";
        }
      }

      // Guardar edición
      if (isset($_POST['guardar_edicion'])) {
        $matricula = $_POST['matricula'];
        $nrc = $_POST['nrc'];
        $nueva = $_POST['nueva_calificacion'];

        $stmt = $conn->prepare("UPDATE calificacion SET CALIFICACION = ? WHERE MATRICULA_ESTUDIANTE = ? AND NRC_MATERIA = ? AND NO_COLABORADOR = ?");
        $stmt->bind_param('sssi', $nueva, $matricula, $nrc, $no_colaborador);

        if ($stmt->execute()) {
          echo "<p style='text-align:center; color:green;'>¡Calificación actualizada correctamente!</p>";
        } else {
          echo "<p style='text-align:center; color:red;'>Error al actualizar la calificación.</p>";
        }
      }
    ?>
  </div>
  
  <script src="../../js/dropdown.js"></script>
</body>
</html>