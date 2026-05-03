<?php

require_once __DIR__ . '/../../../models/daos/torneo/impl/TorneoDAOImpl.php';
require_once __DIR__ . '/../../../models/entities/torneo/Torneo.php';
require_once __DIR__ . '/../../../config/conexion.php';

class TorneoController {
    private $torneoDAO;

    public function __construct() {
        global $conn;
        $this->torneoDAO = new TorneoDAOImpl();
    }

    public function jsonResponse($data, $message = "OK", $status = 200) {
        http_response_code($status);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public function jsonError($message, $status = 400) {
        http_response_code($status);
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }

    public function crearTorneo($torneo){

        //Validar datos
        if (!isset($torneo['nombre']) || !isset($torneo['estado'])) {
            return false;
        }

        $torneo = new Torneo(null,$data['nombre'],$data['estado']);
        $resultado = $this->torneoDAO->crear($torneo);
        return $resultado;   
    }

    public function buscarTorneo(string $nombreTorneo){
       return $this->torneoDAO->buscarPorNombre($nombreTorneo);
    }

    public function mostrarTorneo(): array {
        $torneos = $this->torneoDAO->listarTodos();
        $response = [];
        foreach ($torneos as $torneo) {
            $response[] = [
                'id_torneo'=>$torneo->getIdTorneo(),
                'nombre'=>$torneo->getNombre(),
                'estado'=>$torneo->getEstado()
            ];
        }
        return $response;
    }

    
}