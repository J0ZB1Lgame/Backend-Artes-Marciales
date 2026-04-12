<?php

require_once __DIR__ . '/../interfaces/IUsuarioDAO.php';
require_once __DIR__ . '/../../entities/staff/Usuario.php';
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../base/BaseDAO.php';

class UsuarioDAOImpl extends BaseDAO implements IUsuarioDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crear($usuario) {
        global $conn;
        $sql = "INSERT INTO usuario (username, password, rol, estado) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $estado = $usuario->getEstado() ? 1 : 0;
        $stmt->bind_param("sssi", $usuario->getUsername(), $usuario->getPassword(), $usuario->getRol(), $estado);
        
        if ($stmt->execute()) {
            $usuario->setIdUsuario($conn->insert_id);
            return $usuario;
        }
        return false;
    }

    public function obtenerPorId($id) {
        global $conn;
        $sql = "SELECT * FROM usuario WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return new Usuario($row['id_usuario'], $row['username'], $row['password'], $row['rol'], (bool) $row['estado']);
        }
        return null;
    }

    public function actualizar($usuario) {
        global $conn;
        $sql = "UPDATE usuario SET username=?, password=?, rol=?, estado=? WHERE id_usuario=?";
        $stmt = $conn->prepare($sql);
        $estado = $usuario->getEstado() ? 1 : 0;
        $stmt->bind_param("sssii", $usuario->getUsername(), $usuario->getPassword(), $usuario->getRol(), $estado, $usuario->getIdUsuario());
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        global $conn;
        $sql = "DELETE FROM usuario WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function listarTodos() {
        global $conn;
        $sql = "SELECT * FROM usuario";
        $result = $conn->query($sql);
        $lista = [];

        while ($row = $result->fetch_assoc()) {
            $lista[] = new Usuario($row['id_usuario'], $row['username'], $row['password'], $row['rol'], (bool) $row['estado']);
        }
        return $lista;
    }

    public function obtenerPorUsername($username) {
        global $conn;
        $sql = "SELECT * FROM usuario WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return new Usuario($row['id_usuario'], $row['username'], $row['password'], $row['rol'], (bool) $row['estado']);
        }
        return null;
    }
}

?>
