<?php

require_once __DIR__ . '/../../base/BaseDAO.php';
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
        
        if (!$stmt) {
            throw new Exception("Error en preparación de consulta: " . $this->connection->error);
        }
        
        // Guardar valores en variables locales para bind_param
        $username = $usuario->getUsername();
        $password = $usuario->getPassword();
        $rol = $usuario->getRol();
        $estado = $usuario->getEstado() ? 1 : 0;
        
        // Tipos: s (string), s (string), s (string), i (int)
        $stmt->bind_param("sssi", $username, $password, $rol, $estado);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al crear usuario: " . $stmt->error);
        }
        
        $usuario->setIdUsuario($this->connection->insert_id);
        return $usuario;
    }

    public function obtenerPorId($id) {
        if (!is_int($id) || $id <= 0) {
            throw new Exception("ID inválido");
        }
        
        $sql = "SELECT * FROM usuario WHERE id_usuario = ?";
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error en preparación de consulta: " . $this->connection->error);
        }
        
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al buscar usuario: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new Usuario($row['id_usuario'], $row['username'], $row['password'], $row['rol'], (bool) $row['estado']);
        }
        return null;
    }

    public function obtenerPorUsername(string $username) {
        if (empty($username)) {
            throw new Exception("Username no puede estar vacío");
        }
        
        $sql = "SELECT * FROM usuario WHERE username = ?";
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error en preparación de consulta: " . $this->connection->error);
        }
        
        $stmt->bind_param("s", $username);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al buscar usuario: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new Usuario($row['id_usuario'], $row['username'], $row['password'], $row['rol'], (bool) $row['estado']);
        }
        return null;
    }

    public function actualizar($usuario) {
        $sql = "UPDATE usuario SET username = ?, password = ?, rol = ?, estado = ? WHERE id_usuario = ?";
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error en preparación de consulta: " . $this->connection->error);
        }
        
        // Guardar valores en variables locales para bind_param
        $username = $usuario->getUsername();
        $password = $usuario->getPassword();
        $rol = $usuario->getRol();
        $estado = $usuario->getEstado() ? 1 : 0;
        $id_usuario = $usuario->getIdUsuario();
        
        // Tipos: s, s, s, i, i
        $stmt->bind_param("sssii", $username, $password, $rol, $estado, $id_usuario);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar usuario: " . $stmt->error);
        }
        
        return true;
    }

    public function eliminarPorId($id) {
        if (!is_int($id) || $id <= 0) {
            throw new Exception("ID inválido");
        }
        
        $sql = "DELETE FROM usuario WHERE id_usuario = ?";
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error en preparación de consulta: " . $this->connection->error);
        }
        
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar usuario: " . $stmt->error);
        }
        
        return $stmt->affected_rows > 0;
    }

    public function listarTodos(): array {
        $sql = "SELECT * FROM usuario";
        $result = $this->connection->query($sql);
        
        if (!$result) {
            throw new Exception("Error al listar usuarios: " . $this->connection->error);
        }
        
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = new Usuario($row['id_usuario'], $row['username'], $row['password'], $row['rol'], (bool) $row['estado']);
        }
        return $usuarios;
    }
}

?>