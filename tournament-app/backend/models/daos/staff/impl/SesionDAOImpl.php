<?php

require_once __DIR__ . '/../interfaces/ISesionDAO.php';
require_once __DIR__ . '/../../entities/staff/Sesion.php';
require_once __DIR__ . '/../../entities/staff/Usuario.php';
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../base/BaseDAO.php';

class SesionDAOImpl extends BaseDAO implements ISesionDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crear($sesion) {
        return $this->crearSesion($sesion);
    }

    public function crearSesion($sesion): void {
        $sql = "INSERT INTO sesion (fecha_inicio, fecha_fin, id_usuario_activo) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        $fechaFin = $sesion->getFechaFin() ?: null;
        $idUsuario = $sesion->getUsuarioActivo() ? $sesion->getUsuarioActivo()->getIdUsuario() : null;
        $stmt->bind_param("ssi", $sesion->getFechaInicio(), $fechaFin, $idUsuario);
        $stmt->execute();
    }

    public function obtenerPorId($id) {
        return $this->buscarSesion($id);
    }

    public function buscarSesion(int $id) {
        $sql = "SELECT s.*, u.username, u.password, u.rol, u.estado FROM sesion s LEFT JOIN usuario u ON s.id_usuario_activo = u.id_usuario WHERE s.id_sesion = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $usuario = null;
            if ($row['id_usuario_activo']) {
                $usuario = new Usuario(
                    $row['id_usuario_activo'],
                    $row['username'],
                    $row['password'],
                    $row['rol'],
                    (bool) $row['estado']
                );
            }
            return new Sesion($row['id_sesion'], $row['fecha_inicio'], $row['fecha_fin'], $usuario);
        }
        return null;
    }

    public function actualizar($sesion) {
        $sql = "UPDATE sesion SET fecha_inicio = ?, fecha_fin = ?, id_usuario_activo = ? WHERE id_sesion = ?";
        $stmt = $this->connection->prepare($sql);
        $fechaFin = $sesion->getFechaFin() ?: null;
        $idUsuario = $sesion->getUsuarioActivo() ? $sesion->getUsuarioActivo()->getIdUsuario() : null;
        $stmt->bind_param("ssii", $sesion->getFechaInicio(), $fechaFin, $idUsuario, $sesion->getIdSesion());
        return $stmt->execute();
    }

    public function cerrarSesion(int $id): void {
        $sql = "UPDATE sesion SET fecha_fin = NOW() WHERE id_sesion = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    public function eliminarPorId($id) {
        $sql = "DELETE FROM sesion WHERE id_sesion = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

?>