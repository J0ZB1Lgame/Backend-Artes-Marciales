<?php

require_once __DIR__ . '/IAsignacionTurnoDAO.php';
require_once __DIR__ . '/../../entities/staff/AsignacionTurno.php';
require_once __DIR__ . '/../../../config/conexion.php';

class AsignacionTurnoDAOImpl implements IAsignacionTurnoDAO {

    public function crear($asignacion) {
        global $conn;
        $sql = "INSERT INTO asignacion_turno (id_staff, id_turno, fecha, estado) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $asignacion->getId_staff(), $asignacion->getId_turno(), $asignacion->getFecha(), $asignacion->getEstado());
        
        if ($stmt->execute()) {
            $asignacion->setId_asignacion($conn->insert_id);
            return $asignacion;
        }
        return false;
    }

    public function obtenerPorId($id) {
        global $conn;
        $sql = "SELECT * FROM asignacion_turno WHERE id_asignacion = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return new AsignacionTurno($row['id_asignacion'], $row['id_staff'], $row['id_turno'], $row['fecha'], $row['estado']);
        }
        return null;
    }

    public function actualizar($asignacion) {
        global $conn;
        $sql = "UPDATE asignacion_turno SET id_staff=?, id_turno=?, fecha=?, estado=? WHERE id_asignacion=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissi", $asignacion->getId_staff(), $asignacion->getId_turno(), $asignacion->getFecha(), $asignacion->getEstado(), $asignacion->getId_asignacion());
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        global $conn;
        $sql = "DELETE FROM asignacion_turno WHERE id_asignacion = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function listarPorStaff($id_staff) {
        global $conn;
        $sql = "SELECT * FROM asignacion_turno WHERE id_staff = ? ORDER BY fecha DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_staff);
        $stmt->execute();
        $result = $stmt->get_result();
        $asignacionesList = [];
        
        while ($row = $result->fetch_assoc()) {
            $asignacionesList[] = new AsignacionTurno($row['id_asignacion'], $row['id_staff'], $row['id_turno'], $row['fecha'], $row['estado']);
        }
        return $asignacionesList;
    }

    public function listarTodas() {
        global $conn;
        $sql = "SELECT * FROM asignacion_turno ORDER BY fecha DESC";
        $result = $conn->query($sql);
        $asignacionesList = [];
        
        while ($row = $result->fetch_assoc()) {
            $asignacionesList[] = new AsignacionTurno($row['id_asignacion'], $row['id_staff'], $row['id_turno'], $row['fecha'], $row['estado']);
        }
        return $asignacionesList;
    }

    public function eliminarPorStaff($id_staff) {
        global $conn;
        $sql = "DELETE FROM asignacion_turno WHERE id_staff = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_staff);
        return $stmt->execute();
    }
}

?>
