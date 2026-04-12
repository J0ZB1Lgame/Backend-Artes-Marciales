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
        switch ($action) {
            case 'staff_torneo/listar':
                $ejecutor = $_GET['ejecutor'] ?? null;
                $torneo = $_GET['torneo'] ?? null;
                if (!$ejecutor || !$torneo) {
                    apiError('Parámetros requeridos: ejecutor, torneo');
                }
                apiResponse($controller->listarStaffPorTorneo($ejecutor, $torneo), 'Staff del torneo listado');
                break;
            case 'rol/listar':
                apiResponse($controller->mostrarRol(), 'Roles listados');
                break;
            case 'rol/buscar':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                apiResponse($controller->buscarRol((int) $_GET['id']), 'Rol encontrado');
                break;
            case 'zona/listar':
                apiResponse($controller->mostrarZona(), 'Zonas listadas');
                break;
            case 'zona/buscar':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                apiResponse($controller->buscarZona((int) $_GET['id']), 'Zona encontrada');
                break;
            case 'luchador/listar':
                apiResponse($controller->mostrarLuchadores(), 'Luchadores listados');
                break;
            case 'luchador/buscar':
                if (!isset($_GET['nombre'])) {
                    apiError('Nombre requerido');
                }
                apiResponse($controller->buscarLuchador($_GET['nombre']), 'Luchador encontrado');
                break;
            default:
                apiError('Acción no válida', 404);
        }

    } elseif ($metodo === 'POST') {
        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos)) {
            $datos = [];
        }
        switch ($action) {
            case 'staff_torneo/registrar':
                if (!isset($datos['ejecutor']) || !isset($datos['datos_staff'])) {
                    apiError('Faltan parámetros: ejecutor, datos_staff');
                }
                $resultado = $controller->registrarStaff($datos['ejecutor'], $datos['datos_staff']);
                if ($resultado) {
                    apiResponse($resultado, 'Staff de torneo registrado', 201);
                }
                apiError('Error al registrar staff de torneo');
                break;
            case 'staff_torneo/asignar':
                if (!isset($datos['ejecutor']) || !isset($datos['staff']) || !isset($datos['torneo'])) {
                    apiError('Faltan parámetros: ejecutor, staff, torneo');
                }
                $resultado = $controller->asignarStaffATorneo($datos['ejecutor'], $datos['staff'], $datos['torneo']);
                if ($resultado) {
                    apiResponse(null, 'Staff asignado al torneo', 201);
                }
                apiError('Error al asignar staff al torneo');
                break;
            case 'staff_torneo/asignar-zona':
                if (!isset($datos['ejecutor']) || !isset($datos['staff']) || !isset($datos['zona'])) {
                    apiError('Faltan parámetros: ejecutor, staff, zona');
                }
                $resultado = $controller->asignarZona($datos['ejecutor'], $datos['staff'], $datos['zona']);
                if ($resultado) {
                    apiResponse(null, 'Zona asignada al staff', 201);
                }
                apiError('Error al asignar zona');
                break;
            case 'staff_torneo/asignar-rol':
                if (!isset($datos['ejecutor']) || !isset($datos['staff']) || !isset($datos['rol'])) {
                    apiError('Faltan parámetros: ejecutor, staff, rol');
                }
                $resultado = $controller->asignarRol($datos['ejecutor'], $datos['staff'], $datos['rol']);
                if ($resultado) {
                    apiResponse(null, 'Rol asignado al staff', 201);
                }
                apiError('Error al asignar rol');
                break;
            case 'rol/crear':
                $resultado = $controller->crearRol($datos);
                if ($resultado) {
                    apiResponse($resultado, 'Rol creado', 201);
                }
                apiError('Error al crear rol');
                break;
            case 'zona/crear':
                $resultado = $controller->crearZona($datos);
                if ($resultado) {
                    apiResponse($resultado, 'Zona creada', 201);
                }
                apiError('Error al crear zona');
                break;
            case 'luchador/crear':
                $resultado = $controller->crearLuchador($datos);
                if ($resultado) {
                    apiResponse($resultado, 'Luchador creado', 201);
                }
                apiError('Error al crear luchador');
                break;
            default:
                apiError('Acción no válida', 404);
        }
    } elseif ($metodo === 'PUT') {
        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos)) {
            $datos = [];
        }
        switch ($action) {
            case 'rol/actualizar':
                if (!isset($datos['id_rol'])) {
                    apiError('ID de rol requerido');
                }
                $resultado = $controller->actualizarInformacionRol($datos);
                if ($resultado) {
                    apiResponse(null, 'Rol actualizado');
                }
                apiError('Error al actualizar rol');
                break;
            case 'zona/actualizar':
                if (!isset($datos['id_zona'])) {
                    apiError('ID de zona requerido');
                }
                $resultado = $controller->actualizarInformacionZona($datos);
                if ($resultado) {
                    apiResponse(null, 'Zona actualizada');
                }
                apiError('Error al actualizar zona');
                break;
            case 'luchador/actualizar':
                if (!isset($datos['nombre'])) {
                    apiError('Nombre del luchador requerido');
                }
                $resultado = $controller->actualizarInformacionLuchador($datos);
                if ($resultado) {
                    apiResponse(null, 'Luchador actualizado');
                }
                apiError('Error al actualizar luchador');
                break;
            default:
                apiError('Acción no válida', 404);
        }
    } elseif ($metodo === 'DELETE') {
        switch ($action) {
            case 'staff_torneo/eliminar':
                if (!isset($_GET['ejecutor']) || !isset($_GET['staff']) || !isset($_GET['torneo'])) {
                    apiError('Parámetros requeridos: ejecutor, staff, torneo');
                }
                $resultado = $controller->eliminarStaffDeTorneo($_GET['ejecutor'], $_GET['staff'], $_GET['torneo']);
                if ($resultado) {
                    apiResponse(null, 'Staff eliminado del torneo');
                }
                apiError('Error al eliminar staff del torneo');
                break;
            case 'rol/eliminar':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                $resultado = $controller->eliminarRol((int) $_GET['id']);
                if ($resultado) {
                    apiResponse(null, 'Rol eliminado');
                }
                apiError('Error al eliminar rol');
                break;
            case 'zona/eliminar':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                $resultado = $controller->eliminarZona((int) $_GET['id']);
                if ($resultado) {
                    apiResponse(null, 'Zona eliminada');
                }
                apiError('Error al eliminar zona');
                break;
            case 'luchador/eliminar':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                $resultado = $controller->eliminarLuchador((int) $_GET['id']);
                if ($resultado) {
                    apiResponse(null, 'Luchador eliminado');
                }
                apiError('Error al eliminar luchador');
                break;
            default:
                apiError('Acción no válida', 404);
        }
    } else {
        apiError('Método no permitido', 405);
    }
} catch (Exception $e) {
    apiError('Error del servidor: ' . $e->getMessage(), 500);
}

?>