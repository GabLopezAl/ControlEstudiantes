<?php
$host = "localhost";
$usuario = "root";
$contrasena = ""; 
$base_de_datos = "controlestudiantes"; 

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
echo "<script>
        console.log('Conexion exitosa');
      </script>";
?>
