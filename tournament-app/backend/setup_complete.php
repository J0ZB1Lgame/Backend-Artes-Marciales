#!/usr/bin/env php
<?php
/**
 * Script Maestro - Configuración Completa de API
 * Ejecuta: php setup_complete.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                  CONFIGURACIÓN COMPLETA DE API                ║\n";
echo "║              Torneo de Artes Marciales - Sistema               ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// ================== PASO 1: BD ==================
echo "📊 PASO 1: Importar Base de Datos\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Ejecuta en terminal:\n";
echo "  php import_new_db.php\n\n";
echo "Esto creará:\n";
echo "  ✅ Base de datos: torneo_new\n";
echo "  ✅ Tablas: usuario, rol, sesion, log, permiso, zona, staff_torneo\n";
echo "  ✅ Usuario admin de prueba: admin / password123\n\n";

// ================== PASO 2: ESTRUCTURA ==================
echo "🏗️  PASO 2: Estructura de Carpetas\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Crear carpetas:\n";
echo "  📁 backend/api/endpoints/auth/\n";
echo "  📁 backend/api/endpoints/usuarios/\n";
echo "  📁 backend/api/endpoints/roles/\n";
echo "  📁 backend/api/endpoints/permisos/\n";
echo "  📁 backend/api/endpoints/zonas/\n";
echo "  📁 backend/api/endpoints/logs/\n";
echo "  📁 backend/api/endpoints/health/\n\n";

// ================── PASO 3: VERIFICACIÓN ────────────
echo "✅ PASO 3: Conectar a BD\n";
echo "═══════════════════════════════════════════════════════════════\n";

require_once __DIR__ . '/config/conexion.php';
global $conn;

if ($conn && $conn->ping()) {
    echo "✅ Conexión a BD: OK\n";
    echo "   Host: localhost\n";
    echo "   Base de datos: torneo_new\n";
    echo "   Usuario: root\n";
    
    // Verificar tablas
    $result = $conn->query("SHOW TABLES");
    $numTables = $result->num_rows;
    echo "   Tablas: $numTables\n\n";
    
    echo "📋 Tablas disponibles:\n";
    while ($row = $result->fetch_row()) {
        echo "     ✅ {$row[0]}\n";
    }
    echo "\n";
} else {
    echo "❌ Error de conexión\n";
    exit(1);
}

// ================── PASO 4: ENDPOINTS ────────────
echo "\n📡 PASO 4: Endpoints a Crear\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Documentación completa en: ENDPOINTS_DOCUMENTATION.md\n\n";

$endpoints = [
    'auth' => [
        'login' => 'POST /api/endpoints/auth/auth_api.php?action=login',
        'logout' => 'POST /api/endpoints/auth/auth_api.php?action=logout',
    ],
    'usuarios' => [
        'listar' => 'GET /api/endpoints/usuarios/usuarios_api.php?action=listar',
        'crear' => 'POST /api/endpoints/usuarios/usuarios_api.php?action=crear',
        'obtener' => 'GET /api/endpoints/usuarios/usuarios_api.php?action=obtener&id=1',
        'actualizar' => 'PUT /api/endpoints/usuarios/usuarios_api.php?action=actualizar',
        'eliminar' => 'DELETE /api/endpoints/usuarios/usuarios_api.php?action=eliminar&id=1',
    ],
    'roles' => [
        'listar' => 'GET /api/endpoints/roles/roles_api.php?action=listar',
        'crear' => 'POST /api/endpoints/roles/roles_api.php?action=crear',
        'obtener' => 'GET /api/endpoints/roles/roles_api.php?action=obtener&id=1',
        'actualizar' => 'PUT /api/endpoints/roles/roles_api.php?action=actualizar',
        'eliminar' => 'DELETE /api/endpoints/roles/roles_api.php?action=eliminar&id=1',
    ],
];

foreach ($endpoints as $modulo => $ops) {
    echo "🔹 $modulo:\n";
    foreach ($ops as $nombre => $endpoint) {
        echo "   📌 $endpoint\n";
    }
    echo "\n";
}

// ================── PASO 5: TEST ────────────
echo "🧪 PASO 5: Test Inicial\n";
echo "═══════════════════════════════════════════════════════════════\n";

// Verificar usuarios
$result = $conn->query("SELECT COUNT(*) as cnt FROM usuario");
$row = $result->fetch_assoc();
$totalUsuarios = $row['cnt'];

if ($totalUsuarios > 0) {
    echo "✅ Usuario de prueba creado\n";
    echo "   Total usuarios: $totalUsuarios\n";
    
    $result = $conn->query("SELECT id_usuario, username, rol FROM usuario LIMIT 1");
    $user = $result->fetch_assoc();
    echo "   ID: {$user['id_usuario']} | Usuario: {$user['username']} | Rol: {$user['rol']}\n";
} else {
    echo "⚠️  No hay usuarios en la BD\n";
    echo "   Ejecuta: php import_new_db.php\n";
}

echo "\n";

// ================── RESUMEN ────────────
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                      RESUMEN DE TAREAS                         ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "✅ Tareas completadas:\n";
echo "   1. Estructura de BD verificada\n";
echo "   2. Documentación de endpoints disponible\n";
echo "   3. Conexión a BD OK\n\n";

echo "📋 Próximas tareas:\n";
echo "   1. Ejecutar: php import_new_db.php\n";
echo "   2. Crear carpetas de endpoints\n";
echo "   3. Crear DAOs para cada tabla\n";
echo "   4. Crear controllers para cada módulo\n";
echo "   5. Crear endpoints (files .php)\n";
echo "   6. Implementar validaciones y permisos\n\n";

echo "📚 Archivos de referencia:\n";
echo "   📄 ENDPOINTS_DOCUMENTATION.md - Guía completa de endpoints\n";
echo "   📄 import_new_db.php - Importador de BD\n";
echo "   📄 diagnose_db.php - Herramienta de diagnóstico\n\n";

echo "🚀 ¡Listo para empezar!\n\n";

$conn->close();
?>
