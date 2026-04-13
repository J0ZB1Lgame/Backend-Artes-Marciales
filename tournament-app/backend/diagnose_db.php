#!/usr/bin/env php
<?php
/**
 * Diagnóstico de Estructura de BD
 * Ejecuta: php diagnose_db.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "\n╔════════════════════════════════════════════╗\n";
echo "║   Diagnóstico de BD                        ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

require_once __DIR__ . '/config/conexion.php';

global $conn;

if (!$conn) {
    echo "❌ Error: No hay conexión a BD\n";
    exit(1);
}

echo "✅ Conexión OK a BD: " . $conn->select_db('torneo_db') . "\n\n";

// ================== ESTRUCTURA DE TABLA ==================
echo "📋 Estructura actual de la tabla 'usuario':\n";
echo "═════════════════════════════════════════════\n";

$result = $conn->query("DESCRIBE usuario");

if (!$result) {
    echo "❌ Error: " . $conn->error . "\n";
    exit(1);
}

$columnas = [];
while ($row = $result->fetch_assoc()) {
    $columnas[] = $row;
    echo "   Campo: " . str_pad($row['Field'], 20) . " | Tipo: " . str_pad($row['Type'], 20) . " | Nulo: " . ($row['Null'] == 'YES' ? 'Sí' : 'No') . "\n";
}

echo "\n";

// ================== MOSTRAR DATOS ==================
echo "📊 Datos actuales en tabla usuario:\n";
echo "═════════════════════════════════════════════\n";

$result = $conn->query("SELECT * FROM usuario LIMIT 5");

if (!$result) {
    echo "❌ Error: " . $conn->error . "\n";
    exit(1);
}

$numRows = $result->num_rows;
echo "Total de registros: $numRows\n\n";

if ($numRows > 0) {
    // Encabezados
    $fieldCount = $result->field_count;
    echo "Campos: ";
    for ($i = 0; $i < $fieldCount; $i++) {
        $meta = $result->fetch_field_direct($i);
        echo str_pad($meta->name, 25);
    }
    echo "\n";
    echo str_repeat("─", 25 * $fieldCount) . "\n";
    
    // Datos
    $result = $conn->query("SELECT * FROM usuario LIMIT 5");
    while ($row = $result->fetch_assoc()) {
        foreach ($row as $val) {
            $displayVal = is_null($val) ? 'NULL' : substr((string)$val, 0, 20);
            echo str_pad($displayVal, 25);
        }
        echo "\n";
    }
} else {
    echo "⚠️  No hay datos en la tabla\n";
}

echo "\n";

// ================== RECOMENDACIÓN ==================
echo "🔧 Recomendación:\n";
echo "═════════════════════════════════════════════\n";

// Buscar qué columnas podrían ser password
$hayPassword = false;
foreach ($columnas as $col) {
    if (stripos($col['Field'], 'password') !== false || 
        stripos($col['Field'], 'contrasena') !== false ||
        stripos($col['Field'], 'pwd') !== false) {
        echo "ℹ️ Encontrada columna de contraseña: '{$col['Field']}'\n";
        echo "   Usa esta columna en lugar de 'password'\n\n";
        $hayPassword = true;
    }
}

if (!$hayPassword) {
    echo "⚠️  No se encontró columna de contraseña\n";
    echo "   Debes crear la columna 'password' (VARCHAR 255)\n";
    echo "   Ejecuta este SQL:\n";
    echo "   ALTER TABLE usuario ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT '';\n";
}

echo "\n╔════════════════════════════════════════════╗\n";
echo "║   Diagnóstico Completado                   ║\n";
echo "╚════════════════════════════════════════════╝\n\n";
?>
