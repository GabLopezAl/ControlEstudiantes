<?php

require '../Conexion/conexion.php';

$nombre = $_POST['nombre'];
$apellidoP = $_POST['apellidoP'];
$apellidoM = $_POST['apellidoM'];
$correo = $_POST['correo'];
$fechaNacimiento = $_POST['fechaNacimiento'];
$contraseña = $_POST['password'];
$rol = 'administrador';

// Insertar datos en la base de datos
$sql = "INSERT INTO administrador (NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, CORREO, FECHA_NACIMIENTO, CONTRASENA,ROL)
        VALUES ('$nombre', '$apellidoP', '$apellidoM', '$correo', '$fechaNacimiento', '$contraseña', '$rol')";

if ($conn->query($sql) === TRUE) {
    echo "<script>
            alert('Registro guardado exitosamente.');
            window.location.href = '../../index.php';
          </script>";
} else {
    echo "Error: " . $sql . "<br>" . $conexion->error;
}

$conn->close();
?>
