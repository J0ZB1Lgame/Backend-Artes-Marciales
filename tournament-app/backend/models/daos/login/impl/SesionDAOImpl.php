<?php

require_once __DIR__ . '/../../base/BaseDAO.php';
require_once __DIR__ . '/../interfaces/ISesionDAO.php';
require_once __DIR__ . '/../../../entities/login/Sesion.php';
require_once __DIR__ . '/../../../entities/login/Usuario.php';

class SesionDAOImpl extends BaseDAO implements ISesionDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crearSesion($sesion): void {
        $sql = "INSERT INTO sesion (fecha_inicio, fecha_fin, id_usuario) VALUES (?, NULL, ?)";
        $stmt = $this->connection->prepare($sql);
        
        // Guardar valores en variables locales para bind_param
        $fechaInicio = $sesion->getFechaInicio();
        $idUsuario = $sesion->getUsuarioActivo() ? $sesion->getUsuarioActivo()->getIdUsuario() : null;
        
        $stmt->bind_param("si", $fechaInicio, $idUsuario);
        $stmt->execute();
        $sesion->setIdSesion($this->connection->insert_id);
    }

    public function buscarSesion(int $id) {
        $sql = "SELECT s.*, u.username, u.rol FROM sesion s LEFT JOIN usuario u ON s.id_usuario_activo = u.id_usuario WHERE s.id_sesion = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $usuario = null;
            if ($row['id_usuario_activo']) {
                $usuario = new Usuario($row['id_usuario_activo'], $row['username'], '', $row['rol'], true);
            }
            $estado = is_null($row['fecha_fin']);
            return new Sesion($row['id_sesion'], $row['fecha_inicio'], $estado, $usuario);
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

    public function cerrarSesionesActivas(int $idUsuario): void {
        $sql = "UPDATE sesion SET fecha_fin = ? WHERE id_usuario = ? AND fecha_fin IS NULL";
        $stmt = $this->connection->prepare($sql);
        $fechaFin = date('Y-m-d H:i:s');
        $stmt->bind_param("si", $fechaFin, $idUsuario);
        $stmt->execute();
    }

    public function crear($entidad) {
        $this->crearSesion($entidad);
    }

    public function obtenerPorId($id) {
        return $this->buscarSesion($id);
    }

    public function actualizar($entidad) {
        $sql = "UPDATE sesion SET fecha_inicio = ?, fecha_fin = ?, id_usuario_activo = ? WHERE id_sesion = ?";
        $stmt = $this->connection->prepare($sql);
        
        // Guardar valores en variables locales para bind_param
        $fechaInicio = $entidad->getFechaInicio();
        $idUsuario = $entidad->getUsuarioActivo() ? $entidad->getUsuarioActivo()->getIdUsuario() : null;
        $fechaFin = $entidad->getEstado() ? null : date('Y-m-d H:i:s');
        $idSesion = $entidad->getIdSesion();
        
        $stmt->bind_param("ssii", $fechaInicio, $fechaFin, $idUsuario, $idSesion);
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