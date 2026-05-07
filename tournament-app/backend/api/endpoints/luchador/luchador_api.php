<?php
// ================== HEADERS API ==================
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once __DIR__ . '/../../controllers/staff/StaffTorneoController.php';

$controller = new StaffTorneoController();
$metodo = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($metodo === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function apiResponse($data, $message = "OK", $status = 200) {
    http_response_code($status);
    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function apiError($message, $status = 400) {
    http_response_code($status);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
    exit;
}

try {
    if ($metodo === 'GET') {
        if (!$action || $action === 'listar') {
            $data = $controller->mostrarLuchadores();
            apiResponse($data, "Luchadores listados");
        } elseif ($action === 'obtener' && isset($_GET['id'])) {
            $luchador = $controller->buscarLuchador((int)$_GET['id']);
            if ($luchador) {
                apiResponse([
                    'id_luchador' => $luchador->getIdLuchador(),
                    'nombre' => $luchador->getNombre(),
                    'especie' => $luchador->getEspecie(),
                    'nivelDePoderKi' => $luchador->getNivelDePoderKi(),
                    'origen' => $luchador->getOrigen(),
                    'estado' => $luchador->getEstado()
                ], "Luchador obtenido");
            } else {
                apiError("Luchador no encontrado", 404);
            }
        } else {
            apiError("Acción no válida", 404);
        }
    } elseif ($metodo === 'POST') {
        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos) || !isset($datos['nombre'])) {
            apiError("Falta el nombre del luchador");
        }
        $resultado = $controller->crearLuchador($datos);
        if ($resultado) {
            apiResponse($resultado, "Luchador creado", 201);
        } else {
            apiError("Error al crear luchador");
        }
    } elseif ($metodo === 'PUT') {
        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos) || !isset($datos['nombre'])) {
            apiError("Nombre requerido");
        }
        $resultado = $controller->actualizarInformacionLuchador($datos);
        if ($resultado) {
            apiResponse(null, "Luchador actualizado");
        } else {
            apiError("Error al actualizar luchador");
        }
    } elseif ($metodo === 'DELETE') {
        if ($action === 'eliminar' && isset($_GET['id'])) {
            $resultado = $controller->eliminarLuchador((int)$_GET['id']);
            if ($resultado) {
                apiResponse(null, "Luchador eliminado");
            } else {
                apiError("Error al eliminar luchador");
            }
        } else {
            apiError("Acción no válida", 404);
        }
    } else {
        apiError("Método no permitido", 405);
    }
} catch (Exception $e) {
    apiError("Error del servidor: " . $e->getMessage(), 500);
}
?>
