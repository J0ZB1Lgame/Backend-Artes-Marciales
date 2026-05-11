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
        $sql = "UPDATE sesion SET fecha_inicio = ?, fecha_fin = ?, id_usuario = ? WHERE id_sesion = ?";
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

    public function getAll() {
        $sql = "SELECT s.*, u.username, u.rol FROM sesion s LEFT JOIN usuario u ON s.id_usuario = u.id_usuario ORDER BY s.id_sesion DESC";
        $result = $this->connection->query($sql);
        $sesiones = [];
        while ($row = $result->fetch_assoc()) {
            $usuario = $row['id_usuario'] ? new Usuario($row['id_usuario'], $row['username'], '', $row['rol'], true) : null;
            $sesiones[] = new Sesion($row['id_sesion'], $row['fecha_inicio'], is_null($row['fecha_fin']), $usuario);
        }
        return $sesiones;
    }

    public function getById($id) {
        return $this->buscarSesion((int)$id);
    }

    public function create($data) {
        $sql = "INSERT INTO sesion (fecha_inicio, fecha_fin, id_usuario) VALUES (?, NULL, ?)";
        $stmt = $this->connection->prepare($sql);
        $fecha = $data['fecha_inicio'] ?? date('Y-m-d H:i:s');
        $idUsuario = $data['id_usuario'] ?? null;
        $stmt->bind_param("si", $fecha, $idUsuario);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $sql = "UPDATE sesion SET fecha_fin = ? WHERE id_sesion = ?";
        $stmt = $this->connection->prepare($sql);
        $fechaFin = $data['fecha_fin'] ?? date('Y-m-d H:i:s');
        $stmt->bind_param("si", $fechaFin, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        return $this->eliminarPorId((int)$id);
    }

    public function search($search) {
        $like = "%{$search}%";
        $sql = "SELECT s.*, u.username, u.rol FROM sesion s LEFT JOIN usuario u ON s.id_usuario = u.id_usuario WHERE u.username LIKE ? ORDER BY s.id_sesion DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $result = $stmt->get_result();
        $sesiones = [];
        while ($row = $result->fetch_assoc()) {
            $usuario = $row['id_usuario'] ? new Usuario($row['id_usuario'], $row['username'], '', $row['rol'], true) : null;
            $sesiones[] = new Sesion($row['id_sesion'], $row['fecha_inicio'], is_null($row['fecha_fin']), $usuario);
        }
        return $sesiones;
    }

    public function countAll() {
        $result = $this->connection->query("SELECT COUNT(*) AS total FROM sesion");
        return $result->fetch_assoc();
    }
}

?>