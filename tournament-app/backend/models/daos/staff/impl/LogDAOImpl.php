<?php

require_once __DIR__ . '/../interfaces/ILogDAO.php';
require_once __DIR__ . '/../../entities/staff/Log.php';
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../base/BaseDAO.php';

class LogDAOImpl extends BaseDAO implements ILogDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crear($evento) {
        return $this->crearEvento($evento);
    }

    public function crearEvento($evento): void {
        if ($evento->getFecha()) {
            $sql = "INSERT INTO log (accion, fecha, id_usuario) VALUES (?, ?, ?)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param("ssi", $evento->getAccion(), $evento->getFecha(), $evento->getIdUsuario());
        } else {
            $sql = "INSERT INTO log (accion, id_usuario) VALUES (?, ?)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param("si", $evento->getAccion(), $evento->getIdUsuario());
        }
        $stmt->execute();
    }

    public function consultarHistorial(): array {
        $sql = "SELECT * FROM log ORDER BY fecha DESC";
        $result = $this->connection->query($sql);
        $lista = [];

        while ($row = $result->fetch_assoc()) {
            $lista[] = new Log(
                $row['id_log'],
                $row['accion'],
                $row['fecha'],
                $row['id_usuario']
            );
        }
        return $lista;
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM log WHERE id_log = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return new Log($row['id_log'], $row['accion'], $row['fecha'], $row['id_usuario']);
        }
        return null;
    }

    public function actualizar($evento) {
        $sql = "UPDATE log SET accion = ?, fecha = ?, id_usuario = ? WHERE id_log = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("ssii", $evento->getAccion(), $evento->getFecha(), $evento->getIdUsuario(), $evento->getIdLog());
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        $sql = "DELETE FROM log WHERE id_log = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

?>