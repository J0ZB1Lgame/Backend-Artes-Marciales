<?php
require_once __DIR__ . '/IAsignacionTurnoDAO.php';
require_once __DIR__ . '/../../Controlador/conexion.php';
require_once __DIR__ . '/../Entidades/AsignacionTurno.php';

class AsignacionTurnoDAOImpl implements IAsignacionTurnoDAO {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function crear($asignacion) {
        $stmt = $this->conn->prepare("INSERT INTO asignacion_turno (id_staff, id_turno, fecha, estado) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $asignacion->getIdStaff(), $asignacion->getIdTurno(), $asignacion->getFecha(), $asignacion->getEstado());
        if (!$stmt->execute()) {
            throw new Exception('Error al crear asignación de turno: ' . $stmt->error);
        }
        $asignacion->setIdAsignacion($this->conn->insert_id);
        $stmt->close();
        return $asignacion;
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM asignacion_turno WHERE id_asignacion = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $asignacion = null;
        if ($row = $result->fetch_assoc()) {
            $asignacion = new AsignacionTurno($row['id_asignacion'], $row['id_staff'], $row['id_turno'], $row['fecha'], $row['estado']);
        }
        $stmt->close();
        return $asignacion;
    }

    public function actualizar($asignacion) {
        $stmt = $this->conn->prepare("UPDATE asignacion_turno SET id_staff = ?, id_turno = ?, fecha = ?, estado = ? WHERE id_asignacion = ?");
        $stmt->bind_param("iissi", $asignacion->getIdStaff(), $asignacion->getIdTurno(), $asignacion->getFecha(), $asignacion->getEstado(), $asignacion->getIdAsignacion());
        if (!$stmt->execute()) {
            throw new Exception('Error al actualizar asignación de turno: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function eliminarPorId($id) {
        $stmt = $this->conn->prepare("DELETE FROM asignacion_turno WHERE id_asignacion = ?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception('Error al eliminar asignación de turno: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function listarPorStaff($id_staff) {
        $stmt = $this->conn->prepare("SELECT * FROM asignacion_turno WHERE id_staff = ? ORDER BY fecha DESC");
        $stmt->bind_param("i", $id_staff);
        $stmt->execute();
        $result = $stmt->get_result();
        $asignaciones = [];
        while ($row = $result->fetch_assoc()) {
            $asignaciones[] = new AsignacionTurno($row['id_asignacion'], $row['id_staff'], $row['id_turno'], $row['fecha'], $row['estado']);
        }
        $stmt->close();
        return $asignaciones;
    }

    public function listarTodas() {
        $stmt = $this->conn->prepare("SELECT * FROM asignacion_turno ORDER BY fecha DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $asignaciones = [];
        while ($row = $result->fetch_assoc()) {
            $asignaciones[] = new AsignacionTurno($row['id_asignacion'], $row['id_staff'], $row['id_turno'], $row['fecha'], $row['estado']);
        }
        $stmt->close();
        return $asignaciones;
    }

    public function eliminarPorStaff($id_staff) {
        $stmt = $this->conn->prepare("DELETE FROM asignacion_turno WHERE id_staff = ?");
        $stmt->bind_param("i", $id_staff);
        if (!$stmt->execute()) {
            throw new Exception('Error al eliminar turnos del staff: ' . $stmt->error);
        }
        $stmt->close();
    }
}
?>