<?php
session_start();

require '../Conexion/conexion.php';

// 2. Obtener datos del formulario
$username = $_POST['correo'];
$password = $_POST['password'];

// 3. Consultar en la base de datos alumno
$sql = "SELECT * FROM alumno WHERE CORREO = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$alumno = $stmt->get_result();

// 3. Consultar en la base de datos maestro
$sql1 = "SELECT * FROM maestro WHERE CORREO = ?";
$stmt1 = $conn->prepare($sql1);
$stmt1->bind_param("s", $username);
$stmt1->execute();
$maestro = $stmt1->get_result();

// 3. Consultar en la base de datos admin
$sql2 = "SELECT * FROM administrador WHERE CORREO = ?";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("s", $username);
$stmt2->execute();
$admin = $stmt2->get_result();

if ($alumno->num_rows === 1) {
    $usuario = $alumno->fetch_assoc();

    // 4. Verificar contraseña
    if ($password == $usuario['CONTRASEÑA']) {
        $_SESSION['correo'] = $usuario['CORREO']; // Guardar sesión
        $_SESSION['NOMBRE'] = $usuario['NOMBRE']; // Obtiene nombre del usuario
        $_SESSION['MATRICULA'] = $usuario['MATRICULA']; // Obtiene la matricula del estudiante
        header("Location: ../../Screens/Alumno/Asistencias.php");
        exit();
    } else {
        echo "Contraseña incorrecta";
        // echo "<br>";
        // echo $password;
        // echo "<br>";
        // echo $usuario['CONTRASEÑA'];
    }
} else {
    echo "Usuario no encontrado";
}

if ($maestro->num_rows === 1) {
    $usuario = $maestro->fetch_assoc();

    // 4. Verificar contraseña
    if ($password == $usuario['CONTRASEÑA']) {
        $_SESSION['correo'] = $usuario['CORREO']; // Guardar sesión
        $_SESSION['NOMBRE'] = $usuario['NOMBRE']; // Obtiene nombre del usuario
        $_SESSION['NO_COLABORADOR'] = $usuario['NO_COLABORADOR']; // Obtiene la matricula del estudiante
        header("Location: ../../Screens/Maestro/Asistencias.php");
        exit();
    } else {
        echo "Contraseña incorrecta";
    }
} else {
    echo "Usuario no encontrado";
}

$conn->close();
?>

