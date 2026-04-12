<?php

require_once __DIR__ . '/../../../../backend/config/conexion.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/LuchadorDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/entities/staff/Luchador.php';

class LuchadorController {
    private $luchadorDAO;
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->luchadorDAO = new LuchadorDAOImpl();
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

    public function crearLuchador($luchador) {
        if (is_array($luchador)) {
            $luchador = new Luchador(
                null,
                $luchador['nombre'] ?? '',
                $luchador['especie'] ?? '',
                isset($luchador['nivelDePoderKi']) ? (float) $luchador['nivelDePoderKi'] : 0.0,
                $luchador['origen'] ?? '',
                isset($luchador['estado']) ? (bool) $luchador['estado'] : true
            );
        }
        return $this->luchadorDAO->crear($luchador);
    }

    public function buscarLuchador(string $nombre) {
        return $this->luchadorDAO->buscarPorNombre($nombre);
    }

    public function mostrarLuchadores(): array {
        $luchadores = $this->luchadorDAO->listarTodos();
        $response = [];

        foreach ($luchadores as $luchador) {
            $response[] = [
                'id_luchador' => $luchador->getIdLuchador(),
                'nombre' => $luchador->getNombre(),
                'especie' => $luchador->getEspecie(),
                'nivelDePoderKi' => $luchador->getNivelDePoderKi(),
                'origen' => $luchador->getOrigen(),
                'estado' => $luchador->getEstado()
            ];
        }

        return $response;
    }

    public function actualizarInformacion($luchador) {
        if (is_array($luchador)) {
            $actual = $this->luchadorDAO->buscarPorNombre($luchador['nombre'] ?? '');
            if (!$actual) {
                return false;
            }
            if (isset($luchador['especie'])) {
                $actual->setEspecie($luchador['especie']);
            }
            if (isset($luchador['nivelDePoderKi'])) {
                $actual->setNivelDePoderKi((float) $luchador['nivelDePoderKi']);
            }
            if (isset($luchador['origen'])) {
                $actual->setOrigen($luchador['origen']);
            }
            if (isset($luchador['estado'])) {
                $actual->setEstado((bool) $luchador['estado']);
            }
            return $this->luchadorDAO->actualizar($actual);
        }
        return $this->luchadorDAO->actualizar($luchador);
    }

    public function verEstado(int $id) {
        $luchador = $this->luchadorDAO->obtenerPorId($id);
        return $luchador ? $luchador->getEstado() : null;
    }

    public function eliminarLuchador(int $id): bool {
        return $this->luchadorDAO->eliminarPorId($id);
    }
}

?>