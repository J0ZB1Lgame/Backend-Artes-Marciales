#!/usr/bin/env php
<?php
/**
 * Script de Prueba Completa - Verifica BD y API
 * Ejecuta: php test_complete.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "\n╔════════════════════════════════════════════╗\n";
echo "║   Test Completo de API                     ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

// ================== CONEXIÓN ==================
echo "1️⃣ Conectando a BD...\n";
require_once __DIR__ . '/config/conexion.php';

global $conn;

if (!$conn) {
    echo "❌ Error: No hay conexión a BD\n";
    exit(1);
}

echo "✅ Conexión OK\n\n";

// ================== VERIFICAR TABLA ==================
echo "2️⃣ Verificando tabla usuario...\n";
$result = $conn->query("SHOW COLUMNS FROM usuario");
if (!$result) {
    echo "❌ Error: " . $conn->error . "\n";
    exit(1);
}

while ($col = $result->fetch_assoc()) {
    echo "   ✅ {$col['Field']} ({$col['Type']})\n";
}
echo "\n";

// ================== CONTAR USUARIOS ==================
echo "3️⃣ Usuarios en BD:\n";
$result = $conn->query("SELECT COUNT(*) as cnt FROM usuario");
$row = $result->fetch_assoc();
$total = $row['cnt'];
echo "   Total: $total usuarios\n";

if ($total > 0) {
    $result = $conn->query("SELECT id_usuario, username, rol FROM usuario");
    while ($user = $result->fetch_assoc()) {
        echo "   - ID: {$user['id_usuario']} | {$user['username']} ({$user['rol']})\n";
    }
}
echo "\n";

// ================== PROBAR DAO ==================
echo "4️⃣ Probando UsuarioDAO:\n";

require_once __DIR__ . '/models/daos/login/impl/UsuarioDAOImpl.php';
require_once __DIR__ . '/models/entities/login/Usuario.php';

try {
    $usuarioDAO = new UsuarioDAOImpl();
    $usuarios = $usuarioDAO->listarTodos();
    
    echo "   DAO devolvió: " . count($usuarios) . " usuarios\n";
    
    if (count($usuarios) > 0) {
        echo "   Tipo de primer usuario: " . get_class($usuarios[0]) . "\n";
        echo "   - ID: " . $usuarios[0]->getIdUsuario() . "\n";
        echo "   - Username: " . $usuarios[0]->getUsername() . "\n";
        echo "   - Rol: " . $usuarios[0]->getRol() . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error en DAO: " . $e->getMessage() . "\n";
}
echo "\n";

// ================== PROBAR CONTROLLER ==================
echo "5️⃣ Probando UsuarioController:\n";

require_once __DIR__ . '/api/controllers/login/UsuarioController.php';

try {
    $usuarioController = new UsuarioController();
    $usuarios = $usuarioController->mostrarUsuario();
    
    echo "   Controller devolvió: " . count($usuarios) . " usuarios\n";
    
    if (count($usuarios) > 0) {
        echo "   Tipo de primer elemento: " . gettype($usuarios[0]) . "\n";
        
        // Intentar serializar a JSON
        $json = json_encode($usuarios[0]);
        echo "   JSON: $json\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error en Controller: " . $e->getMessage() . "\n";
}
echo "\n";

// ================== SIMULAR RESPUESTA API ==================
echo "6️⃣ Simulando respuesta API completa:\n";

try {
    $usuarioController = new UsuarioController();
    $usuarios = $usuarioController->mostrarUsuario();
    
    $respuesta = [
        'status' => 'success',
        'message' => 'Usuarios listados',
        'data' => $usuarios
    ];
    
    $json = json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "   ✅ Respuesta JSON:\n";
    echo "   " . str_replace("\n", "\n   ", $json) . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n╔════════════════════════════════════════════╗\n";
echo "║   Test Completado                          ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

echo "✅ Ahora prueba en Postman:\n";
echo "   GET http://localhost/Backend-Artes-Marciales/tournament-app/backend/api/endpoints/login/login_api.php?action=listar-usuarios\n\n";
?>
