<?php

require_once __DIR__ . '/IUsuarioDAO.php';
require_once __DIR__ . '/../../entities/staff/Usuario.php';
require_once __DIR__ . '/../../../config/conexion.php';

class UsuarioDAOImpl implements IUsuarioDAO {

    public function crear($usuario) {
        global $conn;
        $sql = "INSERT INTO usuario (username, password_hash, estado) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $usuario->getUsername(), $usuario->getPassword_hash(), $usuario->getEstado());
        
        if ($stmt->execute()) {
            $usuario->setId_usuario($conn->insert_id);
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
            return new Usuario($row['id_usuario'], $row['username'], $row['password_hash'], $row['estado'], $row['fecha_creacion']);
        }
        return null;
    }

    public function actualizar($usuario) {
        global $conn;
        $sql = "UPDATE usuario SET username=?, password_hash=?, estado=? WHERE id_usuario=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $usuario->getUsername(), $usuario->getPassword_hash(), $usuario->getEstado(), $usuario->getId_usuario());
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        global $conn;
        $sql = "DELETE FROM usuario WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function obtenerPorUsername($username) {
        global $conn;
        $sql = "SELECT * FROM usuario WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return new Usuario($row['id_usuario'], $row['username'], $row['password_hash'], $row['estado'], $row['fecha_creacion']);
        }
        return null;
    }
}

?>
