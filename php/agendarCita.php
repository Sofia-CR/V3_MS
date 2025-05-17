<?php
// Configurar el encabezado para que el contenido sea JSON
header('Content-Type: application/json; charset=UTF-8');
include 'conexion.php';

// Leer los datos enviados por el cliente
$data = json_decode(file_get_contents("php://input"), true);

// Validar que los datos necesarios hayan llegado
$curp = isset($data["curpUsuario"]) ? $conexion->real_escape_string($data["curpUsuario"]) : '';
$id_medico = isset($data["id_medico"]) ? $conexion->real_escape_string($data["id_medico"]) : '';
$fecha = isset($data["fecha"]) ? $conexion->real_escape_string($data["fecha"]) : '';
$hora = isset($data["hora"]) ? $conexion->real_escape_string($data["hora"]) : '';
$turno = isset($data["turno"]) ? $conexion->real_escape_string($data["turno"]) : '';

if (!$curp || !$id_medico || !$fecha || !$hora || !$turno) {
    echo json_encode(["error" => "Faltan datos necesarios para agendar la cita."]);
    exit;
}

// Verificar si el médico ya tiene una cita a esa fecha y hora
$checkMedico = "SELECT 1 FROM Citas WHERE id_medico = '$id_medico' AND fecha = '$fecha' AND hora = '$hora'";
$resultMedico = $conexion->query($checkMedico);

if ($resultMedico && $resultMedico->num_rows > 0) {
    echo json_encode(["error" => "El médico ya tiene una cita agendada en esa fecha y hora."]);
    exit;
}

// Verificar si el USUARIO ya tiene una cita en esa fecha y hora (con cualquier médico)
$checkUsuario = "SELECT 1 FROM Citas 
WHERE fecha = '$fecha' 
  AND hora = '$hora' 
  AND turno = '$turno' 
  AND curp = '$curp' 
  AND estatus = 'Agendada'";
$resultUsuario = $conexion->query($checkUsuario);

if ($resultUsuario && $resultUsuario->num_rows > 0) {
    echo json_encode(["error" => "Ya tienes una cita agendada en esa fecha y hora."]);
    exit;
}

// Insertar la cita
$sql = "INSERT INTO Citas (curp, id_medico, fecha, hora, turno) 
VALUES ('$curp', '$id_medico', '$fecha', '$hora', '$turno')";

if ($conexion->query($sql) === TRUE) {
    echo json_encode([
        "success" => true,
        "mensaje" => "Cita programada correctamente.",
        "id_cita" => $conexion->insert_id
    ]);
} else {
    echo json_encode(["error" => "Error al agendar la cita: " . $conexion->error]);
}

// Cerrar la conexión
$conexion->close();
?>



