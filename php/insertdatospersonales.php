<?php
include 'conexion.php'; 
include 'datospersonales.php';

// Recibir los datos del formulario
$curp = $_POST['curp'];
$nombre = $_POST['nombre'];
$apaterno = $_POST['apaterno'];
$amaterno = $_POST['amaterno'];
$edad = $_POST['edad'];
$calle = $_POST['calle'];
$colonia = $_POST['colonia'];
$municipio = $_POST['municipio'];
$cp = $_POST['cp'];
$estado = $_POST['estado'];
$nexterior = $_POST['nExt'];
$ninterior = $_POST['nInt'];
$telefono = $_POST['telefono'];
$correo = $_POST['correo'];

// Verificar si ya existe la CURP
$consulta = $conexion->prepare("SELECT curp FROM usuarios WHERE curp = ?");
$consulta->bind_param("s", $curp);
$consulta->execute();
$consulta->store_result();

if ($consulta->num_rows > 0) {
    // Si ya existe, puedes actualizar los datos si lo deseas:
    $sql = "UPDATE usuarios SET nombre=?, apaterno=?, amaterno=?, edad=?, calle=?, colonia=?, municipio=?, cp=?, estado=?, nexterior=?, ninterior=?, telefono=?, correo=? WHERE curp=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssissssssssss", $nombre, $apaterno, $amaterno, $edad, $calle, $colonia, $municipio, $cp, $estado, $nexterior, $ninterior, $telefono, $correo, $curp);
} else {
    // Si no existe, insertar
    $sql = "INSERT INTO usuarios (curp, nombre, apaterno, amaterno, edad, calle, colonia, municipio, cp, estado, nexterior, ninterior, telefono, correo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssisssssssss", $curp, $nombre, $apaterno, $amaterno, $edad, $calle, $colonia, $municipio, $cp, $estado, $nexterior, $ninterior, $telefono, $correo);
}

if ($stmt->execute()) {
    $_SESSION['curp'] = $curp;
    header("Location: ../html/datospersonales.html?guardado=ok");
    exit;
} else {
    echo json_encode(["error" => "Error al guardar los datos: " . $stmt->error]);
}

$stmt->close();
$consulta->close();
$conexion->close();
?>
