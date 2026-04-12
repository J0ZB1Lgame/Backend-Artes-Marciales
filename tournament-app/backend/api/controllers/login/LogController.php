<?php

require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../models/daos/login/impl/LogDAOImpl.php';

class LogController {
    private $logDAO;

    public function __construct() {
        global $conn;
        $this->logDAO = new LogDAOImpl();
    }

    public function registrarEvento(int $idUsuario, string $accion): void {
        $log = new Log(null, $accion, date('Y-m-d H:i:s'), $idUsuario);
        $this->logDAO->crearEvento($log);
    }

    public function consultarLog(): array {
        return $this->logDAO->consultarHistorial();
    }

    public function jsonResponse($data, $message = "OK", $status = 200) {
        http_response_code($status);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public function jsonError($message, $status = 400) {
        http_response_code($status);
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }
}

?>