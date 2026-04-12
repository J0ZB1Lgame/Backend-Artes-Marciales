<?php
/**
 * Wrapper de conexión para compatibilidad con código legado
 * Usa el patrón Singleton de Database.php
 */

require_once __DIR__ . '/../core/Database.php';

// Exportar conexión global para compatibilidad con DAOs existentes
$database = Database::getInstance();
$conn = $database->getConnection();
?>
