<?php
require '../Conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noColaborador = $_POST['noColaborador'];
    $nombre = $_POST['nombre'];
    $apellidoP = $_POST['apellidoP'];
    $apellidoM = $_POST['apellidoM'];
    $correo = $_POST['correo'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $password = $_POST['password'];

    $query = "UPDATE maestro SET 
        NOMBRE = ?, 
        APELLIDO_PATERNO = ?, 
        APELLIDO_MATERNO = ?, 
        CORREO = ?, 
        FECHA_NACIMIENTO = ?, 
        CONTRASEÑA = ?
        WHERE NO_COLABORADOR = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssss", $nombre, $apellidoP, $apellidoM, $correo, $fechaNacimiento, $password, $noColaborador);

    if ($stmt->execute()) {
        echo "<script>
            alert('Datos actualizados exitosamente.');
            window.location.href = '../../Screens/Maestro/Asistencias.php';
          </script>";
        exit();
    } else {
        echo "Error al actualizar: " . $conn->error;
    }
}
?>