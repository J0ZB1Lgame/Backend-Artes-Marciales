<?php
// Configuración de la base de datos
$host = 'localhost'; // Cambia si es necesario
$dbname = 'torneo_db'; // Nombre de la BD
$username = 'root'; // Usuario de BD
$password = ''; // Contraseña de BD

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer charset
$conn->set_charset("utf8");
?>