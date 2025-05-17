<?php
header("Content-Type: application/json");
include 'conexion.php';

// Obtener la fecha desde la URL
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$turno = isset($_GET['turno']) ? $_GET['turno'] : ''; 

if ($fecha && $turno) {
    $horariosDisponibles = [];

    // Definir rangos según el turno
    $horaInicio = ($turno === 'Matutino') ? 8 : 14;
    $horaFin = ($turno === 'Matutino') ? 14 : 21;

    // Generar dos citas por cada hora
    for ($hora = $horaInicio; $hora < $horaFin; $hora++) {
        $horariosDisponibles[] = sprintf("%02d:00", $hora);
        $horariosDisponibles[] = sprintf("%02d:30", $hora);
    }

    // Filtrar horarios ya ocupados en la BD
    $sql = "SELECT hora FROM HorariosCitas WHERE fecha = ? AND turno = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $fecha, $turno);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $ocupados = [];
    while ($fila = $resultado->fetch_assoc()) {
        $ocupados[] = $fila['hora'];
    }

    // Eliminar los horarios ocupados
    $horariosDisponibles = array_diff($horariosDisponibles, $ocupados);

    echo json_encode(["horarios" => array_values($horariosDisponibles)]);
} else {
    echo json_encode(["error" => "Fecha o turno no proporcionados"]);
}

$conexion->close();
?>
