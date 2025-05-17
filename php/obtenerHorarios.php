<?php
header('Content-Type: application/json; charset=UTF-8');

include 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
$id_medico = isset($data["id_medico"]) ? $conexion->real_escape_string($data["id_medico"]) : '';

if (!$id_medico) {
    echo json_encode([]);
    exit;
}

error_log("ID de médico recibido: " . $id_medico);
$sql = "SELECT hora FROM Citas WHERE id_medico = '$id_medico' AND disponible = 1";

$resultado = $conexion->query($sql);

$horarios = [];

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $horarios[] = $fila['horario']; // Guardar solo el horario
    }
}

$conexion->close();
echo json_encode($horarios);
?>
