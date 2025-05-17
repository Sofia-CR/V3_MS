<?php
session_start(); // <-- ¡Muy importante!
header('Content-Type: application/json; charset=UTF-8');

include 'conexion.php';

// Recibir datos del formulario
$data = json_decode(file_get_contents("php://input"), true);
$curp = isset($data["username"]) ? $conexion->real_escape_string($data["username"]) : '';
$password = isset($data["password"]) ? $data["password"] : '';

if (!$curp || !$password) {
    echo json_encode(["error" => "Usuario y contraseña son obligatorios."]);
    exit;
}

// Buscar el usuario en la BD
$sql = "SELECT contrasena FROM Sesion WHERE curp = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $curp);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
        // ✅ Aquí guardamos la CURP en la sesión
        $_SESSION["curp"] = $curp;

        echo json_encode(["success" => "Inicio de sesión exitoso", "curp" => $curp]);
    } else {
        echo json_encode(["error" => "Contraseña incorrecta"]);
    }
} else {
    echo json_encode(["error" => "Usuario no encontrado"]);
}

$stmt->close();
$conexion->close();
?>
