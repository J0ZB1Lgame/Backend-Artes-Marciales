<?php

require_once __DIR__ . '/../../base/BaseDAO.php';
require_once __DIR__ . '/../interfaces/ITorneoDAO.php';
require_once __DIR__ . '/../../../entities/torneo/Torneo.php';
require_once __DIR__ . '/../../../../config/conexion.php';

class TorneoDAOImpl extends BaseDAO implements ITorneoDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crear($torneo) {
        $sql = "INSERT INTO torneo(nombre, estado) VALUES (?,?)";
        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ss",
            $torneo->getNombre(),
            $torneo->getEstado()
        );

        if ($stmt->execute()) {
            $torneo->setIdTorneo($conn->insert_id);
            return $torneo;
        }
        return false;
    }

    public function obtenerPorId($id){
         
        $sql = "SELECT * FROM torneo WHERE id_torneo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if($row = $result->fetch_assoc()) {
            return new Torneo(
                $row['id_torneo'],
                $row['nombre'],
                $row['estado']
            );
        }
        return null;
    }

    public function actualizar($torneo){
        
        $sql= "UPDATE torneo SET nombre=?, estado=?";
        $stmt= $conn->prepare($sql);
        $stmt->bind_param(
            "ss",
            $torneo->getNombre(),
            $torneo->getEstado()
        );
        
        return stmt->execute();
    }

    public function eliminarPorId($id) {

        $sql = "DELETE FROM torneo WHERE id_torneo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function listarTodos(){

        $sql = "SELECT * FROM torneo";
        $result = $conn->query($sql);
        $torneoList = [];
        
        while ($row = $result->fetch_assoc()) {
            $torneoList [] = new Torneo(
                $row['nombre'],
                $row['estado']
            );
        }
        return $torneoList;
    }

    public function buscarPorNombre($nombre){
         $sql = "SELECT * FROM torneo WHERE nombre= ?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("s", $nombre);
         $stmt->execute();
         $result = $stmt->get_result();

         if ($row = $result->fetch_assoc()) {
            return new Torneo(
                $row['id_torneo'],
                $row['nombre'],
                $row['estado']
            );
         }
         return null;
    }
}

?>