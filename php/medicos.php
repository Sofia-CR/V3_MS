<?php
include 'conexion.php';

// Establecer codificación
$conexion->set_charset("utf8mb4");

// Función para mostrar médicos por turno
function mostrarMedicosPorTurno($turno, $conexion) {
    echo "<h3 class='text-center'>MÉDICOS $turno</h3>";
    echo "<div class='row g-4'>";
    
    $sql = "SELECT nombre, apaterno, amaterno, especialidad, foto, descripcion FROM Medicos WHERE turno='$turno'";
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
            echo "<p class='card-text'>" . $fila['descripcion'] . "</p>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p class='text-center'>No hay médicos en el turno $turno.</p>";
    }

    echo "</div>";
}

// Mostrar médicos por turnos
mostrarMedicosPorTurno("MATUTINO", $conexion);
mostrarMedicosPorTurno("VESPERTINO", $conexion);

// Cerrar la conexión
$conexion->close();
?>
