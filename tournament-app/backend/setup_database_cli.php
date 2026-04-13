#!/usr/bin/env php
<?php
/**
 * Script de Configuración de Base de Datos (CLI)
 * Ejecuta desde terminal: php setup_database_cli.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "\n";
echo "╔════════════════════════════════════════════╗\n";
echo "║   Configuración de Base de Datos - CLI     ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

// Configuración
$host = 'localhost';
$user = 'root';
$pass = '';
$port = 3306;
$dbname = 'torneo_db';

echo "📝 Configuración:\n";
echo "   Host: $host\n";
echo "   Usuario: $user\n";
echo "   Puerto: $port\n";
echo "   Base de datos: $dbname\n\n";

// ================== CONEXIÓN INICIAL ==================
echo "🔌 Conectando a MySQL...\n";
$conn = new mysqli($host, $user, $pass, '', $port);

if ($conn->connect_error) {
    echo "❌ Error de conexión: " . $conn->connect_error . "\n";
    exit(1);
}

echo "✅ Conectado correctamente\n\n";

// ================== LEER Y EJECUTAR SQL ==================
echo "📂 Leyendo archivo SQL...\n";
$sqlFile = __DIR__ . '/database/tournament_db.sql';

if (!file_exists($sqlFile)) {
    echo "❌ No se encontró el archivo: $sqlFile\n";
    exit(1);
}

$sqlContent = file_get_contents($sqlFile);
echo "✅ Archivo SQL leído (" . strlen($sqlContent) . " bytes)\n\n";

// Ejecutar el SQL
$queries = explode(';', $sqlContent);

$successCount = 0;
$errorCount = 0;
$errors = [];

echo "⚙️  Ejecutando consultas SQL...\n";
echo "════════════════════════════════════\n";

foreach ($queries as $query) {
    $query = trim($query);
    
    // Saltar comentarios y líneas vacías
    if (empty($query) || substr($query, 0, 2) === '--') {
        continue;
    }
    
    if ($conn->query($query) === true) {
        $successCount++;
        
        // Extraer nombre de tabla
        if (stripos($query, 'CREATE') !== false) {
            preg_match('/CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+(\w+)/i', $query, $matches);
            if (!empty($matches[1])) {
                echo "✅ Tabla creada: {$matches[1]}\n";
            }
        } elseif (stripos($query, 'INSERT') !== false) {
            echo "✅ Datos insertados\n";
        }
    } else {
        $errorCount++;
        $errors[] = [
            'query' => $query,
            'error' => $conn->error
        ];
        echo "❌ Error: " . $conn->error . "\n";
    }
}

echo "════════════════════════════════════\n\n";

// ================== INSERTAR USUARIO DE PRUEBA ==================
echo "👤 Agregando usuario de prueba...\n";

$conn->select_db($dbname);
$conn->set_charset("utf8");

// Verificar si ya existe usuario admin
$checkUser = $conn->query("SELECT COUNT(*) as cnt FROM usuario WHERE username = 'admin'");
$row = $checkUser->fetch_assoc();

if ($row['cnt'] == 0) {
    $username = 'admin';
    $password = password_hash('password123', PASSWORD_BCRYPT, ['cost' => 12]);
    $rol = 'Administrador';
    $estado = 1;
    
    $stmt = $conn->prepare("INSERT INTO usuario (username, password, rol, estado) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssi", $username, $password, $rol, $estado);
        
        if ($stmt->execute()) {
            $successCount++;
            echo "✅ Usuario de prueba creado:\n";
            echo "   Username: admin\n";
            echo "   Password: password123\n";
        } else {
            $errorCount++;
            echo "❌ Error al crear usuario: " . $stmt->error . "\n";
        }
        $stmt->close();
    }
} else {
    echo "ℹ️  Usuario admin ya existe\n";
}

// ================== MOSTRAR RESULTADOS ==================
echo "\n";
echo "╔════════════════════════════════════════════╗\n";
echo "║          RESUMEN FINAL                     ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

echo "📊 Estadísticas:\n";
echo "   ✅ Exitosas: $successCount\n";
echo "   ❌ Errores: $errorCount\n\n";

if ($errorCount > 0) {
    echo "🚨 Detalles de errores:\n";
    foreach ($errors as $err) {
        echo "   Query: " . substr($err['query'], 0, 100) . "...\n";
        echo "   Error: " . $err['error'] . "\n\n";
    }
}

// ================== VERIFICAR BASE DE DATOS ==================
echo "\n📋 Verificando tabla usuario:\n";
$result = $conn->query("DESCRIBE usuario");
if ($result) {
    while ($col = $result->fetch_assoc()) {
        echo "   ✅ {$col['Field']} - {$col['Type']}\n";
    }
} else {
    echo "❌ Error al verificar tabla: " . $conn->error . "\n";
}

echo "\n";
echo "📝 Usuarios en base de datos:\n";
$result = $conn->query("SELECT id_usuario, username, rol, estado FROM usuario");
if ($result) {
    while ($user = $result->fetch_assoc()) {
        $estado = $user['estado'] ? 'Activo' : 'Inactivo';
        echo "   ID: {$user['id_usuario']} | Usuario: {$user['username']} | Rol: {$user['rol']} | Estado: $estado\n";
    }
} else {
    echo "❌ Error al listar usuarios: " . $conn->error . "\n";
}

$conn->close();

echo "\n✅ ¡Configuración completada!\n\n";
?>
