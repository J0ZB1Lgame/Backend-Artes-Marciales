<?php
/**
 * Script para remover el UNIQUE KEY de la tabla sesion
 * Ejecutar una sola vez: http://localhost/Backend-Artes-Marciales/tournament-app/fix_db_constraint.php
 */

require_once __DIR__ . '/config/conexion.php';

try {
    global $conn;
    
    echo "<h2>Removiendo UNIQUE KEY de la tabla sesion...</h2>";
    
    // Remover el UNIQUE KEY
    $sql1 = "ALTER TABLE sesion DROP INDEX id_usuario";
    if ($conn->query($sql1)) {
        echo "<p style='color:green;'>✅ UNIQUE KEY removido exitosamente</p>";
    } else {
        echo "<p style='color:red;'>⚠️ Error o el índice ya no existe: " . $conn->error . "</p>";
    }
    
    // Agregar KEY normal
    $sql2 = "ALTER TABLE sesion ADD KEY id_usuario (id_usuario)";
    if ($conn->query($sql2)) {
        echo "<p style='color:green;'>✅ KEY normal agregado exitosamente</p>";
    } else {
        echo "<p style='color:red;'>⚠️ Error al agregar KEY: " . $conn->error . "</p>";
    }
    
    echo "<hr>";
    echo "<h3 style='color:green;'>✅ Completado!</h3>";
    echo "<p>Ahora puedes:</p>";
    echo "<ul>";
    echo "<li>Hacer login múltiples veces con el mismo usuario</li>";
    echo "<li>Las sesiones previas se cerrarán automáticamente</li>";
    echo "<li>Solo habrá una sesión activa por usuario en cualquier momento</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
