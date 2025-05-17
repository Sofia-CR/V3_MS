<?php
include 'conexion.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$curp = $input['curp'];


$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE curp = ?");
$stmt->bind_param("s", $curp);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    echo json_encode(["existe" => true]);
} else {
    echo json_encode(["existe" => false]);
}

$stmt->close();
$conexion->close();
?>
