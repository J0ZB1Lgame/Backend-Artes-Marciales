<?php

/*
|--------------------------------------------------------------------------
| HEADERS
|--------------------------------------------------------------------------
*/

header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

/*
|--------------------------------------------------------------------------
| OPTIONS
|--------------------------------------------------------------------------
*/

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){

    http_response_code(200);

    exit();

}

/*
|--------------------------------------------------------------------------
| CONTROLLER
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../controllers/luchador/LuchadorController.php';

$controller = new LuchadorController();

/*
|--------------------------------------------------------------------------
| REQUEST
|--------------------------------------------------------------------------
*/

$method = $_SERVER['REQUEST_METHOD'];

$action = $_GET['action'] ?? '';

/*
|--------------------------------------------------------------------------
| RESPONSE HELPERS
|--------------------------------------------------------------------------
*/

function apiResponse($data = null, $message = "OK", $status = 200){

    http_response_code($status);

    echo json_encode([

        "success" => true,

        "message" => $message,

        "data" => $data

    ]);

    exit();

}

function apiError($message = "Error", $status = 400){

    http_response_code($status);

    echo json_encode([

        "success" => false,

        "message" => $message

    ]);

    exit();

}

/*
|--------------------------------------------------------------------------
| TRY
|--------------------------------------------------------------------------
*/

try {

    /*
    |--------------------------------------------------------------------------
    | GET
    |--------------------------------------------------------------------------
    */

    if($method === 'GET'){

        /*
        |--------------------------------------------------------------------------
        | Obtener por ID
        |--------------------------------------------------------------------------
        */

        if($action === 'obtener' && isset($_GET['id'])){

            $result = $controller->getById(

                (int)$_GET['id']

            );

            echo json_encode($result);

            exit();

        }

        /*
        |--------------------------------------------------------------------------
        | Buscar
        |--------------------------------------------------------------------------
        */

        if($action === 'buscar' && isset($_GET['search'])){

            $result = $controller->search(

                $_GET['search']

            );

            echo json_encode($result);

            exit();

        }

        /*
        |--------------------------------------------------------------------------
        | Contar
        |--------------------------------------------------------------------------
        */

        if($action === 'contar'){

            $result = $controller->countAll();

            echo json_encode($result);

            exit();

        }

        /*
        |--------------------------------------------------------------------------
        | Listar
        |--------------------------------------------------------------------------
        */

        $result = $controller->getAll();

        echo json_encode($result);

        exit();

    }

    /*
    |--------------------------------------------------------------------------
    | POST
    |--------------------------------------------------------------------------
    */

    if($method === 'POST'){

        $data = json_decode(

            file_get_contents("php://input"),

            true

        );

        $result = $controller->create($data);

        echo json_encode($result);

        exit();

    }

    /*
    |--------------------------------------------------------------------------
    | PUT
    |--------------------------------------------------------------------------
    */

    if($method === 'PUT'){

        if(!isset($_GET['id'])){

            apiError("ID requerido");

        }

        $data = json_decode(

            file_get_contents("php://input"),

            true

        );

        $result = $controller->update(

            (int)$_GET['id'],

            $data

        );

        echo json_encode($result);

        exit();

    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    if($method === 'DELETE'){

        if(!isset($_GET['id'])){

            apiError("ID requerido");

        }

        $result = $controller->delete(

            (int)$_GET['id']

        );

        echo json_encode($result);

        exit();

    }

    /*
    |--------------------------------------------------------------------------
    | Invalid Method
    |--------------------------------------------------------------------------
    */

    apiError(

        "Método no permitido",

        405

    );

} catch(Exception $e){

    apiError(

        $e->getMessage(),

        500

    );

}

?>