<?php

require_once __DIR__ . '/IRolDAO.php';
require_once __DIR__ . '/../../entities/staff/Rol.php';
require_once __DIR__ . '/../../../config/conexion.php';

class RolDAOImpl implements IRolDAO {

    public function crear($rol) {
        global $conn;
        $sql = "INSERT INTO rol (nombre_rol, descripcion) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $rol->getNombre_rol(), $rol->getDescripcion());
        
        if ($stmt->execute()) {
            $rol->setId_rol($conn->insert_id);
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
            return new Rol($row['id_rol'], $row['nombre_rol'], $row['descripcion']);
        }
        return null;
    }

    public function actualizar($rol) {
        global $conn;
        $sql = "UPDATE rol SET nombre_rol=?, descripcion=? WHERE id_rol=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $rol->getNombre_rol(), $rol->getDescripcion(), $rol->getId_rol());
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
            $rolList[] = new Rol($row['id_rol'], $row['nombre_rol'], $row['descripcion']);
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
            $rolList[] = new Rol($row['id_rol'], $row['nombre_rol'], $row['descripcion']);
        }
        return $rolList;
    }
}

?>
