<?php
require_once 'IStaffDAO.php';
require_once '../../Controlador/conexion.php';

class StaffDAOImpl implements IStaffDAO {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function crear($staff) {
        $stmt = $this->conn->prepare("INSERT INTO staff (id_usuario, nombre, apellido, tipo_documento, numero_documento, telefono, email, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $staff->getIdUsuario(), $staff->getNombre(), $staff->getApellido(), $staff->getTipoDocumento(), $staff->getNumeroDocumento(), $staff->getTelefono(), $staff->getEmail(), $staff->getEstado());
        $stmt->execute();
        $staff->setIdStaff($this->conn->insert_id);
        $stmt->close();
        return $staff;
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM staff WHERE id_staff = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $staff = new Staff($row['id_staff'], $row['id_usuario'], $row['nombre'], $row['apellido'], $row['tipo_documento'], $row['numero_documento'], $row['telefono'], $row['email'], $row['estado']);
        } else {
            $staff = null;
        }
        $stmt->close();
        return $staff;
    }

    public function actualizar($staff) {
        $stmt = $this->conn->prepare("UPDATE staff SET id_usuario = ?, nombre = ?, apellido = ?, tipo_documento = ?, numero_documento = ?, telefono = ?, email = ?, estado = ? WHERE id_staff = ?");
        $stmt->bind_param("isssssssi", $staff->getIdUsuario(), $staff->getNombre(), $staff->getApellido(), $staff->getTipoDocumento(), $staff->getNumeroDocumento(), $staff->getTelefono(), $staff->getEmail(), $staff->getEstado(), $staff->getIdStaff());
        $stmt->execute();
        $stmt->close();
    }

    public function eliminarPorId($id) {
        $stmt = $this->conn->prepare("DELETE FROM staff WHERE id_staff = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}
?>