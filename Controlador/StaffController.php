<?php
// ================== IMPORTS ==================

require_once __DIR__ . '/../Modelo/DAO/StaffDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/TurnoDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/TipoRolStaffDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/AsignacionTurnoDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/StaffRolDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/UsuarioDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/RolDAOImpl.php';
require_once __DIR__ . '/../Modelo/Entidades/Staff.php';
require_once __DIR__ . '/../Modelo/Entidades/AsignacionTurno.php';
require_once __DIR__ . '/../Controlador/conexion.php';

// ================== CLASE ==================

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

    // ================== RESPUESTAS JSON (para API) ==================

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

    // ================== VALIDACIONES ==================

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

    // ================== MÉTODOS DE LISTA ==================

    public function listarStaff() {
        try {
            $staffs = $this->staffDAO->listarTodos();
            $data = [];

            foreach ($staffs as $staff) {
                $data[] = [
                    "id_staff" => $staff->getIdStaff(),
                    "id_usuario" => $staff->getIdUsuario(),
                    "nombre" => $staff->getNombre(),
                    "apellido" => $staff->getApellido(),
                    "tipo_documento" => $staff->getTipoDocumento(),
                    "numero_documento" => $staff->getNumeroDocumento(),
                    "telefono" => $staff->getTelefono(),
                    "email" => $staff->getEmail(),
                    "estado" => $staff->getEstado()
                ];
            }
            return $data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function listarUsuarios() {
        try {
            $query = "SELECT id_usuario, username, email, estado FROM usuario";
            $result = $this->conn->query($query);
            $data = [];
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            return $data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function listarTurnos() {
        try {
            $turnos = $this->turnoDAO->listarTodos();
            $data = [];
            
            foreach ($turnos as $turno) {
                $data[] = [
                    "id_turno" => $turno->getIdTurno(),
                    "nombre" => $turno->getNombre(),
                    "hora_inicio" => $turno->getHoraInicio(),
                    "hora_fin" => $turno->getHoraFin()
                ];
            }
            return $data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function listarTiposRol() {
        try {
            $roles = $this->tipoRolStaffDAO->listarTodos();
            $data = [];
            
            foreach ($roles as $rol) {
                $data[] = [
                    "id_tipo_rol" => $rol->getIdTipoRol(),
                    "nombre" => $rol->getNombre(),
                    "descripcion" => $rol->getDescripcion()
                ];
            }
            return $data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function listarAsignacionesTurno() {
        try {
            $asignaciones = $this->asignacionTurnoDAO->listarTodas();
            $data = [];
            
            foreach ($asignaciones as $asignacion) {
                $data[] = [
                    "id_asignacion" => $asignacion->getIdAsignacion(),
                    "id_staff" => $asignacion->getIdStaff(),
                    "id_turno" => $asignacion->getIdTurno(),
                    "fecha" => $asignacion->getFecha(),
                    "estado" => $asignacion->getEstado()
                ];
            }
            return $data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function listarRolesAsignados() {
        try {
            $staffRoles = $this->staffRolDAO->listarTodos();
            $data = [];
            
            foreach ($staffRoles as $staffRole) {
                $data[] = [
                    "id_staff" => $staffRole->getIdStaff(),
                    "id_tipo_rol" => $staffRole->getIdTipoRol()
                ];
            }
            return $data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    // ================== MÉTODOS CRUD ==================

    public function registrarStaff($id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email) {
        try {
            $this->validarStaffData($id_usuario, $nombre, $apellido, $email);

            $staff = new Staff(
                null,
                $id_usuario,
                $nombre,
                $apellido,
                $tipo_documento,
                $numero_documento,
                $telefono,
                $email
            );

            $id = $this->staffDAO->crear($staff);
            return $id;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actualizarStaff($id, $id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email, $estado) {
        try {
            $this->validarId($id, 'ID de staff');
            $this->validarStaffData($id_usuario, $nombre, $apellido, $email);

            $staff = new Staff(
                $id,
                $id_usuario,
                $nombre,
                $apellido,
                $tipo_documento,
                $numero_documento,
                $telefono,
                $email
            );

            $staff->setEstado($estado);
            $this->staffDAO->actualizar($staff);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function eliminarStaff($id) {
        try {
            $this->validarId($id, 'ID de staff');
            $this->staffDAO->eliminarPorId($id);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function obtenerStaff($id) {
        try {
            $this->validarId($id, 'ID de staff');
            $staff = $this->staffDAO->obtenerPorId($id);
            return $staff;
        } catch (Exception $e) {
            throw $e;
        }
    }

    // ================== MÉTODOS DE ASIGNACIÓN ==================

    public function asignarRol($id_staff, $id_tipo_rol) {
        try {
            $this->validarId($id_staff, 'ID de staff');
            $this->validarId($id_tipo_rol, 'ID de tipo rol');

            $query = "INSERT INTO staff_rol (id_staff, id_tipo_rol) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $id_staff, $id_tipo_rol);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al asignar rol: ' . $stmt->error);
            }
            
            $stmt->close();
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function asignarTurno($id_staff, $id_turno, $fecha) {
        try {
            $this->validarId($id_staff, 'ID de staff');
            $this->validarId($id_turno, 'ID de turno');
            $this->validarFecha($fecha);

            $asignacion = new AsignacionTurno(null, $id_staff, $id_turno, $fecha, 'activo');
            $this->asignacionTurnoDAO->crear($asignacion);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function revocarRol($id_staff, $id_tipo_rol) {
        try {
            $this->validarId($id_staff, 'ID de staff');
            $this->validarId($id_tipo_rol, 'ID de tipo rol');

            $query = "DELETE FROM staff_rol WHERE id_staff = ? AND id_tipo_rol = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $id_staff, $id_tipo_rol);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al revocar rol: ' . $stmt->error);
            }
            
            $stmt->close();
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
