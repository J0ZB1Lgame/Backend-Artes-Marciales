<?php

require_once __DIR__ . '/../../base/BaseDAO.php';
require_once __DIR__ . '/../interfaces/ILogDAO.php';
require_once __DIR__ . '/../../../entities/login/Log.php';

class LogDAOImpl extends BaseDAO implements ILogDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crearEvento($evento): void {
        $sql = "INSERT INTO log (accion, fecha, id_usuario) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        
        // Guardar valores en variables locales para bind_param
        $accion = $evento->getAccion();
        $fecha = $evento->getFecha();
        $idUsuario = $evento->getIdUsuario();
        
        $stmt->bind_param("ssi", $accion, $fecha, $idUsuario);
        $stmt->execute();
        $evento->setIdLog($this->connection->insert_id);
    }

    public function consultarHistorial(): array {
        $sql = "SELECT * FROM log ORDER BY fecha DESC";
        $result = $this->connection->query($sql);
        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $logs[] = new Log($row['id_log'], $row['accion'], $row['fecha'], $row['id_usuario']);
        }
        return $logs;
    }

    public function crear($entidad) {
        $this->crearEvento($entidad);
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

    public function actualizar($entidad) {
        $sql = "UPDATE log SET accion = ?, fecha = ?, id_usuario = ? WHERE id_log = ?";
        $stmt = $this->connection->prepare($sql);
        
        // Guardar valores en variables locales para bind_param
        $accion = $entidad->getAccion();
        $fecha = $entidad->getFecha();
        $idUsuario = $entidad->getIdUsuario();
        $idLog = $entidad->getIdLog();
        
        $stmt->bind_param("ssii", $accion, $fecha, $idUsuario, $idLog);
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