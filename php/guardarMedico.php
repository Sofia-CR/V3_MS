<?php
include 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
$id_medico = isset($data["id_medico"]) ? $conexion->real_escape_string($data["id_medico"]) : '';

if (!$id_medico) {
    echo json_encode(["error" => "No se recibió el ID del médico"]);
    exit;
}

$sql = "INSERT INTO Citas (id_medico) VALUES ('$id_medico')";

if ($conexion->query($sql) === TRUE) {
    echo json_encode(["mensaje" => "Médico seleccionado guardado exitosamente"]);
} else {
    echo json_encode(["error" => "Error al guardar médico: " . $conexion->error]);
}

$conexion->close();
?>
