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
    
    
    // if (isset($_POST['asignar'])){
    //     // Paso 1: Obtener todas las materias en las que está inscrito el alumno
    //     $matricula = $_POST['matricula'];
    //     $materiasQuery = "SELECT NRC, NOMBRE FROM materia WHERE MATRICULA_ESTUDIANTE = ?";
    //     $stmtMaterias = $conn->prepare($materiasQuery);
    //     $stmtMaterias->bind_param("s", $matricula);
    //     $stmtMaterias->execute();
    //     $materiasResult = $stmtMaterias->get_result();

    //     // Paso 2: Obtener todos los maestros
    //     $maestrosQuery = "SELECT NO_COLABORADOR, NOMBRE FROM maestro";
    //     $maestrosResult = mysqli_query($conn, $maestrosQuery);

    //     echo "<div style='display: flex; justify-content: center; margin-top: 30px;'>";
    //     echo "<form method='POST'>";
    //     echo "<input type='hidden' name='asignacion_confirmada' value='1'>";
    //     echo "<input type='hidden' name='matricula' value='{$_POST['matricula']}'>";

    //     // Día, hora inicio y fin
    //     echo "<label>Día:</label> <input type='text' name='dia' required><br><br>";
    //     echo "<label>Hora Inicio:</label> <input type='time' name='hora_inicio' required><br><br>";
    //     echo "<label>Hora Fin:</label> <input type='time' name='hora_fin' required><br><br>";

    //     // Selección de materia
    //     echo "<label>Materia:</label> <select name='nrc' id='nrc' required>";
    //     echo "<option value=''>-- Selecciona una materia --</option>";
    //     while ($row = mysqli_fetch_assoc($materiasResult)) {
    //         echo "<option value='{$row['NRC']}'>{$row['NOMBRE']} ({$row['NRC']})</option>";
    //     }
    //     echo "</select><br><br>";

    //     // Selección de maestro (opcional, se usará vía JavaScript si no hay uno asignado)
    //     echo "<div id='select_maestro_container' style='display:none'>";
    //     echo "<label>Maestro:</label> <select name='no_colaborador'>";
    //     echo "<option value=''>-- Selecciona un maestro --</option>";
    //     while ($row = mysqli_fetch_assoc($maestrosResult)) {
    //         echo "<option value='{$row['NO_COLABORADOR']}'>{$row['NOMBRE']} ({$row['NO_COLABORADOR']})</option>";
    //     }
    //     echo "</select></div><br><br>";

    //     echo "<button type='submit' class='botonAsistencias'>Guardar Horario</button>";
    //     echo "</form>";
    //     echo "</div>";

    //     // Script para manejar dinámicamente si mostrar el campo de maestro o no
    //     echo "
    //     <script>
    //         document.getElementById('nrc').addEventListener('change', function() {
    //             const nrc = this.value;
    //             if (nrc === '') return;

    //             fetch('verifica_maestro.php?nrc=' + nrc)
    //                 .then(response => response.json())
    //                 .then(data => {
    //                     if (data.asignado) {
    //                         document.getElementById('select_maestro_container').style.display = 'none';
    //                     } else {
    //                         document.getElementById('select_maestro_container').style.display = 'block';
    //                     }
    //                 });
    //         });
    //     </script>";
    // }

    if (isset($_POST['asignar'])) {
        $matricula = $_POST['matricula'];

        // 1. Verificar si el alumno tiene materias asignadas
        $materiasAsignadasQuery = "SELECT m.NRC, m.NOMBRE, m.NO_COLABORADOR 
                                    FROM materia m
                                    WHERE m.MATRICULA_ESTUDIANTE = ?";
        $stmtMaterias = $conn->prepare($materiasAsignadasQuery);
        $stmtMaterias->bind_param("s", $matricula);
        $stmtMaterias->execute();
        $materiasResult = $stmtMaterias->get_result();

        // 2. Obtener todas las materias si no tiene asignadas
        if ($materiasResult->num_rows === 0) {
            $materiasResult = $conn->query("SELECT NRC, NOMBRE, NO_COLABORADOR FROM materia");
        }

        // 3. Obtener todos los maestros disponibles
        $maestrosResult = $conn->query("SELECT NO_COLABORADOR, NOMBRE FROM maestro");

        // Mostrar formulario centrado
        // echo "<div style='display: flex; justify-content: center; margin-top: 30px;'>";
        // echo "<form method='POST' style='text-align: center;'>";

        // echo "<input type='hidden' name='matricula' value='{$matricula}'>";

        // // Materia
        // echo "<label for='nrc'>Seleccionar Materia:</label><br>";
        // echo "<select name='nrc' id='nrcSelect' required style='margin-bottom: 15px;'>";
        // $materias = []; // almacenaremos aquí para uso en JS opcional si lo deseas
        // while ($row = $materiasResult->fetch_assoc()) {
        //     $materias[] = $row;
        //     echo "<option value='{$row['NRC']}' data-maestro='{$row['NO_COLABORADOR']}'>{$row['NOMBRE']}</option>";
        // }
        // echo "</select><br><br>";

        // // Maestro
        // echo "<label for='maestro'>Seleccionar Maestro:</label><br>";
        // echo "<select name='maestro' id='maestroSelect' required style='margin-bottom: 15px;'>";
        // while ($row = $maestrosResult->fetch_assoc()) {
        //     echo "<option value='{$row['NO_COLABORADOR']}'>{$row['NOMBRE']}</option>";
        // }
        // echo "</select><br><br>";

        // // Día y horas
        // echo "<label for='dia'>Día de la Semana:</label><br>";
        // echo "<input type='text' name='dia' required style='margin-bottom: 15px;'><br>";

        // echo "<label for='hora_inicio'>Hora de Inicio:</label><br>";
        // echo "<input type='time' name='hora_inicio' required style='margin-bottom: 15px;'><br>";

        // echo "<label for='hora_fin'>Hora de Fin:</label><br>";
        // echo "<input type='time' name='hora_fin' required style='margin-bottom: 15px;'><br>";

        // echo "<button type='submit' name='guardar_asignacion' class='botonAsistencias'>Asignar Horario</button>";

        // echo "</form>";
        // echo "</div>";
        echo "<div style='display: flex; justify-content: center; margin-top: 30px;'>";
        echo "<form method='POST' style='text-align: center;'>";

        echo "<input type='hidden' name='matricula' value='{$matricula}'>";

        // Materia
        echo "<label for='nrc'>Seleccionar Materia:</label><br>";
        echo "<select name='nrc' id='nrcSelect' required style='margin-bottom: 15px;'>";

        $materiasJS = []; // para JavaScript
        while ($row = $materiasResult->fetch_assoc()) {
            $nrc = $row['NRC'];
            $nombre = $row['NOMBRE'];
            $maestro = $row['NO_COLABORADOR'];
            echo "<option value='$nrc' data-maestro='$maestro'>$nombre</option>";
            $materiasJS[] = ["nrc" => $nrc, "maestro" => $maestro];
        }
        echo "</select><br><br>";

        // Maestro
        echo "<label for='maestro'>Seleccionar Maestro:</label><br>";
        echo "<select name='maestro' id='maestroSelect' required style='margin-bottom: 15px;'>";

        $maestros = [];
        while ($row = $maestrosResult->fetch_assoc()) {
            $id = $row['NO_COLABORADOR'];
            $nombre = $row['NOMBRE'];
            echo "<option value='$id'>$nombre</option>";
            $maestros[] = ["id" => $id, "nombre" => $nombre];
        }
        echo "</select><br><br>";

        // Día y horas
        echo "<label for='dia'>Día de la Semana:</label><br>";
        echo "<input type='text' name='dia' required style='margin-bottom: 15px;'><br>";

        echo "<label for='hora_inicio'>Hora de Inicio:</label><br>";
        echo "<input type='time' name='hora_inicio' required style='margin-bottom: 15px;'><br>";

        echo "<label for='hora_fin'>Hora de Fin:</label><br>";
        echo "<input type='time' name='hora_fin' required style='margin-bottom: 15px;'><br>";

        echo "<button type='submit' name='guardar_asignacion' class='botonAsistencias'>Asignar Horario</button>";

        echo "</form>";
        echo "</div>";

        // Pasar datos al JS
        echo "<script>
            const materias = " . json_encode($materiasJS) . ";
            const maestroSelect = document.getElementById('maestroSelect');
            const nrcSelect = document.getElementById('nrcSelect');

            function actualizarMaestro() {
                const selectedNrc = nrcSelect.value;
                const materia = materias.find(m => m.nrc === selectedNrc);
                
                if (materia && materia.maestro) {
                    for (let i = 0; i < maestroSelect.options.length; i++) {
                        if (maestroSelect.options[i].value === materia.maestro) {
                            maestroSelect.selectedIndex = i;
                            break;
                        }
                    }
                    maestroSelect.disabled = true;
                } else {
                    maestroSelect.disabled = false;
                    maestroSelect.selectedIndex = 0;
                }
            }

            nrcSelect.addEventListener('change', actualizarMaestro);
            window.onload = actualizarMaestro;
        </script>";

    }


    if (isset($_POST['guardar_asignacion'])) {
        $dia = $_POST['dia'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];
        $nrc = $_POST['nrc'];
        $matricula = $_POST['matricula'];

        // Verificamos si ya hay un maestro asignado
        $stmt = $conn->prepare("SELECT NO_COLABORADOR FROM materia WHERE NRC = ?");
        $stmt->bind_param("s", $nrc);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $no_colaborador = $row['NO_COLABORADOR'];

        if (!$no_colaborador && isset($_POST['no_colaborador']) && $_POST['no_colaborador'] !== '') {
            $no_colaborador = $_POST['no_colaborador'];
        }

        if ($no_colaborador) {
            $insert = "INSERT INTO horario (DIA_SEMANA, HORA_INICIO, HORA_FIN, MATRICULA_ESTUDIANTE, NO_COLABORADOR, NRC_MATERIA)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert);
            $stmt->bind_param("ssssss", $dia, $hora_inicio, $hora_fin, $matricula, $no_colaborador, $nrc);
            $stmt->execute();

            // Actualizar maestro en materia solo si no tiene uno asignado aún
            $updateMateria = "UPDATE materia SET NO_COLABORADOR = ? WHERE NRC = ? AND NO_COLABORADOR IS NULL";
            $stmt2 = $conn->prepare($updateMateria);
            $stmt2->bind_param("ss", $maestro, $nrc);
            $stmt2->execute();

            echo "<p style='color: green; text-align: center;'>Horario asignado correctamente.</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>Error: No se puede asignar horario sin maestro.</p>";
        }
    }

}
?>

  <script src="../../js/dropdown.js"></script>
</body>
</html>