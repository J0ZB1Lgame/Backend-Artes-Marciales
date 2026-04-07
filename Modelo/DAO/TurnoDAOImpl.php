<?php
require_once __DIR__ . '/ITurnoDAO.php';
require_once __DIR__ . '/../../Controlador/conexion.php';
require_once __DIR__ . '/../Entidades/Turno.php';

class TurnoDAOImpl implements ITurnoDAO {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function crear($turno) {
        $stmt = $this->conn->prepare("INSERT INTO turno (nombre, hora_inicio, hora_fin) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $turno->getNombre(), $turno->getHoraInicio(), $turno->getHoraFin());
        if (!$stmt->execute()) {
            throw new Exception('Error al crear turno: ' . $stmt->error);
        }
        $turno->setIdTurno($this->conn->insert_id);
        $stmt->close();
        return $turno;
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM turno WHERE id_turno = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $turno = null;
        if ($row = $result->fetch_assoc()) {
            $turno = new Turno($row['id_turno'], $row['nombre'], $row['hora_inicio'], $row['hora_fin']);
        }
        $stmt->close();
        return $turno;
    }

    public function actualizar($turno) {
        $stmt = $this->conn->prepare("UPDATE turno SET nombre = ?, hora_inicio = ?, hora_fin = ? WHERE id_turno = ?");
        $stmt->bind_param("sssi", $turno->getNombre(), $turno->getHoraInicio(), $turno->getHoraFin(), $turno->getIdTurno());
        if (!$stmt->execute()) {
            throw new Exception('Error al actualizar turno: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function eliminarPorId($id) {
        $stmt = $this->conn->prepare("DELETE FROM turno WHERE id_turno = ?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception('Error al eliminar turno: ' . $stmt->error);
        }
        $stmt->close();
    }

    public function listarTodos() {
        $stmt = $this->conn->prepare("SELECT * FROM turno ORDER BY nombre");
        $stmt->execute();
        $result = $stmt->get_result();
        $turnos = [];
        while ($row = $result->fetch_assoc()) {
            $turnos[] = new Turno($row['id_turno'], $row['nombre'], $row['hora_inicio'], $row['hora_fin']);
        }
        $stmt->close();
        return $turnos;
    }
}
?>