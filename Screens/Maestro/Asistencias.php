<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
require '../../PHP/Headers/Maestro.php';
?>


  <div class="welcome">
    Bienvenido <strong><?php echo isset($_SESSION['NOMBRE']) ? $_SESSION['NOMBRE'] : 'Usuario'; ?></strong>
  </div>

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
        <button type="submit" name="buscar" class="botonAsistencias">Mostrar Asistencias</button>
        <button type="submit" name="asignar" class="botonAsistencias">Asignar Asistencias</button>
        <button type="submit" name="editar" class="botonAsistencias">Editar Asistencias</button>
    </form>
</div>

<?php
    if (isset($_POST['buscar'])) {
        $matricula = $_POST['matricula'];
        $nrc = $_POST['nrc'];

        // Consultar asistencias por día
        $queryAsistencias = "SELECT DIA_SEMANA, SUM(ASISTENCIA) as asistencias_dia
                            FROM asistencia
                            WHERE MATRICULA_ESTUDIANTE = ? AND NRC_MATERIA = ?
                            GROUP BY DIA_SEMANA
                            ORDER BY FIELD(DIA_SEMANA, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado')";
        $stmtAsistencias = $conn->prepare($queryAsistencias);
        $stmtAsistencias->bind_param('ss', $matricula, $nrc);
        $stmtAsistencias->execute();
        $resultAsistencias = $stmtAsistencias->get_result();
        $asistencias = $resultAsistencias->fetch_all(MYSQLI_ASSOC);

        // Buscar nombre del estudiante para mostrarlo
        $queryNombreAlumno = "SELECT NOMBRE FROM alumno WHERE MATRICULA = ?";
        $stmtNombreAlumno = $conn->prepare($queryNombreAlumno);
        $stmtNombreAlumno->bind_param('s', $matricula);
        $stmtNombreAlumno->execute();
        $resultNombreAlumno = $stmtNombreAlumno->get_result();
        $rowNombreAlumno = $resultNombreAlumno->fetch_assoc();
        $nombreAlumno = $rowNombreAlumno['NOMBRE'];

        // Buscar el nombre de la materia para mostrarlo
        $queryNombreMateria = "SELECT NOMBRE FROM materia WHERE NRC = ?";
        $stmtNombreMateria = $conn->prepare($queryNombreMateria);
        $stmtNombreMateria->bind_param('s', $nrc);
        $stmtNombreMateria->execute();
        $resultNombreMateria = $stmtNombreMateria->get_result();
        $rowNombreMateria = $resultNombreMateria->fetch_assoc();
        $nombreMateria = $rowNombreMateria['NOMBRE'];

        // Consultar asistencias totales de la materia
        $queryTotal = "SELECT ASISTENCIAS_TOTALES FROM materia WHERE NRC = ?";
        $stmtTotal = $conn->prepare($queryTotal);
        $stmtTotal->bind_param('s', $nrc);
        $stmtTotal->execute();
        $resultTotal = $stmtTotal->get_result();
        $rowTotal = $resultTotal->fetch_assoc();
        $asistenciasTotales = $rowTotal['ASISTENCIAS_TOTALES'];

        // SOLO si encontró asistencias muestra la tabla
        if ($asistencias) {
    ?>
        <div style="margin-top: 40px; text-align: center;">
            <h2>Asistencias de <?= htmlspecialchars($nombreAlumno) ?> en la materia de <?= htmlspecialchars($nombreMateria) ?></h2>
            <table style='margin: 20px auto; border-collapse: collapse; width: 60%; text-align: center;'>
                <thead>
                    <tr style='background-color: #f2f2f2;'>
                        <th style='padding: 10px; border: 1px solid #ccc;'>Día de la semana</th>
                        <th style='padding: 10px; border: 1px solid #ccc;'>Asistencias</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($asistencias as $asistencia) { ?>
                        <tr style='background-color: #f2f2f2;'>
                            <td><?= htmlspecialchars($asistencia['DIA_SEMANA']) ?></td>
                            <td><?= htmlspecialchars($asistencia['asistencias_dia']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <br>
            <p><strong>Asistencias Totales Permitidas:</strong> <?= htmlspecialchars($asistenciasTotales) ?></p>
        </div>
    <?php
        } else {
            echo "<p style='text-align:center; margin-top:20px;'>No hay asistencias registradas para este alumno en esta materia.</p>";
        }
    }

            if (isset($_POST['asignar'])) {
                $matricula = $_POST['matricula'];
                $nrc = $_POST['nrc'];
            
                // Buscar nombre del estudiante para mostrarlo
                $queryNombreAlumno = "SELECT NOMBRE FROM alumno WHERE MATRICULA = ?";
                $stmtNombreAlumno = $conn->prepare($queryNombreAlumno);
                $stmtNombreAlumno->bind_param('s', $matricula);
                $stmtNombreAlumno->execute();
                $resultNombreAlumno = $stmtNombreAlumno->get_result();
                $rowNombreAlumno = $resultNombreAlumno->fetch_assoc();
                $nombreAlumno = $rowNombreAlumno['NOMBRE'];
        ?>
    
        <div style="margin-top: 40px; text-align: center;">
            <h2>Asignar Asistencia para <?= htmlspecialchars($nombreAlumno) ?></h2>
    
            <form method="POST" action="">
                <input type="hidden" name="matricula" value="<?= htmlspecialchars($matricula) ?>">
                <input type="hidden" name="nrc" value="<?= htmlspecialchars($nrc) ?>">
    
                <label for="dia">Selecciona el día:</label>
                <select name="dia" id="dia" required>
                    <option value="">-- Selecciona un día --</option>
                    <option value="Lunes">Lunes</option>
                    <option value="Martes">Martes</option>
                    <option value="Miércoles">Miércoles</option>
                    <option value="Jueves">Jueves</option>
                    <option value="Viernes">Viernes</option>
                </select>
    
                <br><br>
    
                <label for="asistencia">Cantidad de asistencias:</label>
                <input type="number" name="asistencia" id="asistencia" min="0" required>
    
                <br><br>
    
                <button type="submit" name="guardar_asistencia" class="botonAsistencias">Guardar Asistencia</button>
            </form>
        </div>

        
    
    <?php
    }

        if (isset($_POST['guardar_asistencia'])) {
            $matricula = $_POST['matricula'];
            $nrc = $_POST['nrc'];
            $dia = $_POST['dia'];
            $asistencia = $_POST['asistencia'];
            $no_colaborador = $_SESSION['NO_COLABORADOR']; // Lo tomamos de sesión
        
            $queryInsert = "INSERT INTO asistencia (NRC_MATERIA, MATRICULA_ESTUDIANTE, NO_COLABORADOR, DIA_SEMANA, ASISTENCIA) 
                            VALUES (?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($queryInsert);
            $stmtInsert->bind_param('ssisi', $nrc, $matricula, $no_colaborador, $dia, $asistencia);
        
            if ($stmtInsert->execute()) {
                echo "<p style='text-align:center; color:green;'>¡Asistencia asignada correctamente!</p>";
            } else {
                echo "<p style='text-align:center; color:red;'>Error al asignar asistencia.</p>";
            }
        }
    
    
    

    if (isset($_POST['editar'])) {
            $matricula = $_POST['matricula'];
            $nrc = $_POST['nrc'];
        
            // Buscar nombre del estudiante para mostrarlo
            $queryNombreAlumno = "SELECT NOMBRE FROM alumno WHERE MATRICULA = ?";
            $stmtNombreAlumno = $conn->prepare($queryNombreAlumno);
            $stmtNombreAlumno->bind_param('s', $matricula);
            $stmtNombreAlumno->execute();
            $resultNombreAlumno = $stmtNombreAlumno->get_result();
            $rowNombreAlumno = $resultNombreAlumno->fetch_assoc();
            $nombreAlumno = $rowNombreAlumno['NOMBRE'];
        
            // Buscar asistencias ya registradas
            $queryAsistencias = "SELECT ID, DIA_SEMANA, ASISTENCIA 
                                 FROM asistencia 
                                 WHERE MATRICULA_ESTUDIANTE = ? AND NRC_MATERIA = ?";
            $stmtAsistencias = $conn->prepare($queryAsistencias);
            $stmtAsistencias->bind_param('ss', $matricula, $nrc);
            $stmtAsistencias->execute();
            $resultAsistencias = $stmtAsistencias->get_result();
            $asistencias = $resultAsistencias->fetch_all(MYSQLI_ASSOC);
        
            if ($asistencias) {
        ?>
        
            <div style="margin-top: 40px; text-align: center;">
                <h2>Editar Asistencias de <?= htmlspecialchars($nombreAlumno) ?></h2>
        
                <form method="POST" action="">
                    <input type="hidden" name="matricula" value="<?= htmlspecialchars($matricula) ?>">
                    <input type="hidden" name="nrc" value="<?= htmlspecialchars($nrc) ?>">
        
                    <table style='margin: 20px auto; border-collapse: collapse; width: 60%; text-align: center;'>
                        <thead>
                            <tr style='background-color: #f2f2f2;'>
                                <th style='padding: 10px; border: 1px solid #ccc;'>Día de la semana</th>
                                <th style='padding: 10px; border: 1px solid #ccc;'>Asistencias</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($asistencias as $asistencia) { ?>
                                <tr style='background-color: #f2f2f2;'>
                                    <td><?= htmlspecialchars($asistencia['DIA_SEMANA']) ?></td>
                                    <td>
                                        <input type="number" name="asistencias_editadas[<?= $asistencia['ID'] ?>]" 
                                               value="<?= htmlspecialchars($asistencia['ASISTENCIA']) ?>" min="0" required>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
        
                    <br><br>
                    <button type="submit" name="guardar_cambios" class="botonAsistencias">Guardar Cambios</button>
                </form>
            </div>
        
        <?php
            } else {
                echo "<p style='text-align:center; margin-top:20px;'>No hay asistencias registradas para este alumno en esta materia.</p>";
            }
        }

        if (isset($_POST['guardar_cambios'])) {
            $asistenciasEditadas = $_POST['asistencias_editadas'];
        
            foreach ($asistenciasEditadas as $id => $nuevaAsistencia) {
                $queryUpdate = "UPDATE asistencia SET ASISTENCIA = ? WHERE ID = ?";
                $stmtUpdate = $conn->prepare($queryUpdate);
                $stmtUpdate->bind_param('ii', $nuevaAsistencia, $id);
                $stmtUpdate->execute();
            }
        
            echo "<p style='text-align:center; color:green;'>¡Asistencias actualizadas correctamente!</p>";
        }
        
        
    
?>

  <script src="../../js/dropdown.js"></script>
</body>
</html>