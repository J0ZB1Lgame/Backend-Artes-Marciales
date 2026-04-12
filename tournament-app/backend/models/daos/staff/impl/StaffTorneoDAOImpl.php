<?php

require_once __DIR__ . '/../interfaces/IStaffTorneoDAO.php';
require_once __DIR__ . '/../../../entities/staff/StaffTorneo.php';
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../base/BaseDAO.php';

class StaffTorneoDAOImpl extends BaseDAO implements IStaffTorneoDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crear($staffTorneo) {
        $sql = "INSERT INTO staff_torneo (nombre) VALUES (?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("s", $staffTorneo->getNombre());

        if ($stmt->execute()) {
            $staffTorneo->setIdStaffTorneo($this->connection->insert_id);
            return $staffTorneo;
        }
        return false;
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM staff_torneo WHERE id_staff_torneo = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return new StaffTorneo($row['id_staff_torneo'], $row['nombre']);
        }
        return null;
    }

    public function actualizar($staffTorneo) {
        $sql = "UPDATE staff_torneo SET nombre = ? WHERE id_staff_torneo = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("si", $staffTorneo->getNombre(), $staffTorneo->getIdStaffTorneo());
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        $sql = "DELETE FROM staff_torneo WHERE id_staff_torneo = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function listarTodos() {
        $sql = "SELECT * FROM staff_torneo";
        $result = $this->connection->query($sql);
        $lista = [];

        while ($row = $result->fetch_assoc()) {
            $lista[] = new StaffTorneo($row['id_staff_torneo'], $row['nombre']);
        }
        return $lista;
    }
}

?>