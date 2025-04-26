<?php
require '../Conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = $_POST['matricula'];
    $nombre = $_POST['nombre'];
    $apellidoP = $_POST['apellidoP'];
    $apellidoM = $_POST['apellidoM'];
    $correo = $_POST['correo'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $edad = $_POST['edad'];
    $password = $_POST['password'];

    $query = "UPDATE alumno SET 
        NOMBRE = ?, 
        APELLIDO_PATERNO = ?, 
        APELLIDO_MATERNO = ?, 
        CORREO = ?, 
        FECHA_NACIMIENTO = ?, 
        EDAD = ?, 
        CONTRASEÑA = ?
        WHERE MATRICULA = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssss", $nombre, $apellidoP, $apellidoM, $correo, $fechaNacimiento, $edad, $password, $matricula);

    if ($stmt->execute()) {
        echo "<script>
            alert('Datos actualizados exitosamente.');
            window.location.href = '../../Screens/Alumno/Asistencias.php';
          </script>";
        exit();
    } else {
        echo "Error al actualizar: " . $conn->error;
    }
}
?>
