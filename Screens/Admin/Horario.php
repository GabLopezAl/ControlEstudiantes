<?php 
session_start(); 
$pagina = basename($_SERVER['PHP_SELF']);
require '../../PHP/Conexion/conexion.php';
require '../../PHP/Headers/Admin.php';
require '../../PHP/verificaMaestro.php';
?>

<?php
// Obtener alumnos
$queryAlumnos = "SELECT DISTINCT MATRICULA, NOMBRE FROM alumno";
$resAlumnos = mysqli_query($conn, $queryAlumnos);
$alumnos = [];
while ($row = mysqli_fetch_assoc($resAlumnos)) {
    $alumnos[] = $row;
}
?>

<div style="margin-top: 30px; text-align: center;">
    <form method="POST" action="">
        <label for="alumno">Seleccionar Alumno:</label>
        <select name="matricula" id="alumno" required>
            <option value="">-- Selecciona un alumno --</option>
            <?php foreach($alumnos as $alumno): ?>
                <option value="<?= $alumno['MATRICULA'] ?>" <?= (isset($_POST['matricula']) && $_POST['matricula'] == $alumno['MATRICULA']) ? 'selected' : '' ?>>
                    <?= $alumno['NOMBRE'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <button type="submit" name="mostrar" class="botonAsistencias">Mostrar Horarios</button>
        <button type="submit" name="editar" class="botonAsistencias">Editar Horarios</button>
        <button type="submit" name="asignar" class="botonAsistencias">Asignar Horarios</button>
    </form>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if(isset($_POST['mostrar'])){
        $matricula = $_POST['matricula'];

        $query = "SELECT h.DIA_SEMANA, h.HORA_INICIO, h.HORA_FIN, m.NOMBRE AS NOMBRE_MATERIA
                FROM horario h
                JOIN materia m ON h.NRC_MATERIA = m.NRC
                WHERE h.MATRICULA_ESTUDIANTE = ?
                ORDER BY 
                    FIELD(h.DIA_SEMANA, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'),
                    h.HORA_INICIO";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<div style='text-align: center; margin-top: 30px;'>";
        echo "<h2>Horario del Alumno</h2>";

        if ($result->num_rows > 0) {
            echo "<table border='1' style='margin: auto;'>
                    <tr>
                        <th>Día</th>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                        <th>Materia</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['DIA_SEMANA']}</td>
                        <td>{$row['HORA_INICIO']}</td>
                        <td>{$row['HORA_FIN']}</td>
                        <td>{$row['NOMBRE_MATERIA']}</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay horario asignado para este alumno.</p>";
        }

        echo "</div>";
    } 
    
    if (isset($_POST['editar'])){
        $matricula = $_POST['matricula'];

        $query = "SELECT ID, DIA_SEMANA, HORA_INICIO, HORA_FIN, NRC_MATERIA 
                FROM horario 
                WHERE MATRICULA_ESTUDIANTE = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            echo "<form method='POST'>";
            echo "<input type='hidden' name='matricula' value='$matricula'>";
            echo "<table border='1' style='margin: auto; margin-top: 20px;'>
                    <tr><th>Día</th><th>Hora Inicio</th><th>Hora Fin</th><th>Materia (NRC)</th></tr>";

            while ($row = $resultado->fetch_assoc()) {
                echo "<tr>
                        <td><input type='text' name='dia_{$row['ID']}' value='{$row['DIA_SEMANA']}' readonly></td>
                        <td><input type='time' name='inicio_{$row['ID']}' value='{$row['HORA_INICIO']}'></td>
                        <td><input type='time' name='fin_{$row['ID']}' value='{$row['HORA_FIN']}'></td>
                        <td><input type='text' name='nrc_{$row['ID']}' value='{$row['NRC_MATERIA']}' readonly></td>
                        <input type='hidden' name='ids[]' value='{$row['ID']}'>
                    </tr>";
            }

            echo "</table><br>";
            echo "<div style='text-align: center; margin-top: 20px;'>";
            echo "<button type='submit' name='guardar_edicion' class='botonAsistencias'>Guardar Cambios</button>";
            echo "</div>";
            echo "</form>";
        } else {
            echo "<p style='text-align: center; margin-top: 20px;'>No se encontró horario para este alumno.</p>";
        }

    } 

    if (isset($_POST['guardar_edicion'])) {
            foreach ($_POST['ids'] as $id) {
                $dia = $_POST["dia_$id"];
                $inicio = $_POST["inicio_$id"];
                $fin = $_POST["fin_$id"];
                $nrc = $_POST["nrc_$id"];

                $update = "UPDATE horario 
                        SET DIA_SEMANA = ?, HORA_INICIO = ?, HORA_FIN = ?, NRC_MATERIA = ? 
                        WHERE ID = ?";
                $stmt = $conn->prepare($update);
                $stmt->bind_param("ssssi", $dia, $inicio, $fin, $nrc, $id);
                $stmt->execute();
            }

            echo "<p style='color: green; text-align: center;'>'Cambios guardados correctamente.'</p>";
    }

    if (isset($_POST['asignar'])) {
        $matricula = $_POST['matricula'];

        // ✅ Obtener todas las materias sin excluir ninguna
        $materiasQuery = "
            SELECT m.NRC, m.NOMBRE, m.NO_COLABORADOR, ma.NOMBRE AS NOMBRE_MAESTRO
            FROM materia m
            LEFT JOIN maestro ma ON m.NO_COLABORADOR = ma.NO_COLABORADOR
        ";

        $materiasResult = $conn->query($materiasQuery);

        // Obtener todos los maestros
        $maestrosQuery = "SELECT NO_COLABORADOR, NOMBRE FROM maestro";
        $maestrosResult = $conn->query($maestrosQuery);

        // Para el JS
        $materiasJS = [];
        $maestros = [];
        while ($row = $maestrosResult->fetch_assoc()) {
            $maestros[] = $row;
        }

        echo "<div style='display: flex; justify-content: center; margin-top: 30px;'>";
        echo "<form method='POST' style='text-align: center;'>";

        echo "<input type='hidden' name='matricula' value='{$matricula}'>";

        // === SELECT MATERIA ===
        echo "<label for='nrc'>Seleccionar Materia:</label><br>";
        echo "<select name='nrc' id='nrcSelect' required style='margin-bottom: 15px;'>";

        while ($row = $materiasResult->fetch_assoc()) {
            $materiasJS[] = [
                "nrc" => $row["NRC"],
                "nombre" => $row["NOMBRE"],
                "maestro_id" => $row["NO_COLABORADOR"],
                "maestro_nombre" => $row["NOMBRE_MAESTRO"]
            ];
            echo "<option value='{$row["NRC"]}'>{$row["NOMBRE"]}</option>";
        }
        echo "</select><br><br>";

        // === SELECT MAESTRO ===
        echo "<label for='maestro'>Seleccionar Maestro:</label><br>";
        echo "<select name='maestro' id='maestroSelect' required style='margin-bottom: 15px;'>";
        foreach ($maestros as $m) {
            echo "<option value='{$m["NO_COLABORADOR"]}'>{$m["NOMBRE"]}</option>";
        }
        echo "</select><br><br>";

        // === HORARIO ===
        echo "<label for='dia'>Día de la Semana:</label><br>";
        echo "<input type='text' name='dia' required style='margin-bottom: 15px;'><br>";

        echo "<label for='hora_inicio'>Hora de Inicio:</label><br>";
        echo "<input type='time' name='hora_inicio' required style='margin-bottom: 15px;'><br>";

        echo "<label for='hora_fin'>Hora de Fin:</label><br>";
        echo "<input type='time' name='hora_fin' required style='margin-bottom: 15px;'><br>";

        echo "<button type='submit' name='guardar_asignacion' class='botonAsistencias'>Asignar Horario</button>";

        echo "</form>";
        echo "</div>";

        // === JS para actualizar maestro automáticamente ===
        echo "<script>
            const materias = " . json_encode($materiasJS) . ";
            const maestroSelect = document.getElementById('maestroSelect');
            const nrcSelect = document.getElementById('nrcSelect');

            function actualizarMaestro() {
                const nrcSeleccionado = nrcSelect.value;
                const materia = materias.find(m => m.nrc === nrcSeleccionado);

                if (materia && materia.maestro_id) {
                    maestroSelect.value = materia.maestro_id;
                    maestroSelect.readOnly = true; // o simplemente deja que lo seleccione sin editar si ya está asignado
                } else {
                    maestroSelect.disabled = false;
                    maestroSelect.selectedIndex = 0;
                }
            }

            nrcSelect.addEventListener('change', actualizarMaestro);
            window.onload = actualizarMaestro;
        </script>";
    }

    // if (isset($_POST['asignar'])) {
    //     $matricula = $_POST['matricula'];

    //     // Materias no asignadas al alumno
    //     $materiasQuery = "
    //         SELECT m.NRC, m.NOMBRE, m.NO_COLABORADOR, ma.NOMBRE AS NOMBRE_MAESTRO
    //         FROM materia m
    //         LEFT JOIN maestro ma ON m.NO_COLABORADOR = ma.NO_COLABORADOR
    //         WHERE m.NRC NOT IN (
    //             SELECT NRC_MATERIA 
    //             FROM horario 
    //             WHERE MATRICULA_ESTUDIANTE = ?
    //         )
    //     ";

    //     $stmtMaterias = $conn->prepare($materiasQuery);
    //     $stmtMaterias->bind_param("s", $matricula);
    //     $stmtMaterias->execute();
    //     $materiasResult = $stmtMaterias->get_result();

    //     // Todos los maestros para el select (si es necesario)
    //     $maestrosQuery = "SELECT NO_COLABORADOR, NOMBRE FROM maestro";
    //     $maestrosResult = $conn->query($maestrosQuery);

    //     // Guardar en arrays para JS
    //     $materiasJS = [];
    //     $maestros = [];
    //     while ($row = $maestrosResult->fetch_assoc()) {
    //         $maestros[] = $row;
    //     }

    //     echo "<div style='display: flex; justify-content: center; margin-top: 30px;'>";
    //     echo "<form method='POST' style='text-align: center;'>";

    //     echo "<input type='hidden' name='matricula' value='{$matricula}'>";

    //     // === SELECT MATERIA ===
    //     echo "<label for='nrc'>Seleccionar Materia:</label><br>";
    //     echo "<select name='nrc' id='nrcSelect' required style='margin-bottom: 15px;'>";

    //     while ($row = $materiasResult->fetch_assoc()) {
    //         $materiasJS[] = [
    //             "nrc" => $row["NRC"],
    //             "nombre" => $row["NOMBRE"],
    //             "maestro_id" => $row["NO_COLABORADOR"],
    //             "maestro_nombre" => $row["NOMBRE_MAESTRO"]
    //         ];
    //         echo "<option value='{$row["NRC"]}'>{$row["NOMBRE"]}</option>";
    //     }
    //     echo "</select><br><br>";

    //     // === SELECT MAESTRO ===
    //     echo "<label for='maestro'>Seleccionar Maestro:</label><br>";
    //     echo "<select name='maestro' id='maestroSelect' required style='margin-bottom: 15px;'>";
    //     foreach ($maestros as $m) {
    //         echo "<option value='{$m["NO_COLABORADOR"]}'>{$m["NOMBRE"]}</option>";
    //     }
    //     echo "</select><br><br>";

    //     // === CAMPOS DE HORARIO ===
    //     echo "<label for='dia'>Día de la Semana:</label><br>";
    //     echo "<input type='text' name='dia' required style='margin-bottom: 15px;'><br>";

    //     echo "<label for='hora_inicio'>Hora de Inicio:</label><br>";
    //     echo "<input type='time' name='hora_inicio' required style='margin-bottom: 15px;'><br>";

    //     echo "<label for='hora_fin'>Hora de Fin:</label><br>";
    //     echo "<input type='time' name='hora_fin' required style='margin-bottom: 15px;'><br>";

    //     echo "<button type='submit' name='guardar_asignacion' class='botonAsistencias'>Asignar Horario</button>";

    //     echo "</form>";
    //     echo "</div>";

    //     // === JS PARA ACTUALIZAR MAESTRO AUTOMÁTICAMENTE ===
    //     echo "<script>
    //         const materias = " . json_encode($materiasJS) . ";
    //         const maestros = " . json_encode($maestros) . ";

    //         const maestroSelect = document.getElementById('maestroSelect');
    //         const nrcSelect = document.getElementById('nrcSelect');

    //         function actualizarMaestro() {
    //             const nrcSeleccionado = nrcSelect.value;
    //             const materia = materias.find(m => m.nrc === nrcSeleccionado);

    //             if (materia && materia.maestro_id) {
    //                 // Ya tiene maestro asignado
    //                 maestroSelect.value = materia.maestro_id;
    //                 maestroSelect.disabled = true;
    //             } else {
    //                 // Sin maestro, permitir seleccionar
    //                 maestroSelect.disabled = false;
    //                 maestroSelect.selectedIndex = 0;
    //             }
    //         }

    //         nrcSelect.addEventListener('change', actualizarMaestro);
    //         window.onload = actualizarMaestro;
    //     </script>";
    // }


    if (isset($_POST['guardar_asignacion'])) {
        $maestro = $_POST['maestro'] ?? null;
        $dia = $_POST['dia'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];
        $nrc = $_POST['nrc'];
        $matricula = $_POST['matricula'];

        if ($maestro) {
            // Insertar en horario
            $insert = "INSERT INTO horario (DIA_SEMANA, HORA_INICIO, HORA_FIN, MATRICULA_ESTUDIANTE, NO_COLABORADOR, NRC_MATERIA)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert);
            $stmt->bind_param("ssssss", $dia, $hora_inicio, $hora_fin, $matricula, $maestro, $nrc);
            $stmt->execute();

            // Insertar en calificacion SOLO SI no existe
            $check = $conn->prepare("SELECT 1 FROM calificacion WHERE NRC_MATERIA = ? AND MATRICULA_ESTUDIANTE = ? AND NO_COLABORADOR = ?");
            $check->bind_param("sss", $nrc, $matricula, $maestro);
            $check->execute();
            $check->store_result();
            if ($check->num_rows === 0) {
                $insertCal = $conn->prepare("INSERT INTO calificacion (NRC_MATERIA, MATRICULA_ESTUDIANTE, NO_COLABORADOR, CALIFICACION) VALUES (?, ?, ?, NULL)");
                $insertCal->bind_param("sss", $nrc, $matricula, $maestro);
                $insertCal->execute();
            }

            // Insertar SIEMPRE en asistencia con 0 asistencias (permitiendo edición posterior)
            $insertAsist = $conn->prepare("INSERT INTO asistencia (NRC_MATERIA, MATRICULA_ESTUDIANTE, NO_COLABORADOR, DIA_SEMANA, ASISTENCIA)
                                        VALUES (?, ?, ?, ?, 0)");
            $insertAsist->bind_param("ssss", $nrc, $matricula, $maestro, $dia);
            $insertAsist->execute();

            echo "<p style='color: green; text-align: center;'>Horario registrado correctamente.</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>Error: Maestro no definido correctamente.</p>";
        }
    }



}
?>

  <script src="../../js/dropdown.js"></script>
</body>
</html>