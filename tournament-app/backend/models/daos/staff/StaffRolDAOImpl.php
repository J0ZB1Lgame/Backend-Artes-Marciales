<?php

require_once __DIR__ . '/IStaffRolDAO.php';
require_once __DIR__ . '/../../entities/staff/StaffRol.php';
require_once __DIR__ . '/../../../config/conexion.php';

class StaffRolDAOImpl implements IStaffRolDAO {

    public function asignar($id_staff, $id_tipo_rol) {
        global $conn;
        $sql = "INSERT INTO staff_rol (id_staff, id_tipo_rol) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_staff, $id_tipo_rol);
        return $stmt->execute();
    }

    public function revocar($id_staff, $id_tipo_rol) {
        global $conn;
        $sql = "DELETE FROM staff_rol WHERE id_staff=? AND id_tipo_rol=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_staff, $id_tipo_rol);
        return $stmt->execute();
    }

    public function listarRolesPorStaff($id_staff) {
        global $conn;
        $sql = "SELECT * FROM staff_rol WHERE id_staff = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_staff);
        $stmt->execute();
        $result = $stmt->get_result();
        $rolesList = [];
        
        while ($row = $result->fetch_assoc()) {
            $rolesList[] = new StaffRol($row['id_staff'], $row['id_tipo_rol'], $row['fecha_asignacion']);
        }
        return $rolesList;
    }

    public function listarTodos() {
        global $conn;
        $sql = "SELECT * FROM staff_rol ORDER BY fecha_asignacion DESC";
        $result = $conn->query($sql);
        $rolesList = [];
        
        while ($row = $result->fetch_assoc()) {
            $rolesList[] = new StaffRol($row['id_staff'], $row['id_tipo_rol'], $row['fecha_asignacion']);
        }
        return $rolesList;
    }
}

?>
