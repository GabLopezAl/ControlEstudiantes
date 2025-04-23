<?php

require 'conexion.php';

$noColaborador = $_POST['noColaborador'];
$nombre = $_POST['nombre'];
$apellidoP = $_POST['apellidoP'];
$apellidoM = $_POST['apellidoM'];
$correo = $_POST['correo'];
$fechaNacimiento = $_POST['fechaNacimiento'];
$rol = 'maestro';

// Insertar datos en la base de datos
$sql = "INSERT INTO maestro (NO_COLABORADOR, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, CORREO, FECHA_NACIMIENTO, ROL)
        VALUES ('$noColaborador', '$nombre', '$apellidoP', '$apellidoM', '$correo', '$fechaNacimiento', '$rol')";

if ($conn->query($sql) === TRUE) {
    echo "<script>
            alert('Registro guardado exitosamente.');
            window.location.href = '../index.php';
          </script>";
} else {
    echo "Error: " . $sql . "<br>" . $conexion->error;
}

$conn->close();
?>