#!/usr/bin/env php
<?php
/**
 * Script de Importación de Nueva BD - torneo_new
 * Ejecuta: php import_new_db.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "\n╔════════════════════════════════════════════╗\n";
echo "║   Importador de BD - torneo_new            ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

// ================== CONEXIÓN INICIAL ==================
echo "🔌 Conectando a MySQL...\n";
$conn = new mysqli('localhost', 'root', '', '', 3306);

if ($conn->connect_error) {
    echo "❌ Error de conexión: " . $conn->connect_error . "\n";
    exit(1);
}

echo "✅ Conectado\n\n";

// ================== LEER SQL ==================
echo "📂 Leyendo archivo SQL...\n";
$sqlFile = __DIR__ . '/database/torneo_new.sql';

if (!file_exists($sqlFile)) {
    echo "❌ No se encontró: $sqlFile\n";
    exit(1);
}

$sqlContent = file_get_contents($sqlFile);
echo "✅ Archivo leído (" . strlen($sqlContent) . " bytes)\n\n";

// ================== EJECUTAR SQL ==================
echo "⚙️  Ejecutando consultas...\n";
echo "════════════════════════════════════\n";

$queries = explode(';', $sqlContent);
$successCount = 0;
$errorCount = 0;

foreach ($queries as $query) {
    $query = trim($query);
    
    if (empty($query) || substr($query, 0, 2) === '--' || substr($query, 0, 2) === '/*') {
        continue;
    }
    
    if ($conn->query($query) === true) {
        $successCount++;
        
        if (stripos($query, 'CREATE TABLE') !== false) {
            preg_match('/CREATE TABLE `?(\w+)`?/i', $query, $matches);
            if (!empty($matches[1])) {
                echo "✅ Tabla creada: {$matches[1]}\n";
            }
        } elseif (stripos($query, 'INSERT') !== false) {
            echo "✅ Datos insertados\n";
        }
    } else {
        $errorCount++;
        // Ignorar errores de DROP siempre presentes
        if (stripos($conn->error, 'already exists') === false) {
            echo "⚠️  " . $conn->error . "\n";
        }
    }
}

echo "════════════════════════════════════\n\n";

// ================== INSERTAR USUARIO DE PRUEBA ==================
echo "👤 Creando usuario de prueba...\n";
$conn->select_db('torneo_new');
$conn->set_charset("utf8");

$checkUser = $conn->query("SELECT COUNT(*) as cnt FROM usuario WHERE username = 'admin'");
$row = $checkUser->fetch_assoc();

if ($row['cnt'] == 0) {
    $username = 'admin';
    $password = password_hash('password123', PASSWORD_BCRYPT, ['cost' => 12]);
    $rol = 'Usuario administrador';
    $estado = 1;
    
    $stmt = $conn->prepare("INSERT INTO usuario (username, password, estado, rol) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssis", $username, $password, $estado, $rol);
        
        if ($stmt->execute()) {
            echo "✅ Usuario admin creado\n";
            echo "   Username: admin\n";
            echo "   Password: password123\n";
        } else {
            echo "❌ Error: " . $stmt->error . "\n";
        }
    }
} else {
    echo "ℹ️  Usuario admin ya existe\n";
}

echo "\n";

// ================== VERIFICAR ESTRUCTURA ==================
echo "📋 Tablas creadas:\n";
$result = $conn->query("SHOW TABLES");

if ($result) {
    $tables = [];
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
        echo "   ✅ {$row[0]}\n";
    }
} else {
    echo "   ❌ Error: " . $conn->error . "\n";
}

echo "\n╔════════════════════════════════════════════╗\n";
echo "║   Importación Completada                   ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

echo "✅ BD importada correctamente\n";
echo "   Base de datos: torneo_new\n";
echo "   Tablas: " . count($tables ?? []) . "\n\n";

$conn->close();
?>
