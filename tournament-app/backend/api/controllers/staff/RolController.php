<?php

require_once __DIR__ . '/../../../../backend/config/conexion.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/RolDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/entities/staff/Rol.php';

class RolController {
    private $rolDAO;
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->rolDAO = new RolDAOImpl();
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

    public function crearRol($rol) {
        if (is_array($rol)) {
            $rol = new Rol(null, $rol['nombre'] ?? '', $rol['descripcion'] ?? '');
        }
        return $this->rolDAO->crear($rol);
    }

    public function buscarRol(int $id) {
        return $this->rolDAO->obtenerPorId($id);
    }

    public function mostrarRol(): array {
        $roles = $this->rolDAO->listarTodos();
        $response = [];

        foreach ($roles as $rol) {
            $response[] = [
                'id_rol' => $rol->getIdRol(),
                'nombre' => $rol->getNombre(),
                'descripcion' => $rol->getDescripcion()
            ];
        }

        return $response;
    }

    public function actualizarInformacion($rol) {
        if (is_array($rol)) {
            $rolActual = $this->rolDAO->obtenerPorId($rol['id_rol'] ?? 0);
            if (!$rolActual) {
                return false;
            }
            if (isset($rol['nombre'])) {
                $rolActual->setNombre($rol['nombre']);
            }
            if (isset($rol['descripcion'])) {
                $rolActual->setDescripcion($rol['descripcion']);
            }
            return $this->rolDAO->actualizar($rolActual);
        }

        return $this->rolDAO->actualizar($rol);
    }

    public function eliminarRol(int $id): bool {
        return $this->rolDAO->eliminarPorId($id);
    }
}

?>