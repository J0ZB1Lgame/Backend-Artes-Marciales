<?php

require_once __DIR__ . '/../../../daos/base/BaseDAO.php';
require_once __DIR__ . '/../interfaces/IUsuarioDAO.php';
require_once __DIR__ . '/../../../entities/login/Usuario.php';

class UsuarioDAOImpl extends BaseDAO implements IUsuarioDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crear($usuario) {
        $sql = "INSERT INTO usuario (username, password, rol, estado) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("sssi", $usuario->getUsername(), $usuario->getPassword(), $usuario->getRol(), $usuario->getEstado() ? 1 : 0);
        $stmt->execute();
        $usuario->setIdUsuario($this->connection->insert_id);
        return $usuario;
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuario WHERE id_usuario = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new Usuario($row['id_usuario'], $row['username'], $row['password'], $row['rol'], (bool) $row['estado']);
        }
        return null;
    }

    public function obtenerPorUsername(string $username) {
        $sql = "SELECT * FROM usuario WHERE username = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new Usuario($row['id_usuario'], $row['username'], $row['password'], $row['rol'], (bool) $row['estado']);
        }
        return null;
    }

    public function actualizar($usuario) {
        $sql = "UPDATE usuario SET username = ?, password = ?, rol = ?, estado = ? WHERE id_usuario = ?";
        $stmt = $this->connection->prepare($sql);
        $estado = $usuario->getEstado() ? 1 : 0;
        $stmt->bind_param("sssii", $usuario->getUsername(), $usuario->getPassword(), $usuario->getRol(), $estado, $usuario->getIdUsuario());
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        $sql = "DELETE FROM usuario WHERE id_usuario = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function listarTodos(): array {
        $sql = "SELECT * FROM usuario";
        $result = $this->connection->query($sql);
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = new Usuario($row['id_usuario'], $row['username'], $row['password'], $row['rol'], (bool) $row['estado']);
        }
        return $usuarios;
    }
}

?>