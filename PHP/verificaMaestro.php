<?php
require 'Conexion/conexion.php';

if (isset($_GET['nrc'])) {
    $nrc = $_GET['nrc'];
    $query = "SELECT NO_COLABORADOR FROM materia WHERE NRC = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nrc);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode(['asignado' => !empty($row['NO_COLABORADOR'])]);
}