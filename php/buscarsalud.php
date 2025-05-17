<?php
session_start();
include 'conexion.php';

$curp = $_SESSION['curp'] ?? '';

if ($curp == '') {
    echo json_encode(['error' => 'CURP NO ENCONTRADA EN SESION']);
    exit;
}

$sql = "SELECT 
    estatura, 
    peso, 
    cintura, 
    sexo, 
    gsangre, 
    csueno, 
    halimen, 
    diabetes, 
    hipertension, 
    alergias, 
    calcohol, 
    ctabaco, 
    enfermedades, 
    cirujias, 
    mactual, 
    nafisica, 
    antfami, 
    visitas, 
    lesiones, 
    prespiratorios, 
    pcardiovasculares, 
    ementales, 
    embarazo, 
    adermatologicas, 
    tsueños 
FROM DatosSalud 
WHERE curp = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $curp);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    echo json_encode($fila);
} else {
    echo json_encode(['error' => 'NO SE ENCONTRARON DATOS']);
}

$stmt->close();
$conexion->close();
?>
