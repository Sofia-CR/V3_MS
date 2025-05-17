<?php
include 'conexion.php'; 
include 'datospersonales.php';

$curp = $_SESSION['curp'];
$estatura = $_POST['Estatura'];
$peso = $_POST['Peso'];
$cintura = $_POST['Cintura'];
$sexo = $_POST['Sexo'];
$gsangre = $_POST['GrupoSanguineo'];
$csueno = $_POST['CalidadSueno'];
$halimen = $_POST['HabitosAlimenticios'];
$diabetes = $_POST['Diabetes'];
$hipertension = $_POST['Hipertension'];
$alergias = $_POST['Alergias'];
$calcohol = $_POST['ConsumoAlcohol'];
$ctabaco = $_POST['ConsumoTabaco'];
$enfermedades = $_POST['HistorialEnfermedades'];
$cirujias = $_POST['CirugiasPrevias'];
$mactual = $_POST['MedicaciónActual'];
$nafisica = $_POST['NivelActividadFisica'];
$antfami = $_POST['AntecedentesFamiliares'];
$visitas = $_POST['VisitasMedicas'];
$lesiones = $_POST['HistorialLesiones'];
$prespiratorios = $_POST['ProblemasRespiratorios'];
$pcardiovasculares = $_POST['ProblemasCardiovasculares'];
$ementales = $_POST['EnfermedadesMentales'];
$embarazo = $_POST['Embarazo'];
$adermatologicas = $_POST['AfeccionesDermatologicas'];
$tsueños = $_POST['TrastornosSueño'];

// Verificar si ya existe el CURP en DatosSalud
$consulta = $conexion->prepare("SELECT curp FROM DatosSalud WHERE curp = ?");
$consulta->bind_param("s", $curp);
$consulta->execute();
$consulta->store_result();

if ($consulta->num_rows > 0) {
    // UPDATE
    $sql = "UPDATE DatosSalud SET estatura=?, peso=?, cintura=?, sexo=?, gsangre=?, csueno=?, halimen=?, diabetes=?, hipertension=?, alergias=?, calcohol=?, ctabaco=?, enfermedades=?, cirujias=?, mactual=?, nafisica=?, antfami=?, visitas=?, lesiones=?, prespiratorios=?, pcardiovasculares=?, ementales=?, embarazo=?, adermatologicas=?, tsueños=? WHERE curp=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssssssssssssssssssssss", $estatura, $peso, $cintura, $sexo, $gsangre, $csueno, $halimen, $diabetes, $hipertension, $alergias, $calcohol, $ctabaco, $enfermedades, $cirujias, $mactual, $nafisica, $antfami, $visitas, $lesiones, $prespiratorios, $pcardiovasculares, $ementales, $embarazo, $adermatologicas, $tsueños, $curp);
} else {
    // INSERT
    $sql = "INSERT INTO DatosSalud (curp, estatura, peso, cintura, sexo, gsangre, csueno, halimen, diabetes, hipertension, alergias, calcohol, ctabaco, enfermedades, cirujias, mactual, nafisica, antfami, visitas, lesiones, prespiratorios, pcardiovasculares, ementales, embarazo, adermatologicas, tsueños) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssssssssssssssssssssss", $curp, $estatura, $peso, $cintura, $sexo, $gsangre, $csueno, $halimen, $diabetes, $hipertension, $alergias, $calcohol, $ctabaco, $enfermedades, $cirujias, $mactual, $nafisica, $antfami, $visitas, $lesiones, $prespiratorios, $pcardiovasculares, $ementales, $embarazo, $adermatologicas, $tsueños);
}

if ($stmt->execute()) {
    header("Location: ../html/salud.html?guardado=ok");
    exit;
} else {
    echo "Error al guardar los datos: " . $stmt->error;
}

$stmt->close();
$consulta->close();
$conexion->close();
?>
