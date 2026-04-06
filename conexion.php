<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "tienda";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(" Error al conectar con MySQL: " . $conn->connect_error);
} else {
    // echo " Conectado correctamente";
}
?>
