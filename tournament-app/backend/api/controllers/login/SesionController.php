<?php

require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../models/daos/login/impl/SesionDAOImpl.php';
require_once __DIR__ . '/../../../models/daos/login/impl/UsuarioDAOImpl.php';
require_once __DIR__ . '/../../../models/daos/login/impl/LogDAOImpl.php';

class SesionController {
    private $sesionDAO;
    private $usuarioDAO;
    private $logDAO;
    private $sesionActual;

    public function __construct() {
        global $conn;
        $this->sesionDAO = new SesionDAOImpl();
        $this->usuarioDAO = new UsuarioDAOImpl();
        $this->logDAO = new LogDAOImpl();
    }

    public function iniciarSesion(string $username, string $password): array|false {
        $usuario = $this->usuarioDAO->obtenerPorUsername($username);
        if (!$usuario || !password_verify($password, $usuario->getPassword()) || !$usuario->getEstado()) {
            return false;
        }
        $sesion = new Sesion(null, date('Y-m-d H:i:s'), '', $usuario);
        $this->sesionDAO->crearSesion($sesion);
        $this->sesionActual = $sesion;
        // Registrar en log
        $log = new Log(null, "Inicio de sesión: {$username}", date('Y-m-d H:i:s'), $usuario->getIdUsuario());
        $this->logDAO->crearEvento($log);
        return [
            'idUsuario' => $usuario->getIdUsuario(),
            'username' => $usuario->getUsername(),
            'rol' => $usuario->getRol()
        ];
    }

    public function cerrarSesion(int $idSesion): void {
        $this->sesionDAO->cerrarSesion($idSesion);
        // Registrar en log
        $usuario = $this->sesionActual ? $this->sesionActual->getUsuarioActivo() : null;
        if ($usuario) {
            $log = new Log(null, "Cierre de sesión: {$usuario->getUsername()}", date('Y-m-d H:i:s'), $usuario->getIdUsuario());
            $this->logDAO->crearEvento($log);
        }
    }

    public function buscarSesion(int $id) {
        return $this->sesionDAO->buscarSesion($id);
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