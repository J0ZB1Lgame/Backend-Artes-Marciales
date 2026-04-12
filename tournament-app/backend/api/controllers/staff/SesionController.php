<?php

require_once __DIR__ . '/../../../../backend/config/conexion.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/SesionDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/UsuarioDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/entities/staff/Usuario.php';
require_once __DIR__ . '/../../../../backend/models/entities/staff/Sesion.php';

class SesionController {
    private $sesionDAO;
    private $usuarioDAO;
    private $sesionActual;
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->sesionDAO = new SesionDAOImpl();
        $this->usuarioDAO = new UsuarioDAOImpl();
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

    public function iniciarSesion(string $username, string $password): bool {
        $usuario = $this->usuarioDAO->obtenerPorUsername($username);
        if (!$usuario || $usuario->getPassword() !== $password || !$usuario->getEstado()) {
            return false;
        }

        $this->sesionActual = new Sesion(null, date('Y-m-d H:i:s'), '', $usuario);
        $this->sesionDAO->crearSesion($this->sesionActual);
        return true;
    }

    public function buscarSesion(int $id) {
        return $this->sesionDAO->buscarSesion($id);
    }

    public function cerrarSesion(): void {
        if ($this->sesionActual && $this->sesionActual->getIdSesion()) {
            $this->sesionDAO->cerrarSesion($this->sesionActual->getIdSesion());
        }
    }

    public function validarPermisos(string $rolRequerido): bool {
        $usuario = $this->getUsuarioLogueado();
        if (!$usuario) {
            return false;
        }
        return $usuario->getRol() === $rolRequerido || $usuario->getRol() === 'Administrador';
    }

    public function getUsuarioLogueado() {
        return $this->sesionActual ? $this->sesionActual->getUsuarioActivo() : null;
    }
}

?>