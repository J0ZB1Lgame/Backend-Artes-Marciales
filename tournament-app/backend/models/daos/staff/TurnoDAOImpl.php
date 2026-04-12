<?php

require_once __DIR__ . '/ITurnoDAO.php';
require_once __DIR__ . '/../../entities/staff/Turno.php';
require_once __DIR__ . '/../../../config/conexion.php';

class TurnoDAOImpl implements ITurnoDAO {

    public function crear($turno) {
        global $conn;
        $sql = "INSERT INTO turno (nombre, hora_inicio, hora_fin) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $turno->getNombre(), $turno->getHora_inicio(), $turno->getHora_fin());
        
        if ($stmt->execute()) {
            $turno->setId_turno($conn->insert_id);
            return $turno;
        }
        return false;
    }

    public function obtenerPorId($id) {
        global $conn;
        $sql = "SELECT * FROM turno WHERE id_turno = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return new Turno($row['id_turno'], $row['nombre'], $row['hora_inicio'], $row['hora_fin']);
        }
        return null;
    }

    public function actualizar($turno) {
        global $conn;
        $sql = "UPDATE turno SET nombre=?, hora_inicio=?, hora_fin=? WHERE id_turno=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $turno->getNombre(), $turno->getHora_inicio(), $turno->getHora_fin(), $turno->getId_turno());
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        global $conn;
        $sql = "DELETE FROM turno WHERE id_turno = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function listarTodos() {
        global $conn;
        $sql = "SELECT * FROM turno ORDER BY nombre";
        $result = $conn->query($sql);
        $turnoList = [];
        
        while ($row = $result->fetch_assoc()) {
            $turnoList[] = new Turno($row['id_turno'], $row['nombre'], $row['hora_inicio'], $row['hora_fin']);
        }
        return $turnoList;
    }
}

?>
