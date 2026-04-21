<?php

require_once __DIR__ . '/../interfaces/IPermisoDAO.php';
require_once __DIR__ . '/../../../entities/staff/Permiso.php';
require_once __DIR__ . '/../../../../config/conexion.php';
require_once __DIR__ . '/../../base/BaseDAO.php';

class PermisoDAOImpl extends BaseDAO implements IPermisoDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crear($permiso) {
        $sql = "INSERT INTO permiso (nombre) VALUES (?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("s", $permiso->getNombre());

        if ($stmt->execute()) {
            $permiso->setIdPermiso($this->connection->insert_id);
            return $permiso;
        }
        return false;
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM permiso WHERE id_permiso = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return new Permiso($row['id_permiso'], $row['nombre']);
        }
        return null;
    }

    public function actualizar($permiso) {
        $sql = "UPDATE permiso SET nombre = ? WHERE id_permiso = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("si", $permiso->getNombre(), $permiso->getIdPermiso());
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        $sql = "DELETE FROM permiso WHERE id_permiso = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        return $this->eliminarPorId($id);
    }

    public function obtenerTodos() {
        $sql = "SELECT * FROM permiso";
        $result = $this->connection->query($sql);
        $permisos = [];

        while ($row = $result->fetch_assoc()) {
            $permisos[] = new Permiso($row['id_permiso'], $row['nombre']);
        }
        return $permisos;
    }
}

?>