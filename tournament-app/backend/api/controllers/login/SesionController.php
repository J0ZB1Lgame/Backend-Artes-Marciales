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

    public function iniciarSesion($usuario, $staffTorneo = null): array|false {
        $username = null;
        $password = null;
        $staffTorneoInfo = null;

        if (is_string($usuario)) {
            $username = $usuario;
        } elseif ($usuario instanceof Usuario) {
            $username = $usuario->getUsername();
            $password = $usuario->getPassword();
        }

        if (is_string($staffTorneo)) {
            $password = $staffTorneo;
        } elseif (is_int($staffTorneo)) {
            $staffTorneoInfo = $staffTorneo;
        } elseif (is_object($staffTorneo) && method_exists($staffTorneo, 'getIdStaffTorneo')) {
            $staffTorneoInfo = $staffTorneo->getIdStaffTorneo();
        }

        if (!$username || !$password) {
            return false;
        }

        $usuarioEntity = $this->usuarioDAO->obtenerPorUsername($username);
        if (!$usuarioEntity || !password_verify($password, $usuarioEntity->getPassword()) || !$usuarioEntity->getEstado()) {
            return false;
        }

        // Cerrar sesiones activas previas del mismo usuario
        $this->sesionDAO->cerrarSesionesActivas($usuarioEntity->getIdUsuario());

        $sesion = new Sesion(null, date('Y-m-d H:i:s'), true, $usuarioEntity);
        $this->sesionDAO->crearSesion($sesion);
        $this->sesionActual = $sesion;

        // Registrar en log
        $log = new Log(null, "Inicio de sesión: {$username}", date('Y-m-d H:i:s'), $usuarioEntity->getIdUsuario());
        $this->logDAO->crearEvento($log);

        $response = [
            'idUsuario' => $usuarioEntity->getIdUsuario(),
            'username' => $usuarioEntity->getUsername(),
            'rol' => $usuarioEntity->getRol()
        ];

        if ($staffTorneoInfo !== null) {
            $response['staffTorneo'] = $staffTorneoInfo;
        }

        return $response;
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