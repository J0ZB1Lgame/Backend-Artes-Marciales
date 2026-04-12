<?php

require_once __DIR__ . '/../../../../backend/config/conexion.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/UsuarioDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/entities/staff/Usuario.php';

class UsuarioController {
    private $usuarioDAO;
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
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

    public function crearUsuario($usuario) {
        if (is_array($usuario)) {
            $usuario = new Usuario(
                null,
                $usuario['username'] ?? '',
                $usuario['password'] ?? '',
                $usuario['rol'] ?? 'staff',
                isset($usuario['estado']) ? (bool) $usuario['estado'] : true
            );
        }

        return $this->usuarioDAO->crear($usuario);
    }

    public function buscarUsuario(int $id) {
        return $this->usuarioDAO->obtenerPorId($id);
    }

    public function mostrarUsuario(): array {
        $usuarios = $this->usuarioDAO->listarTodos();
        $response = [];

        foreach ($usuarios as $usuario) {
            $response[] = [
                'id_usuario' => $usuario->getIdUsuario(),
                'username' => $usuario->getUsername(),
                'rol' => $usuario->getRol(),
                'estado' => $usuario->getEstado()
            ];
        }

        return $response;
    }

    public function actualizarInformacion($usuario) {
        if (is_array($usuario)) {
            $usuarioActual = $this->usuarioDAO->obtenerPorId($usuario['id_usuario'] ?? 0);
            if (!$usuarioActual) {
                return false;
            }

            if (isset($usuario['username'])) {
                $usuarioActual->setUsername($usuario['username']);
            }
            if (isset($usuario['password'])) {
                $usuarioActual->setPassword($usuario['password']);
            }
            if (isset($usuario['rol'])) {
                $usuarioActual->setRol($usuario['rol']);
            }
            if (isset($usuario['estado'])) {
                $usuarioActual->setEstado((bool) $usuario['estado']);
            }

            return $this->usuarioDAO->actualizar($usuarioActual);
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
        $usuario->setPassword($password);
        return $this->usuarioDAO->actualizar($usuario);
    }

    public function verEstado(int $id) {
        $usuario = $this->usuarioDAO->obtenerPorId($id);
        return $usuario ? $usuario->getEstado() : null;
    }

    public function eliminarUsuario(int $id): bool {
        return $this->usuarioDAO->eliminarPorId($id);
    }
}

?>