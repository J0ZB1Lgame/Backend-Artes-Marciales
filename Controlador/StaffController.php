<?php
// ================== HEADERS API ==================

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// ================== IMPORTS ==================

require_once __DIR__ . '/../Modelo/DAO/StaffDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/TurnoDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/TipoRolStaffDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/AsignacionTurnoDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/StaffRolDAOImpl.php';
require_once __DIR__ . '/../Modelo/DAO/RolDAOImpl.php';
require_once __DIR__ . '/../Modelo/Entidades/Staff.php';
require_once __DIR__ . '/../Modelo/Entidades/AsignacionTurno.php';

// ================== CLASE ==================

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

    // ================== RESPUESTAS JSON ==================

    private function response($data, $message = "OK", $status = 200) {
        http_response_code($status);
        echo json_encode([
            "status" => "success",
            "message" => $message,
            "data" => $data
        ]);
        exit;
    }

    private function error($message, $status = 400) {
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

    // ================== MÉTODOS API ==================

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
                    "telefono" => $staff->getTelefono(),
                    "email" => $staff->getEmail(),
                    "estado" => $staff->getEstado()
                ];
            }

            $this->response($data, "Lista de staff obtenida");

        } catch (Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    public function obtenerStaff($id) {
        try {
            $this->validarId($id, 'ID de staff');
            $data = $this->staffDAO->obtenerPorId($id);
            $this->response($data, "Staff obtenido");
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function registrarStaff($datos) {
        try {
            $this->validarStaffData(
                $datos['id_usuario'],
                $datos['nombre'],
                $datos['apellido'],
                $datos['email']
            );

            $staff = new Staff(
                null,
                $datos['id_usuario'],
                $datos['nombre'],
                $datos['apellido'],
                $datos['tipo_documento'],
                $datos['numero_documento'],
                $datos['telefono'],
                $datos['email']
            );

            $id = $this->staffDAO->crear($staff);

            $this->response($id, "Staff registrado correctamente", 201);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function eliminarStaff($id) {
        try {
            $this->validarId($id, 'ID de staff');
            $this->staffDAO->eliminarPorId($id);
            $this->response(null, "Staff eliminado");
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function listarAsignacionesTurno() {
        try {
            $data = $this->asignacionTurnoDAO->listarTodas();
            $this->response($data, "Turnos listados");
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}

// ================== ROUTER ==================

$controller = new StaffController();
$metodo = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($metodo === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {

    if ($metodo === 'GET') {

        if ($action === 'listar') {
            $controller->listarStaff();

        } elseif ($action === 'obtener' && isset($_GET['id'])) {
            $controller->obtenerStaff($_GET['id']);

        } elseif ($action === 'turnos') {
            $controller->listarAsignacionesTurno();

        } else {
            throw new Exception("Acción GET no válida");
        }

    } elseif ($metodo === 'POST') {

        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);

        if ($action === 'registrar') {
            $controller->registrarStaff($datos);

        } else {
            throw new Exception("Acción POST no válida");
        }

    } elseif ($metodo === 'DELETE') {

        if ($action === 'eliminar' && isset($_GET['id'])) {
            $controller->eliminarStaff($_GET['id']);

        } else {
            throw new Exception("Acción DELETE no válida");
        }

    } else {
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
