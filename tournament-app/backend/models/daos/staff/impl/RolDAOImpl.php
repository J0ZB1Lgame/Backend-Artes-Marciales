<?php

require_once __DIR__ . '/../interfaces/IRolDAO.php';
require_once __DIR__ . '/../../../entities/staff/Rol.php';
require_once __DIR__ . '/../../../../config/conexion.php';
require_once __DIR__ . '/../../base/BaseDAO.php';

class RolDAOImpl extends BaseDAO implements IRolDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crear($rol) {
        global $conn;
        $sql = "INSERT INTO rol (nombre, descripcion) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $rol->getNombre(), $rol->getDescripcion());
        
        if ($stmt->execute()) {
            $rol->setIdRol($conn->insert_id);
            return $rol;
        }
        return false;
    }

    public function obtenerPorId($id) {
        global $conn;
        $sql = "SELECT * FROM rol WHERE id_rol = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return new Rol($row['id_rol'], $row['nombre'], $row['descripcion']);
        }
        return null;
    }

    public function actualizar($rol) {
        global $conn;
        $sql = "UPDATE rol SET nombre=?, descripcion=? WHERE id_rol=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $rol->getNombre(), $rol->getDescripcion(), $rol->getIdRol());
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        global $conn;
        $sql = "DELETE FROM rol WHERE id_rol = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function listarTodos() {
        global $conn;
        $sql = "SELECT * FROM rol";
        $result = $conn->query($sql);
        $rolList = [];
        
        while ($row = $result->fetch_assoc()) {
            $rolList[] = new Rol($row['id_rol'], $row['nombre'], $row['descripcion']);
        }
        return $rolList;
    }

    public function asignarAUsuario($id_usuario, $id_rol) {
        global $conn;
        $sql = "INSERT INTO usuario_rol (id_usuario, id_rol) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_usuario, $id_rol);
        return $stmt->execute();
    }

    public function revocarDeUsuario($id_usuario, $id_rol) {
        global $conn;
        $sql = "DELETE FROM usuario_rol WHERE id_usuario=? AND id_rol=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_usuario, $id_rol);
        return $stmt->execute();
    }

    public function listarPorUsuario($id_usuario) {
        global $conn;
        $sql = "SELECT r.* FROM rol r 
                INNER JOIN usuario_rol ur ON r.id_rol = ur.id_rol 
                WHERE ur.id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $rolList = [];
        
        while ($row = $result->fetch_assoc()) {
            $rolList[] = new Rol($row['id_rol'], $row['nombre'], $row['descripcion']);
        }
        return $rolList;
    }
}

?>
