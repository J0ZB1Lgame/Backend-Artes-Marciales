<?php
require_once 'IUsuarioDAO.php';
require_once '../../Controlador/conexion.php';

class UsuarioDAOImpl implements IUsuarioDAO {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function crear($usuario) {
        $stmt = $this->conn->prepare("INSERT INTO usuario (username, password_hash, estado) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $usuario->getUsername(), $usuario->getPasswordHash(), $usuario->getEstado());
        $stmt->execute();
        $usuario->setIdUsuario($this->conn->insert_id);
        $stmt->close();
        return $usuario;
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $usuario = new Usuario($row['id_usuario'], $row['username'], $row['password_hash'], $row['estado'], $row['fecha_creacion']);
        } else {
            $usuario = null;
        }
        $stmt->close();
        return $usuario;
    }

    public function actualizar($usuario) {
        $stmt = $this->conn->prepare("UPDATE usuario SET username = ?, password_hash = ?, estado = ? WHERE id_usuario = ?");
        $stmt->bind_param("sssi", $usuario->getUsername(), $usuario->getPasswordHash(), $usuario->getEstado(), $usuario->getIdUsuario());
        $stmt->execute();
        $stmt->close();
    }

    public function eliminarPorId($id) {
        $stmt = $this->conn->prepare("DELETE FROM usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    public function obtenerPorUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM usuario WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $usuario = new Usuario($row['id_usuario'], $row['username'], $row['password_hash'], $row['estado'], $row['fecha_creacion']);
        } else {
            $usuario = null;
        }
        $stmt->close();
        return $usuario;
    }
}
?>