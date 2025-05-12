<?php
require '../Conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellidoP = $_POST['apellidoP'];
    $apellidoM = $_POST['apellidoM'];
    $correo = $_POST['correo'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $password = $_POST['password'];

    $query = "UPDATE administrador SET 
        NOMBRE = ?, 
        APELLIDO_PATERNO = ?, 
        APELLIDO_MATERNO = ?, 
        CORREO = ?, 
        FECHA_NACIMIENTO = ?, 
        CONTRASENA = ?
        WHERE ID = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssss", $nombre, $apellidoP, $apellidoM, $correo, $fechaNacimiento, $password, $id);

    if ($stmt->execute()) {
        echo "<script>
            alert('Datos actualizados exitosamente.');
            window.location.href = '../../Screens/Admin/Asistencias.php';
          </script>";
        exit();
    } else {
        echo "Error al actualizar: " . $conn->error;
    }
}
?>