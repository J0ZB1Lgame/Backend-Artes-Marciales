<?php
require_once __DIR__ . '/../Modelo/DAO/StaffDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/TurnoDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/TipoRolStaffDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/AsignacionTurnoDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/StaffRolDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/RolDAOImpl.php';
require_once __DIR__ . '/../Modelo/Entidades/Staff.php';
require_once __DIR__ . '/../Modelo/Entidades/AsignacionTurno.php';

class StaffController {
    private $staffDAO;
    private $turnoDAO;
    private $tipoRolStaffDAO;
    private $asignacionTurnoDAO;
    private $staffRolDAO;
    private $rolDAO;

    public function __construct() {
        $this->staffDAO = new StaffDAOImpl();
        $this->turnoDAO = new TurnoDAOImpl();
        $this->tipoRolStaffDAO = new TipoRolStaffDAOImpl();
        $this->asignacionTurnoDAO = new AsignacionTurnoDAOImpl();
        $this->staffRolDAO = new StaffRolDAOImpl();
        $this->rolDAO = new RolDAOImpl();
    }

    private function validarStaffData($id_usuario, $nombre, $apellido, $email = null) {
        if (empty($id_usuario) || !is_numeric($id_usuario)) {
            throw new Exception('El usuario seleccionado no es válido.');
        }
        if (empty(trim($nombre))) {
            throw new Exception('El nombre es obligatorio.');
        }
        if (empty(trim($apellido))) {
            throw new Exception('El apellido es obligatorio.');
        }
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('El email no tiene un formato válido.');
        }
    }

    private function validarId($id, $campo) {
        if (empty($id) || !is_numeric($id)) {
            throw new Exception("El campo {$campo} es inválido.");
        }
    }

    private function validarFecha($fecha) {
        if (empty($fecha) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            throw new Exception('La fecha es inválida.');
        }
    }

    public function registrarStaff($id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email) {
        $this->validarStaffData($id_usuario, $nombre, $apellido, $email);
        $staff = new Staff(null, $id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email);
        return $this->staffDAO->crear($staff);
    }

    public function obtenerStaff($id) {
        $this->validarId($id, 'ID de staff');
        return $this->staffDAO->obtenerPorId($id);
    }

    public function actualizarStaff($id, $id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email, $estado) {
        $this->validarId($id, 'ID de staff');
        $this->validarStaffData($id_usuario, $nombre, $apellido, $email);
        $staff = new Staff($id, $id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email, $estado);
        $this->staffDAO->actualizar($staff);
    }

    public function eliminarStaff($id) {
        $this->validarId($id, 'ID de staff');
        $this->staffDAO->eliminarPorId($id);
    }

    public function listarStaff() {
        return $this->staffDAO->listarTodos();
    }

    public function listarUsuarios() {
        global $conn;
        $usuarios = [];
        $stmt = $conn->prepare("SELECT id_usuario, username FROM usuario WHERE estado = 'activo' ORDER BY username");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        $stmt->close();
        return $usuarios;
    }

    public function listarTurnos() {
        return $this->turnoDAO->listarTodos();
    }

    public function listarTiposRol() {
        return $this->tipoRolStaffDAO->listarTodos();
    }

    public function asignarTurno($id_staff, $id_turno, $fecha) {
        $this->validarId($id_staff, 'ID de staff');
        $this->validarId($id_turno, 'ID de turno');
        $this->validarFecha($fecha);
        $asignacion = new AsignacionTurno(null, $id_staff, $id_turno, $fecha, 'activo');
        return $this->asignacionTurnoDAO->crear($asignacion);
    }

    public function modificarTurno($id_asignacion, $id_staff, $id_turno, $fecha, $estado) {
        $this->validarId($id_asignacion, 'ID de asignación');
        $this->validarId($id_staff, 'ID de staff');
        $this->validarId($id_turno, 'ID de turno');
        $this->validarFecha($fecha);
        if (!in_array($estado, ['activo', 'inactivo'])) {
            throw new Exception('El estado de la asignación es inválido.');
        }
        $asignacion = new AsignacionTurno($id_asignacion, $id_staff, $id_turno, $fecha, $estado);
        $this->asignacionTurnoDAO->actualizar($asignacion);
    }

    public function listarTurnosDeStaff($id_staff) {
        $this->validarId($id_staff, 'ID de staff');
        return $this->asignacionTurnoDAO->listarPorStaff($id_staff);
    }

    public function listarAsignacionesTurno() {
        return $this->asignacionTurnoDAO->listarTodas();
    }

    public function asignarRol($id_staff, $id_tipo_rol) {
        $this->validarId($id_staff, 'ID de staff');
        $this->validarId($id_tipo_rol, 'ID de tipo de rol');
        $this->staffRolDAO->asignar($id_staff, $id_tipo_rol);
    }

    public function revocarRol($id_staff, $id_tipo_rol) {
        $this->validarId($id_staff, 'ID de staff');
        $this->validarId($id_tipo_rol, 'ID de tipo de rol');
        $this->staffRolDAO->revocar($id_staff, $id_tipo_rol);

        // Si el staff ya no tiene ningún rol, se eliminan sus turnos activos
        $rolesRestantes = $this->staffRolDAO->listarRolesPorStaff($id_staff);
        if (empty($rolesRestantes)) {
            $this->asignacionTurnoDAO->eliminarPorStaff($id_staff);
        }
    }

    public function listarRolesDeStaff($id_staff) {
        $this->validarId($id_staff, 'ID de staff');
        $asignaciones = $this->staffRolDAO->listarRolesPorStaff($id_staff);
        $roles = [];
        foreach ($asignaciones as $asignacion) {
            $roles[] = $this->tipoRolStaffDAO->obtenerPorId($asignacion->getIdTipoRol());
        }
        return $roles;
    }

    public function listarRolesAsignados() {
        return $this->staffRolDAO->listarTodos();
    }

    public function listarRolesSistema() {
        return $this->rolDAO->listarTodos();
    }

    public function asignarRolSistemaAUsuario($id_usuario, $id_rol) {
        $this->validarId($id_usuario, 'ID de usuario');
        $this->validarId($id_rol, 'ID de rol');
        $this->rolDAO->asignarAUsuario($id_usuario, $id_rol);
    }

    public function revocarRolSistemaDeUsuario($id_usuario, $id_rol) {
        $this->validarId($id_usuario, 'ID de usuario');
        $this->validarId($id_rol, 'ID de rol');
        $this->rolDAO->revocarDeUsuario($id_usuario, $id_rol);
    }

    public function listarRolesDeUsuario($id_usuario) {
        $this->validarId($id_usuario, 'ID de usuario');
        return $this->rolDAO->listarPorUsuario($id_usuario);
    }
}
?>