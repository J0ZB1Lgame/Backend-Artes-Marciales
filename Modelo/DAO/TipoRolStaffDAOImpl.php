<?php
require_once __DIR__ . '/ITipoRolStaffDAO.php';
require_once __DIR__ . '/../../Controlador/conexion.php';

class TipoRolStaffDAOImpl implements ITipoRolStaffDAO {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function crear($rol) {
        $stmt = $this->conn->prepare("INSERT INTO tipo_rol_staff (nombre, descripcion) VALUES (?, ?)");
        $stmt->bind_param("ss", $rol->getNombre(), $rol->getDescripcion());
        if (!$stmt->execute()) {
            throw new Exception('Error al crear tipo de rol staff: ' . $stmt->error);
        }
        $rol->setIdTipoRol($this->conn->insert_id);
        $stmt->close();
        return $rol;
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM tipo_rol_staff WHERE id_tipo_rol = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rol = null;
        if ($row = $result->fetch_assoc()) {
            $rol = new TipoRolStaff($row['id_tipo_rol'], $row['nombre'], $row['descripcion']);
        }
        $stmt->close();
        return $rol;
    }

    public function actualizar($rol) {
        $stmt = $this->conn->prepare("UPDATE tipo_rol_staff SET nombre = ?, descripcion = ? WHERE id_tipo_rol = ?");
        $stmt->bind_param("ssi", $rol->getNombre(), $rol->getDescripcion(), $rol->getIdTipoRol());
        if (!$stmt->execute()) {
            throw new Exception('Error al actualizar tipo de rol staff: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function eliminarPorId($id) {
        $stmt = $this->conn->prepare("DELETE FROM tipo_rol_staff WHERE id_tipo_rol = ?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception('Error al eliminar tipo de rol staff: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function listarTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM tipo_rol_staff ORDER BY nombre");
        $stmt->execute();
        $result = $stmt->get_result();
        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = new TipoRolStaff($row['id_tipo_rol'], $row['nombre'], $row['descripcion']);
        }
        $stmt->close();
        return $roles;
    }
}
?>