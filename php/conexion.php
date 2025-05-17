<?php
$servidor = "";
$usuario = ""; 
$clave = ""; 
$baseDatos = "";
$port = ; 

// Crear conexión
$conexion = new mysqli($servidor, $usuario, $clave, $baseDatos, $port);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
