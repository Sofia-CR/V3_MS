<?php
// Configuración de la conexión a la base de datos
include 'conexion.php';

// Leer los datos del cuerpo de la solicitud (POST en formato JSON)
$datos = json_decode(file_get_contents("php://input"), true);

// Validar que se recibieron los parámetros necesarios
$doctor = isset($datos['doctor']) ? $datos['doctor'] : null;
$turno = isset($datos['turno']) ? $datos['turno'] : null;

if ($doctor === null || $turno === null) {
    http_response_code(400); // Código de error 400 (Bad Request)
    echo json_encode(["error" => "Datos no válidos o incompletos"]);
    exit;
}


// Fecha actual y fecha dos meses después
$fecha_actual = date('Y-m-d'); // Obtiene la fecha actual
$fecha_dos_meses = date('Y-m-d', strtotime('+2 months')); // Calcula la fecha dos meses después

// Generar todas las fechas dentro del rango (desde fecha actual hasta dos meses después)
$rangoDeFechas = [];
$inicio = strtotime($fecha_actual);
$fin = strtotime($fecha_dos_meses);

while ($inicio <= $fin) {
    $rangoDeFechas[] = date('Y-m-d', $inicio); // Agrega cada día al rango
    $inicio = strtotime('+1 day', $inicio); // Avanza un día
}

// Consultar el turno del médico
$sql_turno = "SELECT turno FROM Medicos WHERE id_medico = ?";
$stmt_turno = $conexion->prepare($sql_turno);
if ($stmt_turno === false) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}
$stmt_turno->bind_param('i', $doctor);
$stmt_turno->execute();
$result_turno = $stmt_turno->get_result();

$turno_medico = null;
if ($row_turno = $result_turno->fetch_assoc()) {
    $turno_medico = $row_turno['turno'];
}

// Definir las horas del turno matutino y vespertino
$horas_turno_matutino = [
    '07:00:00', '07:30:00', '08:00:00', '08:30:00', '09:00:00', '09:30:00',
    '10:00:00', '10:30:00', '11:00:00', '11:30:00', '12:00:00', '12:30:00', '13:00:00', '13:30:00'
];

$horas_turno_vespertino = [
    '14:00:00', '14:30:00', '15:00:00', '15:30:00', '16:00:00', '16:30:00',
    '17:00:00', '17:30:00', '18:00:00', '18:30:00', '19:00:00', '19:30:00', '20:00:00', '20:30:00'
];

// Consulta SQL para obtener las fechas ocupadas y agendadas dentro del rango de fechas
$sql = "SELECT fecha, hora, id_medico 
        FROM citas 
        WHERE id_medico = ? 
        AND turno = ? 
        AND estatus = 'Agendada' 
        AND fecha BETWEEN ? AND ?";

$stmt = $conexion->prepare($sql);
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}
$stmt->bind_param("isss", $doctor, $turno, $fecha_actual, $fecha_dos_meses);
$stmt->execute();
$result = $stmt->get_result();

// Array para almacenar las fechas ocupadas
$ocupadas = [];
while ($row = $result->fetch_assoc()) {
    $ocupadas[] = $row;
}

// Función para obtener las horas disponibles por fecha
function obtener_horas_disponibles_por_fecha($fecha, $turno, $ocupadas, $turno_matutino, $turno_vespertino, $doctor) {
    // Filtrar las horas ocupadas solo para esta fecha y este médico
    $horas_ocupadas = array_column(array_filter($ocupadas, function($ocupada) use ($fecha, $doctor) {
        return $ocupada['fecha'] === $fecha && $ocupada['id_medico'] == $doctor;
    }), 'hora');

    $horas_disponibles = [];

    if ($turno == 'Matutino') {
        foreach ($turno_matutino as $hora) {
            if (!in_array($hora, $horas_ocupadas)) {
                $horas_disponibles[] = $hora;
            }
        }
    } elseif ($turno == 'Vespertino') {
        foreach ($turno_vespertino as $hora) {
            if (!in_array($hora, $horas_ocupadas)) {
                $horas_disponibles[] = $hora;
            }
        }
    }

    return $horas_disponibles;
}


// Crear estructura para el JSON de respuesta
$respuesta = [
    "ocupadas" => $ocupadas,
    "disponibilidad" => []
];

// Procesar todas las fechas del rango
foreach ($rangoDeFechas as $fecha) {
    $horas_disponibles = obtener_horas_disponibles_por_fecha(
        $fecha, $turno_medico, $ocupadas,
        $horas_turno_matutino, $horas_turno_vespertino, $doctor
    );

    // Solo incluir la fecha si tiene horarios disponibles
    if (count($horas_disponibles) > 0) {
        $respuesta['disponibilidad'][$fecha] = $horas_disponibles;
    }
}



// Enviar la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($respuesta);

// Cerrar la conexión
$conexion->close();
?>
