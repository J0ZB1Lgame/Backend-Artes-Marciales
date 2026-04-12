<?php

require_once __DIR__ . '/ITipoRolStaffDAO.php';
require_once __DIR__ . '/../../entities/staff/TipoRolStaff.php';
require_once __DIR__ . '/../../../config/conexion.php';

class TipoRolStaffDAOImpl implements ITipoRolStaffDAO {

    public function crear($tipoRol) {
        global $conn;
        $sql = "INSERT INTO tipo_rol_staff (nombre, descripcion) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $tipoRol->getNombre(), $tipoRol->getDescripcion());
        
        if ($stmt->execute()) {
            $tipoRol->setId_tipo_rol($conn->insert_id);
            return $tipoRol;
        }
        return false;
    }

    public function obtenerPorId($id) {
        global $conn;
        $sql = "SELECT * FROM tipo_rol_staff WHERE id_tipo_rol = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return new TipoRolStaff($row['id_tipo_rol'], $row['nombre'], $row['descripcion']);
        }
        return null;
    }

    public function actualizar($tipoRol) {
        global $conn;
        $sql = "UPDATE tipo_rol_staff SET nombre=?, descripcion=? WHERE id_tipo_rol=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $tipoRol->getNombre(), $tipoRol->getDescripcion(), $tipoRol->getId_tipo_rol());
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        global $conn;
        $sql = "DELETE FROM tipo_rol_staff WHERE id_tipo_rol = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function listarTodos() {
        global $conn;
        $sql = "SELECT * FROM tipo_rol_staff";
        $result = $conn->query($sql);
        $tipoRolList = [];
        
        while ($row = $result->fetch_assoc()) {
            $tipoRolList[] = new TipoRolStaff($row['id_tipo_rol'], $row['nombre'], $row['descripcion']);
        }
        return $tipoRolList;
    }
}

?>
