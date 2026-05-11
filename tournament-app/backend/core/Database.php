<?php
/**
 * Database Singleton Class
 * Maneja la conexión a la BD leyendo variables de entorno desde .env
 */
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        // Leer archivo .env
        $envFile = __DIR__ . '/../../.env';
        $envVars = [];

        if (file_exists($envFile)) {
            $envVars = parse_ini_file($envFile);
        }

        // Variables con fallback a hardcoded defaults
        $host = $envVars['DB_HOST'] ?? 'localhost';
        $dbname = $envVars['DB_NAME'] ?? 'torneo_new';
        $user = $envVars['DB_USER'] ?? 'root';
        $pass = $envVars['DB_PASS'] ?? '';
        $port = $envVars['DB_PORT'] ?? 3306;

        // Conectar a BD
        $this->conn = @new mysqli($host, $user, $pass, $dbname, $port);

        // Verificar conexión
        if ($this->conn->connect_error) {
            throw new Exception("Error en conexión: " . $this->conn->connect_error);
        }

        // Set charset
        $this->conn->set_charset("utf8");
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
