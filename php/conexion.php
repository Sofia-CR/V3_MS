<?php
$servidor = "localhost";
$usuario = "root"; 
$clave = ""; 
$baseDatos = "Hospital";
$port = 3308; 

// Crear conexión
$conexion = new mysqli($servidor, $usuario, $clave, $baseDatos, $port);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
