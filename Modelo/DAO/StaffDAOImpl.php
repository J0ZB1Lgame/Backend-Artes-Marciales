<?php
require_once __DIR__ . '/IStaffDAO.php';
require_once __DIR__ . '/../../Controlador/conexion.php';

class StaffDAOImpl implements IStaffDAO {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function crear($staff) {
        $stmt = $this->conn->prepare("INSERT INTO staff (id_usuario, nombre, apellido, tipo_documento, numero_documento, telefono, email, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $id_usuario = $staff->getIdUsuario();
        $nombre = $staff->getNombre();
        $apellido = $staff->getApellido();
        $tipo_documento = $staff->getTipoDocumento();
        $numero_documento = $staff->getNumeroDocumento();
        $telefono = $staff->getTelefono();
        $email = $staff->getEmail();
        $estado = $staff->getEstado();
        $stmt->bind_param("isssssss", $id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email, $estado);
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
        $id_usuario = $staff->getIdUsuario();
        $nombre = $staff->getNombre();
        $apellido = $staff->getApellido();
        $tipo_documento = $staff->getTipoDocumento();
        $numero_documento = $staff->getNumeroDocumento();
        $telefono = $staff->getTelefono();
        $email = $staff->getEmail();
        $estado = $staff->getEstado();
        $id_staff = $staff->getIdStaff();
        $stmt->bind_param("isssssssi", $id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email, $estado, $id_staff);
        $stmt->execute();
        $stmt->close();
    }

    public function eliminarPorId($id) {
        $stmt = $this->conn->prepare("DELETE FROM staff WHERE id_staff = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    public function listarTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM staff");
        $stmt->execute();
        $result = $stmt->get_result();
        $staffs = [];
        while ($row = $result->fetch_assoc()) {
            $staff = new Staff();
            $staff->setIdStaff($row['id_staff']);
            $staff->setIdUsuario($row['id_usuario']);
            $staff->setNombre($row['nombre']);
            $staff->setApellido($row['apellido']);
            $staff->setTipoDocumento($row['tipo_documento']);
            $staff->setNumeroDocumento($row['numero_documento']);
            $staff->setTelefono($row['telefono']);
            $staff->setEmail($row['email']);
            $staff->setEstado($row['estado']);
            $staffs[] = $staff;
        }
        $stmt->close();
        return $staffs;
    }
}
?>