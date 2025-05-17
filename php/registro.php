<?php
include 'conexion.php';

// Recibir datos del formulario
if (isset($_POST['username']) && isset($_POST['password'])) {
    $curp = $_POST['username'];
    $contrasena = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validar que los datos no estén vacíos
    if (!empty($curp) && !empty($_POST['password'])) {
        $stmt = $conexion->prepare("INSERT INTO Sesion (curp, contrasena) VALUES (?, ?)");
        $stmt->bind_param("ss", $curp, $contrasena);

        if ($stmt->execute()) {
            echo "<script>window.location.href='../html/login.html';</script>";
        } else {
            echo "<script>alert('Error en la consulta: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error: Todos los campos son obligatorios');</script>";
    }
}

$conexion->close();
?>



