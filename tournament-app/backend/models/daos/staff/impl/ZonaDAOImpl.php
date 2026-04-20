<?php

require_once __DIR__ . '/../interfaces/IZonaDAO.php';
require_once __DIR__ . '/../../../entities/staff/Zona.php';
require_once __DIR__ . '/../../../../config/conexion.php';
require_once __DIR__ . '/../../base/BaseDAO.php';

class ZonaDAOImpl extends BaseDAO implements IZonaDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crear($zona) {
        $sql = "INSERT INTO zona (nombre, descripcion) VALUES (?, ?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("ss", $zona->getNombre(), $zona->getDescripcion());

        if ($stmt->execute()) {
            $zona->setIdZona($this->connection->insert_id);
            return $zona;
        }
        return false;
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM zona WHERE id_zona = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return new Zona($row['id_zona'], $row['nombre'], $row['descripcion']);
        }
        return null;
    }

    public function actualizar($zona) {
        $sql = "UPDATE zona SET nombre = ?, descripcion = ? WHERE id_zona = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("ssi", $zona->getNombre(), $zona->getDescripcion(), $zona->getIdZona());
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        $sql = "DELETE FROM zona WHERE id_zona = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function listarTodos() {
        $sql = "SELECT * FROM zona";
        $result = $this->connection->query($sql);
        $lista = [];

        while ($row = $result->fetch_assoc()) {
            $lista[] = new Zona($row['id_zona'], $row['nombre'], $row['descripcion']);
        }
        return $lista;
    }
}

?>