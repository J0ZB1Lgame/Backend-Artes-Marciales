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