<?php

require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../models/daos/login/impl/UsuarioDAOImpl.php';

class UsuarioController {
    private $usuarioDAO;

    public function __construct() {
        global $conn;
        $this->usuarioDAO = new UsuarioDAOImpl();
    }

    public function crearUsuario($datos) {
        $passwordHashed = password_hash($datos['password'], PASSWORD_BCRYPT);
        $usuario = new Usuario(null, $datos['username'], $passwordHashed, $datos['rol'], true);
        return $this->usuarioDAO->crear($usuario);
    }

    public function buscarUsuario(int $id) {
        return $this->usuarioDAO->obtenerPorId($id);
    }

    public function mostrarUsuario(): array {
        return $this->usuarioDAO->listarTodos();
    }

    public function actualizarInformacion($datos) {
        $usuario = $this->usuarioDAO->obtenerPorId($datos['id_usuario']);
        if (!$usuario) {
            return false;
        }
        if (isset($datos['username'])) {
            $usuario->setUsername($datos['username']);
        }
        if (isset($datos['rol'])) {
            $usuario->setRol($datos['rol']);
        }
        if (isset($datos['estado'])) {
            $usuario->setEstado((bool) $datos['estado']);
        }
        return $this->usuarioDAO->actualizar($usuario);
    }

    public function actualizarUsername(int $id, string $username): bool {
        $usuario = $this->usuarioDAO->obtenerPorId($id);
        if (!$usuario) {
            return false;
        }
        $usuario->setUsername($username);
        return $this->usuarioDAO->actualizar($usuario);
    }

    public function actualizarPassword(int $id, string $password): bool {
        $usuario = $this->usuarioDAO->obtenerPorId($id);
        if (!$usuario) {
            return false;
        }
        $passwordHashed = password_hash($password, PASSWORD_BCRYPT);
        $usuario->setPassword($passwordHashed);
        return $this->usuarioDAO->actualizar($usuario);
    }

    public function verEstado(int $id) {
        $usuario = $this->usuarioDAO->obtenerPorId($id);
        return $usuario ? $usuario->getEstado() : null;
    }

    public function eliminarUsuario(int $id): bool {
        return $this->usuarioDAO->eliminarPorId($id);
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