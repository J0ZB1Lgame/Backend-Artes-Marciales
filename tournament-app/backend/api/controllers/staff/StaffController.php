<?php

require_once __DIR__ . '/../../models/daos/staff/StaffDAOImpl.php';
require_once __DIR__ . '/../../models/daos/staff/TurnoDAOImpl.php';
require_once __DIR__ . '/../../models/daos/staff/TipoRolStaffDAOImpl.php';
require_once __DIR__ . '/../../models/daos/staff/AsignacionTurnoDAOImpl.php';
require_once __DIR__ . '/../../models/daos/staff/StaffRolDAOImpl.php';
require_once __DIR__ . '/../../models/daos/staff/UsuarioDAOImpl.php';
require_once __DIR__ . '/../../models/daos/staff/RolDAOImpl.php';
require_once __DIR__ . '/../../models/entities/staff/Staff.php';
require_once __DIR__ . '/../../models/entities/staff/AsignacionTurno.php';
require_once __DIR__ . '/../../config/conexion.php';

class StaffController {
    private $staffDAO;
    private $turnoDAO;
    private $tipoRolStaffDAO;
    private $asignacionTurnoDAO;
    private $staffRolDAO;
    private $usuarioDAO;
    private $rolDAO;
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->staffDAO = new StaffDAOImpl();
        $this->turnoDAO = new TurnoDAOImpl();
        $this->tipoRolStaffDAO = new TipoRolStaffDAOImpl();
        $this->asignacionTurnoDAO = new AsignacionTurnoDAOImpl();
        $this->staffRolDAO = new StaffRolDAOImpl();
        $this->usuarioDAO = new UsuarioDAOImpl();
        $this->rolDAO = new RolDAOImpl();
    }

    // ================== RESPUESTAS JSON ==================

    public function jsonResponse($data, $message = "OK", $status = 200) {
        http_response_code($status);
        echo json_encode([
            "status" => "success",
            "message" => $message,
            "data" => $data
        ]);
        exit;
    }

    public function jsonError($message, $status = 400) {
        http_response_code($status);
        echo json_encode([
            "status" => "error",
            "message" => $message
        ]);
        exit;
    }

    // ================== LISTAR STAFF ==================

    public function listarStaff() {
        $staffList = $this->staffDAO->listarTodos();
        $response = [];
        
        foreach ($staffList as $staff) {
            $response[] = [
                'id_staff' => $staff->getId_staff(),
                'id_usuario' => $staff->getId_usuario(),
                'nombre' => $staff->getNombre(),
                'apellido' => $staff->getApellido(),
                'tipo_documento' => $staff->getTipo_documento(),
                'numero_documento' => $staff->getNumero_documento(),
                'telefono' => $staff->getTelefono(),
                'email' => $staff->getEmail(),
                'estado' => $staff->getEstado(),
                'cargo' => $staff->getCargo(),
                'turno' => $staff->getTurno()
            ];
        }
        return $response;
    }

    // ================== OBTENER STAFF POR ID ==================

    public function obtenerStaff($id) {
        $staff = $this->staffDAO->obtenerPorId($id);
        
        if (!$staff) {
            return null;
        }
        
        return [
            'id_staff' => $staff->getId_staff(),
            'id_usuario' => $staff->getId_usuario(),
            'nombre' => $staff->getNombre(),
            'apellido' => $staff->getApellido(),
            'tipo_documento' => $staff->getTipo_documento(),
            'numero_documento' => $staff->getNumero_documento(),
            'telefono' => $staff->getTelefono(),
            'email' => $staff->getEmail(),
            'estado' => $staff->getEstado(),
            'cargo' => $staff->getCargo(),
            'turno' => $staff->getTurno()
        ];
    }

    // ================== REGISTRAR STAFF ==================

    public function registrarStaff($datos) {
        // Validar datos requeridos
        if (!isset($datos['id_usuario']) || !isset($datos['nombre']) || !isset($datos['apellido']) || 
            !isset($datos['telefono']) || !isset($datos['email'])) {
            return false;
        }

        $staff = new Staff(
            null,
            $datos['id_usuario'],
            $datos['nombre'],
            $datos['apellido'],
            $datos['tipo_documento'] ?? null,
            $datos['numero_documento'] ?? null,
            $datos['telefono'],
            $datos['email'],
            $datos['estado'] ?? 'activo',
            $datos['cargo'] ?? null,
            $datos['turno'] ?? null
        );

        return $this->staffDAO->crear($staff);
    }

    // ================== ACTUALIZAR STAFF ==================

    public function actualizarStaff($id, $datos) {
        $staff = $this->staffDAO->obtenerPorId($id);
        
        if (!$staff) {
            return false;
        }

        if (isset($datos['id_usuario'])) $staff->setId_usuario($datos['id_usuario']);
        if (isset($datos['nombre'])) $staff->setNombre($datos['nombre']);
        if (isset($datos['apellido'])) $staff->setApellido($datos['apellido']);
        if (isset($datos['tipo_documento'])) $staff->setTipo_documento($datos['tipo_documento']);
        if (isset($datos['numero_documento'])) $staff->setNumero_documento($datos['numero_documento']);
        if (isset($datos['telefono'])) $staff->setTelefono($datos['telefono']);
        if (isset($datos['email'])) $staff->setEmail($datos['email']);
        if (isset($datos['estado'])) $staff->setEstado($datos['estado']);
        if (isset($datos['cargo'])) $staff->setCargo($datos['cargo']);
        if (isset($datos['turno'])) $staff->setTurno($datos['turno']);

        return $this->staffDAO->actualizar($staff);
    }

    // ================== ELIMINAR STAFF ==================

    public function eliminarStaff($id) {
        // Eliminar asignaciones de turno primero
        $this->asignacionTurnoDAO->eliminarPorStaff($id);
        
        // Luego eliminar staff
        return $this->staffDAO->eliminarPorId($id);
    }

    // ================== LISTAR USUARIOS ==================

    public function listarUsuarios() {
        // Implementación simple
        $sql = "SELECT id_usuario, username, estado FROM usuario";
        $result = $this->conn->query($sql);
        $usuarios = [];
        
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }

    // ================== LISTAR TURNOS ==================

    public function listarTurnos() {
        $turnos = $this->turnoDAO->listarTodos();
        $response = [];
        
        foreach ($turnos as $turno) {
            $response[] = [
                'id_turno' => $turno->getId_turno(),
                'nombre' => $turno->getNombre(),
                'hora_inicio' => $turno->getHora_inicio(),
                'hora_fin' => $turno->getHora_fin()
            ];
        }
        return $response;
    }

    // ================== LISTAR ASIGNACIONES DE TURNO ==================

    public function listarAsignacionesTurno() {
        $asignaciones = $this->asignacionTurnoDAO->listarTodas();
        $response = [];
        
        foreach ($asignaciones as $asignacion) {
            $response[] = [
                'id_asignacion' => $asignacion->getId_asignacion(),
                'id_staff' => $asignacion->getId_staff(),
                'id_turno' => $asignacion->getId_turno(),
                'fecha' => $asignacion->getFecha(),
                'estado' => $asignacion->getEstado()
            ];
        }
        return $response;
    }

    // ================== LISTAR TIPOS DE ROL ==================

    public function listarTiposRol() {
        $tiposRol = $this->tipoRolStaffDAO->listarTodos();
        $response = [];
        
        foreach ($tiposRol as $tipo) {
            $response[] = [
                'id_tipo_rol' => $tipo->getId_tipo_rol(),
                'nombre' => $tipo->getNombre(),
                'descripcion' => $tipo->getDescripcion()
            ];
        }
        return $response;
    }

    // ================== LISTAR ROLES ASIGNADOS ==================

    public function listarRolesAsignados($id_staff = null) {
        if ($id_staff) {
            $roles = $this->staffRolDAO->listarRolesPorStaff($id_staff);
        } else {
            $roles = $this->staffRolDAO->listarTodos();
        }
        
        $response = [];
        foreach ($roles as $rol) {
            $response[] = [
                'id_staff' => $rol->getId_staff(),
                'id_tipo_rol' => $rol->getId_tipo_rol(),
                'fecha_asignacion' => $rol->getFecha_asignacion()
            ];
        }
        return $response;
    }

    // ================== ASIGNAR ROL ==================

    public function asignarRol($id_staff, $id_tipo_rol) {
        return $this->staffRolDAO->asignar($id_staff, $id_tipo_rol);
    }

    // ================== ASIGNAR TURNO ==================

    public function asignarTurno($id_staff, $id_turno, $fecha) {
        $asignacion = new AsignacionTurno(null, $id_staff, $id_turno, $fecha, 'activo');
        return $this->asignacionTurnoDAO->crear($asignacion);
    }

    // ================== REVOCAR ROL ==================

    public function revocarRol($id_staff, $id_tipo_rol) {
        return $this->staffRolDAO->revocar($id_staff, $id_tipo_rol);
    }
}

?>
