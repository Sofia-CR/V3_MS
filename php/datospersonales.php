<?php
session_start(); 
if (!isset($_SESSION["curp"])) {
    echo json_encode(["error" => "No se encontró CURP en la sesión"]);
    exit;
}
$curp = $_SESSION["curp"];
echo json_encode(["curp" => $curp]); // solo para probar
?>
