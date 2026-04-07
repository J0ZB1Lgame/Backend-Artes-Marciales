<?php
require_once __DIR__ . '/IStaffRolDAO.php';
require_once __DIR__ . '/../../Controlador/conexion.php';
require_once __DIR__ . '/../Entidades/StaffRol.php';

class StaffRolDAOImpl implements IStaffRolDAO {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function asignar($id_staff, $id_tipo_rol) {
        $stmt = $this->conn->prepare("INSERT INTO staff_rol (id_staff, id_tipo_rol) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_staff, $id_tipo_rol);
        if (!$stmt->execute()) {
            throw new Exception('Error al asignar rol al staff: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function revocar($id_staff, $id_tipo_rol) {
        $stmt = $this->conn->prepare("DELETE FROM staff_rol WHERE id_staff = ? AND id_tipo_rol = ?");
        $stmt->bind_param("ii", $id_staff, $id_tipo_rol);
        if (!$stmt->execute()) {
            throw new Exception('Error al revocar rol del staff: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function listarRolesPorStaff($id_staff) {
        $stmt = $this->conn->prepare("SELECT * FROM staff_rol WHERE id_staff = ? ORDER BY fecha_asignacion DESC");
        $stmt->bind_param("i", $id_staff);
        $stmt->execute();
        $result = $stmt->get_result();
        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = new StaffRol($row['id_staff'], $row['id_tipo_rol'], $row['fecha_asignacion']);
        }
        $stmt->close();
        return $roles;
    }

    public function listarTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM staff_rol ORDER BY fecha_asignacion DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = new StaffRol($row['id_staff'], $row['id_tipo_rol'], $row['fecha_asignacion']);
        }
        $stmt->close();
        return $roles;
    }
}
?>