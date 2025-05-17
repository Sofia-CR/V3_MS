<?php
include 'conexion.php';
$conexion->set_charset("utf8mb4");

$turno = isset($_GET['turno']) ? $_GET['turno'] : '';
$nombreMedico = isset($_GET['nombre']) ? $_GET['nombre'] : '';

if ($turno) {
    $sql = "SELECT id_medico, nombre, apaterno, amaterno, especialidad, foto FROM Medicos WHERE turno='$turno'";
} elseif ($nombreMedico) {
    $sql = "SELECT id_medico, nombre, apaterno, amaterno, especialidad, foto FROM Medicos WHERE nombre='$nombreMedico'";
    echo "<p>Nombre recibido: " . htmlspecialchars($nombreMedico) . "</p>";
} else {
    echo "<p class='text-center text-danger'>Error: No se especificó un turno o un médico.</p>";
    exit(); 
}
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $imagen = !empty($fila['foto']) ? "http://localhost:8080/MedicaSur/" . $fila['foto'] : "http://localhost:8080/MedicaSur/imagenes/default.jpg";
    
        echo "<div class='col-md-4'>";
        echo "<div class='card h-100 shadow medico-card' data-doctor-id='" . $fila['id_medico'] . "'>"; // ✅ Aquí agregamos data-doctor-id
        echo "<img src='".$imagen."' class='card-img-top' alt='Foto de ".$fila['nombre']."'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title text-primary'>Dr. " . $fila['nombre'] . " " . $fila['apaterno'] . " " . $fila['amaterno'] . "</h5>";
        echo "<p class='card-text'>Especialidad: " . $fila['especialidad'] . "</p>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }    
} else {
    echo "<p class='text-center'>No se encontró el médico seleccionado.</p>";
}

$conexion->close();
?>



