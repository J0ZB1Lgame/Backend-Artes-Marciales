<?php

require_once __DIR__ . '/../../../../backend/config/conexion.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/ZonaDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/entities/staff/Zona.php';

class ZonaController {
    private $zonaDAO;
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->zonaDAO = new ZonaDAOImpl();
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

    public function crearZona($zona) {
        if (is_array($zona)) {
            $zona = new Zona(null, $zona['nombre'] ?? '', $zona['descripcion'] ?? '');
        }
        return $this->zonaDAO->crear($zona);
    }

    public function buscarZona(int $id) {
        return $this->zonaDAO->obtenerPorId($id);
    }

    public function mostrarZona(): array {
        $zonas = $this->zonaDAO->listarTodos();
        $response = [];

        foreach ($zonas as $zona) {
            $response[] = [
                'id_zona' => $zona->getIdZona(),
                'nombre' => $zona->getNombre(),
                'descripcion' => $zona->getDescripcion()
            ];
        }

        return $response;
    }

    public function actualizarInformacion($zona) {
        if (is_array($zona)) {
            $zonaActual = $this->zonaDAO->obtenerPorId($zona['id_zona'] ?? 0);
            if (!$zonaActual) {
                return false;
            }
            if (isset($zona['nombre'])) {
                $zonaActual->setNombre($zona['nombre']);
            }
            if (isset($zona['descripcion'])) {
                $zonaActual->setDescripcion($zona['descripcion']);
            }
            return $this->zonaDAO->actualizar($zonaActual);
        }

        return $this->zonaDAO->actualizar($zona);
    }

    public function eliminarZona(int $id): bool {
        return $this->zonaDAO->eliminarPorId($id);
    }
}

?>