<?php

require_once __DIR__ . '/../../../daos/base/BaseDAO.php';
require_once __DIR__ . '/../interfaces/ISesionDAO.php';
require_once __DIR__ . '/../../../entities/login/Sesion.php';
require_once __DIR__ . '/../../../entities/login/Usuario.php';

class SesionDAOImpl extends BaseDAO implements ISesionDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crearSesion($sesion): void {
        $sql = "INSERT INTO sesion (fecha_inicio, fecha_fin, id_usuario) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        $idUsuario = $sesion->getUsuarioActivo() ? $sesion->getUsuarioActivo()->getIdUsuario() : null;
        $stmt->bind_param("ssi", $sesion->getFechaInicio(), $sesion->getFechaFin(), $idUsuario);
        $stmt->execute();
        $sesion->setIdSesion($this->connection->insert_id);
    }

    public function buscarSesion(int $id) {
        $sql = "SELECT s.*, u.username, u.rol FROM sesion s LEFT JOIN usuario u ON s.id_usuario = u.id_usuario WHERE s.id_sesion = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $usuario = null;
            if ($row['id_usuario']) {
                $usuario = new Usuario($row['id_usuario'], $row['username'], '', $row['rol'], true);
            }
            return new Sesion($row['id_sesion'], $row['fecha_inicio'], $row['fecha_fin'], $usuario);
        }
        return null;
    }

    public function cerrarSesion(int $id): void {
        $sql = "UPDATE sesion SET fecha_fin = ? WHERE id_sesion = ?";
        $stmt = $this->connection->prepare($sql);
        $fechaFin = date('Y-m-d H:i:s');
        $stmt->bind_param("si", $fechaFin, $id);
        $stmt->execute();
    }

    public function crear($entidad) {
        $this->crearSesion($entidad);
    }

    public function obtenerPorId($id) {
        return $this->buscarSesion($id);
    }

    public function actualizar($entidad) {
        $sql = "UPDATE sesion SET fecha_inicio = ?, fecha_fin = ?, id_usuario = ? WHERE id_sesion = ?";
        $stmt = $this->connection->prepare($sql);
        $idUsuario = $entidad->getUsuarioActivo() ? $entidad->getUsuarioActivo()->getIdUsuario() : null;
        $stmt->bind_param("ssii", $entidad->getFechaInicio(), $entidad->getFechaFin(), $idUsuario, $entidad->getIdSesion());
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        $sql = "DELETE FROM sesion WHERE id_sesion = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

?>