<?php

require_once __DIR__ . '/../../../../backend/config/conexion.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/LogDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/entities/staff/Log.php';

class LogController {
    private $logDAO;
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->logDAO = new LogDAOImpl();
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

    public function registrarEvento(int $id): void {
        $evento = new Log(null, "Evento registrado por usuario {$id}", date('Y-m-d H:i:s'), $id);
        $this->logDAO->crearEvento($evento);
    }

    public function consultarLog(): array {
        $logs = $this->logDAO->consultarHistorial();
        $response = [];

        foreach ($logs as $log) {
            $response[] = [
                'id_log' => $log->getIdLog(),
                'accion' => $log->getAccion(),
                'fecha' => $log->getFecha(),
                'id_usuario' => $log->getIdUsuario()
            ];
        }

        return $response;
    }
}

?>