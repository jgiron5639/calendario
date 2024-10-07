<?php
// Datos de la conexión
$host = "localhost";
$user = "root";
$password = "";
$db = "calendario";

// Crear conexión
$mysqli = new mysqli($host, $user, $password, $db);

// Verificar la conexión
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}
?>
