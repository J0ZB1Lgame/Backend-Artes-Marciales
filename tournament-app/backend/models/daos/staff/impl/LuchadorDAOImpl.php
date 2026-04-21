<?php

require_once __DIR__ . '/../interfaces/ILuchadorDAO.php';
require_once __DIR__ . '/../../../entities/staff/Luchador.php';
require_once __DIR__ . '/../../../../config/conexion.php';
require_once __DIR__ . '/../../base/BaseDAO.php';

class LuchadorDAOImpl extends BaseDAO implements ILuchadorDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crear($luchador) {
        $sql = "INSERT INTO luchador (nombre, especie, nivel_poder_ki, origen, estado) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        $estado = $luchador->getEstado() ? 1 : 0;
        $stmt->bind_param(
            "ssdsi",
            $luchador->getNombre(),
            $luchador->getEspecie(),
            $luchador->getNivelDePoderKi(),
            $luchador->getOrigen(),
            $estado
        );

        if ($stmt->execute()) {
            $luchador->setIdLuchador($this->connection->insert_id);
            return $luchador;
        }
        return false;
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM luchador WHERE id_luchador = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return new Luchador(
                $row['id_luchador'],
                $row['nombre'],
                $row['especie'],
                (float) $row['nivel_poder_ki'],
                $row['origen'],
                (bool) $row['estado']
            );
        }
        return null;
    }

    public function actualizar($luchador) {
        $sql = "UPDATE luchador SET nombre = ?, especie = ?, nivel_poder_ki = ?, origen = ?, estado = ? WHERE id_luchador = ?";
        $stmt = $this->connection->prepare($sql);
        $estado = $luchador->getEstado() ? 1 : 0;
        $stmt->bind_param(
            "ssdsii",
            $luchador->getNombre(),
            $luchador->getEspecie(),
            $luchador->getNivelDePoderKi(),
            $luchador->getOrigen(),
            $estado,
            $luchador->getIdLuchador()
        );
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        $sql = "DELETE FROM luchador WHERE id_luchador = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function listarTodos() {
        $sql = "SELECT * FROM luchador";
        $result = $this->connection->query($sql);
        $lista = [];

        while ($row = $result->fetch_assoc()) {
            $lista[] = new Luchador(
                $row['id_luchador'],
                $row['nombre'],
                $row['especie'],
                (float) $row['nivel_poder_ki'],
                $row['origen'],
                (bool) $row['estado']
            );
        }
        return $lista;
    }

    public function buscarPorNombre($nombre) {
        $sql = "SELECT * FROM luchador WHERE nombre = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return new Luchador(
                $row['id_luchador'],
                $row['nombre'],
                $row['especie'],
                (float) $row['nivel_poder_ki'],
                $row['origen'],
                (bool) $row['estado']
            );
        }
        return null;
    }
}

?>