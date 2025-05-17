<?php
// Conexión a la base de datos
include 'conexion.php';

// Recibir datos JSON
$datos = json_decode(file_get_contents("php://input"), true);

// Validar que los datos existen y no están vacíos
if (empty($datos["doctor"]) || empty($datos["selectedDate"]) || empty($datos["turno"])) {
    echo json_encode(["error" => "Datos incompletos o vacíos."]);
    exit;
}

$doctor = $datos["doctor"];
$selectedDate = $datos["selectedDate"];
$turno = $datos["turno"];

// Función para obtener horarios ocupados por cualquier médico en una fecha específica
function obtenerHorariosOcupados($conexion, $fecha, $turno) {
    $sql = "SELECT hora FROM Citas WHERE fecha = ? AND turno = ?";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(["error" => "Error en la consulta SQL."]);
        exit;
    }
    
    $stmt->bind_param("ss", $fecha, $turno);
    $stmt->execute();
    $result = $stmt->get_result();

    $horas_ocupadas = [];
    while ($row = $result->fetch_assoc()) {
        $horas_ocupadas[] = date("H:i", strtotime($row["hora"])); // Normalizar formato
    }

    $stmt->close();
    return $horas_ocupadas;
}

// Definir horarios posibles por turno
$horarios = [];
if ($turno == 'matutino') {
    $horarios = [
        '07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30'
    ];
} elseif ($turno == 'vespertino') {
    $horarios = [
        '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30'
    ];
}

// Obtener horarios ocupados por todos los médicos en esa fecha y turno
$horas_ocupadas = obtenerHorariosOcupados($conexion, $selectedDate, $turno);

// Calcular horarios disponibles
$horarios_disponibles = array_diff($horarios, $horas_ocupadas);

// Devolver respuesta JSON con horarios ocupados y disponibles
echo json_encode([
    "horas_ocupadas" => $horas_ocupadas,
    "horarios_disponibles" => array_values($horarios_disponibles)
]);

// Cerrar conexión
$conexion->close();
?>











