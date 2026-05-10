<?php

require_once __DIR__ . '/../../../models/daos/luchador/impl/LuchadorDAOImpl.php';

class LuchadorController {

    /*
    |--------------------------------------------------------------------------
    | DAO
    |--------------------------------------------------------------------------
    */

    private $luchadorDAO;

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(){

        $this->luchadorDAO = new LuchadorDAOImpl();

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener todos
    |--------------------------------------------------------------------------
    */

    public function getAll(){

        try {

            $data = $this->luchadorDAO->getAll();

            return [

                "success" => true,

                "message" => "Luchadores obtenidos correctamente",

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

            $data = $this->luchadorDAO->getById($id);

            if(!$data){

                return [

                    "success" => false,

                    "message" => "Luchador no encontrado"

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
    | Crear luchador
    |--------------------------------------------------------------------------
    */

    public function create($data){

        try {

            if(empty($data["nombre"])){

                return [

                    "success" => false,

                    "message" => "Nombre obligatorio"

                ];

            }

            if(empty($data["apellido"])){

                return [

                    "success" => false,

                    "message" => "Apellido obligatorio"

                ];

            }

            $id = $this->luchadorDAO->create($data);

            return [

                "success" => true,

                "message" => "Luchador creado correctamente",

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
    | Actualizar luchador
    |--------------------------------------------------------------------------
    */

    public function update($id, $data){

        try {

            $exists = $this->luchadorDAO->getById($id);

            if(!$exists){

                return [

                    "success" => false,

                    "message" => "Luchador no encontrado"

                ];

            }

            $this->luchadorDAO->update($id, $data);

            return [

                "success" => true,

                "message" => "Luchador actualizado"

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
    | Eliminar luchador
    |--------------------------------------------------------------------------
    */

    public function delete($id){

        try {

            $exists = $this->luchadorDAO->getById($id);

            if(!$exists){

                return [

                    "success" => false,

                    "message" => "Luchador no encontrado"

                ];

            }

            $this->luchadorDAO->delete($id);

            return [

                "success" => true,

                "message" => "Luchador eliminado"

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

            $data = $this->luchadorDAO->search($search);

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

            $data = $this->luchadorDAO->countAll();

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