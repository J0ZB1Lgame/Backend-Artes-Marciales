<?php

require_once __DIR__ . '/../../../models/daos/torneo/impl/TorneoDAOImpl.php';

class TorneoController {

    /*
    |--------------------------------------------------------------------------
    | DAO
    |--------------------------------------------------------------------------
    */

    private $torneoDAO;

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(){

        $this->torneoDAO = new TorneoDAOImpl();

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener todos
    |--------------------------------------------------------------------------
    */

    public function getAll(){

        try {

            $data = $this->torneoDAO->getAll();

            return [

                "success" => true,

                "message" => "Torneos obtenidos correctamente",

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

            $data = $this->torneoDAO->getById($id);

            if(!$data){

                return [

                    "success" => false,

                    "message" => "Torneo no encontrado"

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
    | Crear torneo
    |--------------------------------------------------------------------------
    */

    public function create($data){

        try {

            if(empty($data["nombre"])){

                return [

                    "success" => false,

                    "message" => "Nombre requerido"

                ];

            }

            $id = $this->torneoDAO->create($data);

            return [

                "success" => true,

                "message" => "Torneo creado correctamente",

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
    | Actualizar torneo
    |--------------------------------------------------------------------------
    */

    public function update($id, $data){

        try {

            $exists = $this->torneoDAO->getById($id);

            if(!$exists){

                return [

                    "success" => false,

                    "message" => "Torneo no encontrado"

                ];

            }

            $this->torneoDAO->update($id, $data);

            return [

                "success" => true,

                "message" => "Torneo actualizado"

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
    | Eliminar torneo
    |--------------------------------------------------------------------------
    */

    public function delete($id){

        try {

            $exists = $this->torneoDAO->getById($id);

            if(!$exists){

                return [

                    "success" => false,

                    "message" => "Torneo no encontrado"

                ];

            }

            $this->torneoDAO->delete($id);

            return [

                "success" => true,

                "message" => "Torneo eliminado"

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

            $data = $this->torneoDAO->search($search);

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

            $data = $this->torneoDAO->countAll();

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