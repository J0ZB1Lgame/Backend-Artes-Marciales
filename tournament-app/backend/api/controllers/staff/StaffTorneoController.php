<?php

require_once __DIR__ . '/../../../../backend/config/conexion.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/StaffTorneoDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/ZonaDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/RolDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/LogDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/entities/staff/StaffTorneo.php';
require_once __DIR__ . '/../../../../backend/models/entities/staff/Log.php';

class StaffTorneoController {
    private $staffTorneoDAO;
    private $zonaDAO;
    private $rolDAO;
    private $logDAO;
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->staffTorneoDAO = new StaffTorneoDAOImpl();
        $this->zonaDAO = new ZonaDAOImpl();
        $this->rolDAO = new RolDAOImpl();
        $this->logDAO = new LogDAOImpl();
    }

    public function jsonResponse($data, $message = "OK", $status = 200) {
        http_response_code($status);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public function jsonError($message, $status = 400) {
        http_response_code($status);
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }

    public function registrarStaff($ejecutor, $datosStaff) {
        $staffTorneo = new StaffTorneo(null, $datosStaff['nombre'] ?? '');
        $resultado = $this->staffTorneoDAO->crear($staffTorneo);
        $this->reportarLog($ejecutor, "Registrar staff en torneo: " . $staffTorneo->getNombre());
        return $resultado;
    }

    public function listarStaffPorTorneo($ejecutor, $torneo) {
        $staffList = $this->staffTorneoDAO->listarTodos();
        $response = [];

        foreach ($staffList as $staffTorneo) {
            if ($staffTorneo->getNombre() === $torneo) {
                $response[] = [
                    'id_staff_torneo' => $staffTorneo->getIdStaffTorneo(),
                    'nombre' => $staffTorneo->getNombre()
                ];
            }
        }

        return $response;
    }

    public function asignarStaffATorneo($ejecutor, $staff, $torneo) {
        $staffTorneos = $this->staffTorneoDAO->listarTodos();
        foreach ($staffTorneos as $registro) {
            if ($registro->getNombre() === $torneo) {
                $this->conn->query("UPDATE staff_torneo SET id_staff = {$staff} WHERE id_staff_torneo = {$registro->getIdStaffTorneo()}");
                $this->reportarLog($ejecutor, "Asignar staff {$staff} al torneo {$torneo}");
                return true;
            }
        }
        $sql = "INSERT INTO staff_torneo (id_staff, nombre) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $staff, $torneo);
        $success = $stmt->execute();
        $this->reportarLog($ejecutor, "Asignar staff {$staff} al torneo {$torneo}");
        return $success;
    }

    public function asignarZona($ejecutor, $staff, string $zona) {
        $zonas = $this->zonaDAO->listarTodos();
        foreach ($zonas as $registro) {
            if ($registro->getNombre() === $zona) {
                $this->reportarLog($ejecutor, "Asignar zona {$zona} al staff {$staff}");
                return true;
            }
        }
        return false;
    }

    public function asignarRol($ejecutor, $staff, $rol) {
        $this->reportarLog($ejecutor, "Asignar rol {$rol} al staff {$staff}");
        return true;
    }

    public function modificarRolEnTorneo($ejecutor, $staff, $nuevoRol) {
        $this->reportarLog($ejecutor, "Modificar rol en torneo para staff {$staff} a {$nuevoRol}");
        return true;
    }

    public function modificarZonaEnTorneo($ejecutor, $staff, string $nuevaZona) {
        $this->reportarLog($ejecutor, "Modificar zona en torneo para staff {$staff} a {$nuevaZona}");
        return true;
    }

    public function eliminarStaffDeTorneo($ejecutor, $staff, $torneo) {
        $sql = "DELETE FROM staff_torneo WHERE id_staff = ? AND nombre = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $staff, $torneo);
        $success = $stmt->execute();
        $this->reportarLog($ejecutor, "Eliminar staff {$staff} del torneo {$torneo}");
        return $success;
    }

    public function reportarLog(int $idStaff, string $descripcion): void {
        $evento = new Log(null, $descripcion, date('Y-m-d H:i:s'), $idStaff);
        $this->logDAO->crearEvento($evento);
    }
}

?>