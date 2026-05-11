<?php
/**
 * Script de un solo uso para establecer la contraseña del administrador.
 * Acceder desde: http://localhost/artes%20marciales/tournament-app/backend/reset_admin.php
 * Borrar este archivo después de usarlo.
 */

require_once __DIR__ . '/config/conexion.php';

$username  = 'admin';
$password  = 'admin123';
$rol       = 'admin';
$hash      = password_hash($password, PASSWORD_BCRYPT);

// Actualizar si existe, insertar si no existe
$stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id  = $row['id_usuario'];
    $upd = $conn->prepare("UPDATE usuario SET password = ?, rol = ?, estado = 1 WHERE id_usuario = ?");
    $upd->bind_param("ssi", $hash, $rol, $id);
    $upd->execute();
    $msg = "✅ Contraseña del admin actualizada correctamente.";
} else {
    $ins = $conn->prepare("INSERT INTO usuario (username, password, rol, estado) VALUES (?, ?, ?, 1)");
    $ins->bind_param("sss", $username, $hash, $rol);
    $ins->execute();
    $msg = "✅ Usuario admin creado correctamente.";
}

header('Content-Type: text/plain; charset=utf-8');
echo $msg . PHP_EOL;
echo "Usuario:    admin" . PHP_EOL;
echo "Contraseña: admin123" . PHP_EOL;
echo PHP_EOL;
echo "ELIMINA ESTE ARCHIVO después de usarlo." . PHP_EOL;
