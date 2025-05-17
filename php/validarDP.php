<?php
// Cabecera para permitir solicitudes desde JavaScript
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

// Conexión a la base de datos
include("conexion.php");

// Obtener el cuerpo del POST
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["curp"])) {
    echo json_encode(["error" => "CURP no proporcionada"]);
    exit;
}

$curp = $data["curp"];

// Preparar y ejecutar consulta
$sql = "SELECT * FROM usuarios WHERE curp = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $curp);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si hay resultados
if ($result->num_rows > 0) {
    echo json_encode(["existe" => true]);
} else {
    echo json_encode(["existe" => false]);
}

$stmt->close();
$conexion->close();
?>
