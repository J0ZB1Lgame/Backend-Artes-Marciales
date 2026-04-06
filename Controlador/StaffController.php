<?php
require_once __DIR__ . '/../Modelo/DAO/StaffDAOImpl.php';
require_once __DIR__ . '/../Modelo/Entidades/Staff.php';

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
        return $this->staffDAO->listarTodos();
    }
}
?>