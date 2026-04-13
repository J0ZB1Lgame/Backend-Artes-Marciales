#!/usr/bin/env php
<?php
/**
 * Script de Prueba - Verifica que la BD y los datos sean accesibles
 * Ejecuta desde terminal: php test_api.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "\n╔════════════════════════════════════════════╗\n";
echo "║   Prueba de Conexión API                   ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

// Incluir dependencias
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/models/daos/login/impl/UsuarioDAOImpl.php';
require_once __DIR__ . '/api/controllers/login/UsuarioController.php';

global $conn;

echo "✅ Dependencias cargadas\n";
echo "🔌 Conexión a BD: " . ($conn ? "Activa" : "Inactiva") . "\n\n";

// ================== VERIFICAR ESTRUCTURA DE BD ==================
echo "📋 Verificando tabla usuario:\n";
$result = $conn->query("DESCRIBE usuario");
if ($result) {
    while ($col = $result->fetch_assoc()) {
        echo "   ✅ {$col['Field']} ({$col['Type']})\n";
    }
} else {
    echo "   ❌ Error: " . $conn->error . "\n";
}

echo "\n";

// ================== LISTAR USUARIOS ==================
echo "📝 Usuarios registrados en BD:\n";
$result = $conn->query("SELECT COUNT(*) as cnt FROM usuario");
$row = $result->fetch_assoc();
$totalUsuarios = $row['cnt'] ?? 0;

if ($totalUsuarios == 0) {
    echo "   ⚠️  No hay usuarios registrados\n";
} else {
    $result = $conn->query("SELECT id_usuario, username, rol, estado FROM usuario");
    if ($result) {
        while ($user = $result->fetch_assoc()) {
            $estado = $user['estado'] ? '✅ Activo' : '❌ Inactivo';
            echo "   ID: {$user['id_usuario']} | Usuario: {$user['username']} | Rol: {$user['rol']} | $estado\n";
        }
    }
}

echo "\n";

// ================== PROBAR CONTROLADOR ==================
echo "🧪 Probando UsuarioController:\n";

try {
    $usuarioController = new UsuarioController();
    
    // Intentar listar usuarios
    echo "\n   1️⃣ Listando usuarios...\n";
    $usuarios = $usuarioController->mostrarUsuario();
    
    if (empty($usuarios)) {
        echo "   ⚠️  No hay usuarios para listar\n";
    } else {
        foreach ($usuarios as $usuario) {
            echo "   ✅ " . $usuario->getUsername() . " (" . $usuario->getRol() . ")\n";
        }
    }
    
    // Intentar buscar usuario por ID
    if ($totalUsuarios > 0) {
        echo "\n   2️⃣ Buscando usuario por ID (1)...\n";
        $usuario = $usuarioController->buscarUsuario(1);
        if ($usuario) {
            echo "   ✅ Usuario encontrado: " . $usuario->getUsername() . "\n";
        } else {
            echo "   ❌ Usuario no encontrado\n";
        }
    }
    
    echo "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";
echo "╔════════════════════════════════════════════╗\n";
echo "║   Prueba Completada                        ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

echo "✅ Próximo paso: Prueba en Postman\n";
echo "   GET http://localhost/Backend-Artes-Marciales/tournament-app/backend/api/endpoints/login/login_api.php?action=listar-usuarios\n\n";
?>
