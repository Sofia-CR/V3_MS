<?php
session_start();
include 'conexion.php';

$curp = $_SESSION['curp'] ?? '';

if ($curp == '') {
    echo json_encode(['error' => 'CURP no encontrada en sesión']);
    exit;
}

$sql = "SELECT nombre, apaterno, amaterno, edad, calle, colonia, municipio, cp, estado, nexterior, ninterior, telefono, correo FROM usuarios WHERE curp = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $curp);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    echo json_encode($fila);
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
}

$stmt->close();
$conexion->close();
?>
