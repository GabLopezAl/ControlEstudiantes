<?php

require '../Conexion/conexion.php';

$matricula = $_POST['matricula'];
$nombre = $_POST['nombre'];
$apellidoP = $_POST['apellidoP'];
$apellidoM = $_POST['apellidoM'];
$correo = $_POST['correo'];
$fechaNacimiento = $_POST['fechaNacimiento'];
$edad = $_POST['edad'];
$contraseña = $_POST['password'];
$rol = 'alumno';

// Insertar datos en la base de datos
$sql = "INSERT INTO alumno (MATRICULA, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, CORREO, FECHA_NACIMIENTO, EDAD, CONTRASEÑA, ROL)
        VALUES ('$matricula', '$nombre', '$apellidoP', '$apellidoM', '$correo', '$fechaNacimiento', '$edad', $contraseña, '$rol')";

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