<?php
require_once __DIR__ . '/IRolDAO.php';
require_once __DIR__ . '/../Entidades/Rol.php';
require_once __DIR__ . '/../../Controlador/conexion.php';

class RolDAOImpl implements IRolDAO {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function crear($rol) {
        $stmt = $this->conn->prepare("INSERT INTO rol (nombre_rol, descripcion) VALUES (?, ?)");
        $stmt->bind_param("ss", $rol->getNombreRol(), $rol->getDescripcion());
        if (!$stmt->execute()) {
            throw new Exception('Error al crear rol del sistema: ' . $stmt->error);
        }
        $rol->setIdRol($this->conn->insert_id);
        $stmt->close();
        return $rol;
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM rol WHERE id_rol = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rol = null;
        if ($row = $result->fetch_assoc()) {
            $rol = new Rol($row['id_rol'], $row['nombre_rol'], $row['descripcion']);
        }
        $stmt->close();
        return $rol;
    }

    public function actualizar($rol) {
        $stmt = $this->conn->prepare("UPDATE rol SET nombre_rol = ?, descripcion = ? WHERE id_rol = ?");
        $stmt->bind_param("ssi", $rol->getNombreRol(), $rol->getDescripcion(), $rol->getIdRol());
        if (!$stmt->execute()) {
            throw new Exception('Error al actualizar rol del sistema: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function eliminarPorId($id) {
        $stmt = $this->conn->prepare("DELETE FROM rol WHERE id_rol = ?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception('Error al eliminar rol del sistema: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function listarTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM rol ORDER BY nombre_rol");
        $stmt->execute();
        $result = $stmt->get_result();
        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = new Rol($row['id_rol'], $row['nombre_rol'], $row['descripcion']);
        }
        $stmt->close();
        return $roles;
    }

    public function asignarAUsuario($id_usuario, $id_rol) {
        $stmt = $this->conn->prepare("INSERT INTO usuario_rol (id_usuario, id_rol) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_usuario, $id_rol);
        if (!$stmt->execute()) {
            throw new Exception('Error al asignar rol al usuario: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function revocarDeUsuario($id_usuario, $id_rol) {
        $stmt = $this->conn->prepare("DELETE FROM usuario_rol WHERE id_usuario = ? AND id_rol = ?");
        $stmt->bind_param("ii", $id_usuario, $id_rol);
        if (!$stmt->execute()) {
            throw new Exception('Error al revocar rol del usuario: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function listarPorUsuario($id_usuario) {
        $stmt = $this->conn->prepare(
            "SELECT r.* FROM rol r
             INNER JOIN usuario_rol u ON r.id_rol = u.id_rol
             WHERE u.id_usuario = ?"
        );
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = new Rol($row['id_rol'], $row['nombre_rol'], $row['descripcion']);
        }
        $stmt->close();
        return $roles;
    }
}