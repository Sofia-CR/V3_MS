<?php
include 'conexion.php';

// Establecer codificación
$conexion->set_charset("utf8mb4");

// Obtener el turno desde la URL
$turno = isset($_GET['turno']) ? $_GET['turno'] : '';

if ($turno) {
    $sql = "SELECT nombre, apaterno, amaterno, especialidad, foto  FROM Medicos WHERE turno='$turno'";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $imagen = !empty($fila['foto']) ? "http://localhost:8080/MedicaSur/" . $fila['foto'] : "http://localhost:8080/MedicaSur/imagenes/default.jpg";

            echo "<div class='col-md-4'>";
            echo "<div class='card h-100 shadow'>";
            echo "<img src='".$imagen."' class='card-img-top' alt='Foto de ".$fila['nombre']."'>";
            echo "<div class='card-body'>";
            echo "<h5 class='card-title text-primary'>Dr. " . $fila['nombre'] . " " . $fila['apaterno'] . " " . $fila['amaterno'] . "</h5>";
            echo "<p class='card-text'>Especialidad: " . $fila['especialidad'] . "</p>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p class='text-center'>No hay médicos registrados en este turno.</p>";
    }
} else {
    echo "<p class='text-center text-danger'>Error: No se especificó un turno.</p>";
}

$conexion->close();
?>
