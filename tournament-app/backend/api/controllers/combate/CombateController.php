<?php

require_once __DIR__ . '/../../../models/daos/combate/impl/CombateDAOImpl.php';

class CombateController {

    /*
    |--------------------------------------------------------------------------
    | DAO
    |--------------------------------------------------------------------------
    */

    private $combateDAO;

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(){

        $this->combateDAO = new CombateDAOImpl();

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener todos
    |--------------------------------------------------------------------------
    */

    public function getAll(){

        try {

            $data = $this->combateDAO->getAll();

            return [

                "success" => true,

                "message" => "Combates obtenidos correctamente",

                "data" => $data

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener por ID
    |--------------------------------------------------------------------------
    */

    public function getById($id){

        try {

            $data = $this->combateDAO->getById($id);

            if(!$data){

                return [

                    "success" => false,

                    "message" => "Combate no encontrado"

                ];

            }

            return [

                "success" => true,

                "data" => $data

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Crear combate
    |--------------------------------------------------------------------------
    */

    public function create($data){

        try {

            if(empty($data["id_torneo"])){

                return [

                    "success" => false,

                    "message" => "Torneo requerido"

                ];

            }

            if(empty($data["id_luchador_1"])){

                return [

                    "success" => false,

                    "message" => "Luchador 1 requerido"

                ];

            }

            if(empty($data["id_luchador_2"])){

                return [

                    "success" => false,

                    "message" => "Luchador 2 requerido"

                ];

            }

            $id = $this->combateDAO->create($data);

            // Si hay un ganador asignado, actualizar victorias y derrotas
            if(!empty($data["ganador_id"])){

                require_once __DIR__ . '/../../../models/daos/luchador/impl/LuchadorDAOImpl.php';
                $luchadorDAO = new LuchadorDAOImpl();
                
                // Sumar victoria al ganador
                $ganador = $luchadorDAO->getById($data["ganador_id"]);
                $nuevasVictorias = ($ganador["victorias"] ?? 0) + 1;
                $luchadorDAO->update($data["ganador_id"], ["victorias" => $nuevasVictorias]);

                // Determinar quién perdió y sumar derrota
                $perdedor_id = ($data["ganador_id"] == $data["id_luchador_1"]) 
                    ? $data["id_luchador_2"] 
                    : $data["id_luchador_1"];
                
                $perdedor = $luchadorDAO->getById($perdedor_id);
                $nuevasDerrotas = ($perdedor["derrotas"] ?? 0) + 1;
                $luchadorDAO->update($perdedor_id, ["derrotas" => $nuevasDerrotas]);

            }

            return [

                "success" => true,

                "message" => "Combate creado correctamente",

                "id" => $id

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar combate
    |--------------------------------------------------------------------------
    */

    public function update($id, $data){

        try {

            $exists = $this->combateDAO->getById($id);

            if(!$exists){

                return [

                    "success" => false,

                    "message" => "Combate no encontrado"

                ];

            }

            // Si hay un ganador nuevo, actualizar victorias y derrotas del luchador
            if(isset($data["ganador_id"]) && $data["ganador_id"]){

                require_once __DIR__ . '/../../../models/daos/luchador/impl/LuchadorDAOImpl.php';
                $luchadorDAO = new LuchadorDAOImpl();

                // Si había un ganador anterior, restar una victoria y una derrota
                if(!empty($exists["ganador_id"]) && $exists["ganador_id"] != $data["ganador_id"]){

                    // Restar victoria al ganador anterior
                    $viejoGanador = $luchadorDAO->getById($exists["ganador_id"]);
                    $nuevasVictorias = max(0, ($viejoGanador["victorias"] ?? 0) - 1);
                    $luchadorDAO->update($exists["ganador_id"], ["victorias" => $nuevasVictorias]);

                    // Restar derrota al perdedor anterior
                    $viajoPerdedor_id = ($exists["ganador_id"] == $exists["id_luchador_1"]) 
                        ? $exists["id_luchador_2"] 
                        : $exists["id_luchador_1"];
                    $viajoPerdedor = $luchadorDAO->getById($viajoPerdedor_id);
                    $nuevasDerrotas = max(0, ($viajoPerdedor["derrotas"] ?? 0) - 1);
                    $luchadorDAO->update($viajoPerdedor_id, ["derrotas" => $nuevasDerrotas]);

                }

                // Sumar una victoria al nuevo ganador
                $ganador = $luchadorDAO->getById($data["ganador_id"]);
                $nuevasVictorias = ($ganador["victorias"] ?? 0) + 1;
                $luchadorDAO->update($data["ganador_id"], ["victorias" => $nuevasVictorias]);

                // Sumar una derrota al nuevo perdedor
                $nuevoPerdedor_id = ($data["ganador_id"] == $data["id_luchador_1"]) 
                    ? $data["id_luchador_2"] 
                    : $data["id_luchador_1"];
                $nuevoPerdedor = $luchadorDAO->getById($nuevoPerdedor_id);
                $nuevasDerrotas = ($nuevoPerdedor["derrotas"] ?? 0) + 1;
                $luchadorDAO->update($nuevoPerdedor_id, ["derrotas" => $nuevasDerrotas]);

            }

            $this->combateDAO->update($id, $data);

            return [

                "success" => true,

                "message" => "Combate actualizado"

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar combate
    |--------------------------------------------------------------------------
    */

    public function delete($id){

        try {

            $exists = $this->combateDAO->getById($id);

            if(!$exists){

                return [

                    "success" => false,

                    "message" => "Combate no encontrado"

                ];

            }

            $this->combateDAO->delete($id);

            return [

                "success" => true,

                "message" => "Combate eliminado"

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Buscar
    |--------------------------------------------------------------------------
    */

    public function search($search){

        try {

            $data = $this->combateDAO->search($search);

            return [

                "success" => true,

                "data" => $data

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Contar
    |--------------------------------------------------------------------------
    */

    public function countAll(){

        try {

            $data = $this->combateDAO->countAll();

            return [

                "success" => true,

                "data" => $data

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

}

?>