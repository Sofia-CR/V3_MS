<?php
include 'conexion.php';

$medico_id = isset($_POST['id_medico']) ? $_POST['id_medico'] : null;

// Verificar si se recibió el id del médico
if (!$medico_id) {
    die("Falta el parámetro 'id_medico' en la solicitud.");
}

// Obtener la fecha actual
$fecha_actual = date('Y-m-d');

// Calcular la fecha dentro de dos meses
$fecha_dos_meses = date('Y-m-d', strtotime('+2 months'));

// Consulta para obtener las citas agendadas en los próximos dos meses para el médico especificado
$sql = "SELECT fecha, hora, estatus FROM Citas WHERE id_medico = ? AND fecha BETWEEN ? AND ? AND estatus = 'Agendada'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('iss', $medico_id, $fecha_actual, $fecha_dos_meses);
$stmt->execute();
$result = $stmt->get_result();

$citas_agendadas = [];
while ($row = $result->fetch_assoc()) {
    // Guardar las citas agendadas en el arreglo
    $citas_agendadas[] = [
        'fecha' => $row['fecha'],
        'hora' => $row['hora'],
        'estatus' => $row['estatus']
    ];
}

// Mostrar las citas agendadas
if (count($citas_agendadas) > 0) {
    echo "Citas agendadas para el médico " . $medico_id . " en los próximos dos meses:\n";
    foreach ($citas_agendadas as $cita) {
        echo "Fecha: " . $cita['fecha'] . " - Hora: " . $cita['hora'] . " - Estatus: " . $cita['estatus'] . "\n";
    }
} else {
    echo "No hay citas agendadas para este médico en los próximos dos meses.\n";
}

$conexion->close();
?>
