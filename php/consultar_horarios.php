<?php
// Configuración de la conexión a la base de datos
include 'conexion.php';

// Obtener los parámetros del formulario
$medico_id = $_GET['medico'];
$fecha = $_GET['fecha'];

// Consultar el turno del médico
$sql_turno = "SELECT turno FROM Medicos WHERE id_medico = ?";
$stmt_turno = $conexion->prepare($sql_turno);
$stmt_turno->bind_param('i', $medico_id);
$stmt_turno->execute();
$result_turno = $stmt_turno->get_result();

$turno_medico = null;
if ($row_turno = $result_turno->fetch_assoc()) {
    $turno_medico = $row_turno['turno'];
}

// Definir los turnos
$turno_matutino_inicio = '07:00:00';
$turno_matutino_fin = '13:30:00';
$turno_vespertino_inicio = '14:00:00';
$turno_vespertino_fin = '20:30:00';

// Consultar las horas ocupadas para el médico y la fecha
$sql = "SELECT hora FROM Citas WHERE id_medico = ? AND fecha = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('is', $medico_id, $fecha);
$stmt->execute();
$result = $stmt->get_result();

$horas_ocupadas = [];
while ($row = $result->fetch_assoc()) {
    // Cambiar 'hora_inicio' a 'hora' (nombre correcto de la columna)
    $horas_ocupadas[] = $row['hora'];  // Aquí usamos 'hora' en lugar de 'hora_inicio'
}

// Generar las horas disponibles
$horas_disponibles = [];

// Verificar si es turno matutino o vespertino
if ($turno_medico == 'Matutino') {
    // Rellenar las horas del turno matutino
    $hora = strtotime($turno_matutino_inicio);
    while ($hora <= strtotime($turno_matutino_fin)) {
        $hora_str = date('H:i:s', $hora);
        if (!in_array($hora_str, $horas_ocupadas)) {
            $horas_disponibles[] = $hora_str;
        }
        $hora = strtotime('+30 minutes', $hora);
    }
} elseif ($turno_medico == 'Vespertino') {
    // Rellenar las horas del turno vespertino
    $hora = strtotime($turno_vespertino_inicio);
    while ($hora <= strtotime($turno_vespertino_fin)) {
        $hora_str = date('H:i:s', $hora);
        if (!in_array($hora_str, $horas_ocupadas)) {
            $horas_disponibles[] = $hora_str;
        }
        $hora = strtotime('+30 minutes', $hora);
    }
}

// Mostrar el resultado en un archivo de texto
$txt = "Disponibilidad para el médico " . $medico_id . " el " . $fecha . ":\n\n";
if ($turno_medico == 'Matutino') {
    $txt .= "Horas Matutinas:\n";
} else {
    $txt .= "Horas Vespertinas:\n";
}

foreach ($horas_disponibles as $hora) {
    $txt .= $hora . "\n";
}

// Guardar el archivo de texto
file_put_contents('disponibilidad.txt', $txt);

// Mostrar el contenido en pantalla (opcional)
echo nl2br($txt);

$conexion->close();
?>

