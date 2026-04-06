<?php
require_once '../Modelo/DAO/StaffDAOImpl.php';
require_once '../Modelo/Entidades/Staff.php';

class StaffController {
    private $staffDAO;

    public function __construct() {
        $this->staffDAO = new StaffDAOImpl();
    }

    public function registrarStaff($id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email) {
        $staff = new Staff(null, $id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email);
        return $this->staffDAO->crear($staff);
    }

    public function obtenerStaff($id) {
        return $this->staffDAO->obtenerPorId($id);
    }

    public function actualizarStaff($id, $id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email, $estado) {
        $staff = new Staff($id, $id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email, $estado);
        $this->staffDAO->actualizar($staff);
    }

    public function eliminarStaff($id) {
        $this->staffDAO->eliminarPorId($id);
    }

    public function listarStaff() {
        // Este método no está en DAO, pero podemos agregarlo si es necesario
        // Por simplicidad, asumir que hay un método en DAO para listar todos
        // Aquí un ejemplo simple
        $stmt = $this->staffDAO->conn->prepare("SELECT * FROM staff");
        $stmt->execute();
        $result = $stmt->get_result();
        $staffs = [];
        while ($row = $result->fetch_assoc()) {
            $staffs[] = new Staff($row['id_staff'], $row['id_usuario'], $row['nombre'], $row['apellido'], $row['tipo_documento'], $row['numero_documento'], $row['telefono'], $row['email'], $row['estado']);
        }
        $stmt->close();
        return $staffs;
    }
}
?>