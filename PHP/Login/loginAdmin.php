<?php
session_start();

require '../Conexion/conexion.php';

// 2. Obtener datos del formulario
$username = $_POST['correo'];
$password = $_POST['password'];

// 3. Consultar en la base de datos
$sql = "SELECT * FROM administrador WHERE CORREO = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    // 4. Verificar contraseña
    if ($password == $usuario['CONTRASEÑA']) {
        $_SESSION['correo'] = $usuario['CORREO']; // Guardar sesión
        header("Location: ../../Screens/Alumno/Asistencias.php");
        exit();
    } else {
        echo "Contraseña incorrecta";
        echo "<br>";
        echo $password;
        echo "<br>";
        echo $usuario['CONTRASEÑA'];
    }
} else {
    echo "Usuario no encontrado";
}

$conn->close();
?>

