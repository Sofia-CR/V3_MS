<?php
include 'conexion.php';  // Asegúrate de que la conexión a la base de datos está incluida
$conexion->set_charset("utf8mb4");
// Verificar si la conexión fue exitosa
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);
$curp = $data["curpUsuario"];
$id_medico = $data["id_medico"];

$response = [];

// Buscar nombre del paciente
$queryPaciente = $conexion->prepare("SELECT nombre, apaterno, amaterno FROM usuarios WHERE curp = ?");
$queryPaciente->bind_param("s", $curp);
$queryPaciente->execute();
$resultPaciente = $queryPaciente->get_result();
if ($row = $resultPaciente->fetch_assoc()) {
    $response["paciente"] = $row["nombre"] . " " . $row["apaterno"] . " " . $row["amaterno"];
}

// Buscar nombre del médico
$queryMedico = $conexion->prepare("SELECT nombre, apaterno, amaterno FROM medicos WHERE id_medico = ?");
$queryMedico->bind_param("i", $id_medico);
$queryMedico->execute();
$resultMedico = $queryMedico->get_result();
if ($row = $resultMedico->fetch_assoc()) {
    $response["medico"] = $row["nombre"] . " " . $row["apaterno"] . " " . $row["amaterno"];
}

echo json_encode($response);
?>




