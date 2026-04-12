<?php
// ================== HEADERS API ==================
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once __DIR__ . '/../controllers/staff/UsuarioController.php';
require_once __DIR__ . '/../controllers/staff/SesionController.php';
require_once __DIR__ . '/../controllers/staff/LogController.php';
require_once __DIR__ . '/../controllers/staff/StaffTorneoController.php';
require_once __DIR__ . '/../controllers/staff/RolController.php';
require_once __DIR__ . '/../controllers/staff/ZonaController.php';
require_once __DIR__ . '/../controllers/staff/LuchadorController.php';

$usuarioController = new UsuarioController();
$sesionController = new SesionController();
$logController = new LogController();
$staffTorneoController = new StaffTorneoController();
$rolController = new RolController();
$zonaController = new ZonaController();
$luchadorController = new LuchadorController();

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
            case 'usuario/listar':
                apiResponse($usuarioController->mostrarUsuario(), 'Usuarios listados');
                break;
            case 'usuario/buscar':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                apiResponse($usuarioController->buscarUsuario((int) $_GET['id']), 'Usuario encontrado');
                break;
            case 'usuario/estado':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                apiResponse($usuarioController->verEstado((int) $_GET['id']), 'Estado de usuario obtenido');
                break;
            case 'sesion/buscar':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                apiResponse($sesionController->buscarSesion((int) $_GET['id']), 'Sesión encontrada');
                break;
            case 'sesion/usuario':
                apiResponse($sesionController->getUsuarioLogueado(), 'Usuario en sesión obtenido');
                break;
            case 'log/consultar':
                apiResponse($logController->consultarLog(), 'Historial de logs obtenido');
                break;
            case 'staff_torneo/listar':
                $ejecutor = $_GET['ejecutor'] ?? null;
                $torneo = $_GET['torneo'] ?? null;
                if (!$ejecutor || !$torneo) {
                    apiError('Parámetros requeridos: ejecutor, torneo');
                }
                apiResponse($staffTorneoController->listarStaffPorTorneo($ejecutor, $torneo), 'Staff del torneo listado');
                break;
            case 'rol/listar':
                apiResponse($rolController->mostrarRol(), 'Roles listados');
                break;
            case 'rol/buscar':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                apiResponse($rolController->buscarRol((int) $_GET['id']), 'Rol encontrado');
                break;
            case 'zona/listar':
                apiResponse($zonaController->mostrarZona(), 'Zonas listadas');
                break;
            case 'zona/buscar':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                apiResponse($zonaController->buscarZona((int) $_GET['id']), 'Zona encontrada');
                break;
            case 'luchador/listar':
                apiResponse($luchadorController->mostrarLuchadores(), 'Luchadores listados');
                break;
            case 'luchador/buscar':
                if (!isset($_GET['nombre'])) {
                    apiError('Nombre requerido');
                }
                apiResponse($luchadorController->buscarLuchador($_GET['nombre']), 'Luchador encontrado');
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
            case 'usuario/crear':
                $resultado = $usuarioController->crearUsuario($datos);
                if ($resultado) {
                    apiResponse($resultado, 'Usuario creado', 201);
                }
                apiError('Error al crear usuario');
                break;
            case 'sesion/iniciar':
                if (!isset($datos['username']) || !isset($datos['password'])) {
                    apiError('Faltan parámetros: username, password');
                }
                $resultado = $sesionController->iniciarSesion($datos['username'], $datos['password']);
                if ($resultado) {
                    apiResponse(null, 'Sesión iniciada', 201);
                }
                apiError('Usuario o contraseña inválidos', 401);
                break;
            case 'log/registrar':
                if (!isset($datos['id_usuario']) || !isset($datos['accion'])) {
                    apiError('Faltan parámetros: id_usuario, accion');
                }
                $evento = new Log(null, $datos['accion'], $datos['fecha'] ?? date('Y-m-d H:i:s'), (int) $datos['id_usuario']);
                $logController->registrarEvento((int) $datos['id_usuario']);
                apiResponse(null, 'Evento registrado', 201);
                break;
            case 'staff_torneo/registrar':
                if (!isset($datos['ejecutor']) || !isset($datos['datos_staff'])) {
                    apiError('Faltan parámetros: ejecutor, datos_staff');
                }
                $resultado = $staffTorneoController->registrarStaff($datos['ejecutor'], $datos['datos_staff']);
                if ($resultado) {
                    apiResponse($resultado, 'Staff de torneo registrado', 201);
                }
                apiError('Error al registrar staff de torneo');
                break;
            case 'staff_torneo/asignar':
                if (!isset($datos['ejecutor']) || !isset($datos['staff']) || !isset($datos['torneo'])) {
                    apiError('Faltan parámetros: ejecutor, staff, torneo');
                }
                $resultado = $staffTorneoController->asignarStaffATorneo($datos['ejecutor'], $datos['staff'], $datos['torneo']);
                if ($resultado) {
                    apiResponse(null, 'Staff asignado al torneo', 201);
                }
                apiError('Error al asignar staff al torneo');
                break;
            case 'staff_torneo/asignar-zona':
                if (!isset($datos['ejecutor']) || !isset($datos['staff']) || !isset($datos['zona'])) {
                    apiError('Faltan parámetros: ejecutor, staff, zona');
                }
                $resultado = $staffTorneoController->asignarZona($datos['ejecutor'], $datos['staff'], $datos['zona']);
                if ($resultado) {
                    apiResponse(null, 'Zona asignada al staff', 201);
                }
                apiError('Error al asignar zona');
                break;
            case 'staff_torneo/asignar-rol':
                if (!isset($datos['ejecutor']) || !isset($datos['staff']) || !isset($datos['rol'])) {
                    apiError('Faltan parámetros: ejecutor, staff, rol');
                }
                $resultado = $staffTorneoController->asignarRol($datos['ejecutor'], $datos['staff'], $datos['rol']);
                if ($resultado) {
                    apiResponse(null, 'Rol asignado al staff', 201);
                }
                apiError('Error al asignar rol');
                break;
            case 'rol/crear':
                $resultado = $rolController->crearRol($datos);
                if ($resultado) {
                    apiResponse($resultado, 'Rol creado', 201);
                }
                apiError('Error al crear rol');
                break;
            case 'zona/crear':
                $resultado = $zonaController->crearZona($datos);
                if ($resultado) {
                    apiResponse($resultado, 'Zona creada', 201);
                }
                apiError('Error al crear zona');
                break;
            case 'luchador/crear':
                $resultado = $luchadorController->crearLuchador($datos);
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
            case 'usuario/actualizar':
                if (!isset($datos['id_usuario'])) {
                    apiError('ID de usuario requerido');
                }
                $resultado = $usuarioController->actualizarInformacion($datos);
                if ($resultado) {
                    apiResponse(null, 'Usuario actualizado');
                }
                apiError('Error al actualizar usuario');
                break;
            case 'usuario/actualizar-username':
                if (!isset($datos['id_usuario']) || !isset($datos['username'])) {
                    apiError('Faltan parámetros: id_usuario, username');
                }
                $resultado = $usuarioController->actualizarUsername((int) $datos['id_usuario'], $datos['username']);
                if ($resultado) {
                    apiResponse(null, 'Username actualizado');
                }
                apiError('Error al actualizar username');
                break;
            case 'usuario/actualizar-password':
                if (!isset($datos['id_usuario']) || !isset($datos['password'])) {
                    apiError('Faltan parámetros: id_usuario, password');
                }
                $resultado = $usuarioController->actualizarPassword((int) $datos['id_usuario'], $datos['password']);
                if ($resultado) {
                    apiResponse(null, 'Password actualizado');
                }
                apiError('Error al actualizar password');
                break;
            case 'rol/actualizar':
                if (!isset($datos['id_rol'])) {
                    apiError('ID de rol requerido');
                }
                $resultado = $rolController->actualizarInformacion($datos);
                if ($resultado) {
                    apiResponse(null, 'Rol actualizado');
                }
                apiError('Error al actualizar rol');
                break;
            case 'zona/actualizar':
                if (!isset($datos['id_zona'])) {
                    apiError('ID de zona requerido');
                }
                $resultado = $zonaController->actualizarInformacion($datos);
                if ($resultado) {
                    apiResponse(null, 'Zona actualizada');
                }
                apiError('Error al actualizar zona');
                break;
            case 'luchador/actualizar':
                if (!isset($datos['nombre'])) {
                    apiError('Nombre del luchador requerido');
                }
                $resultado = $luchadorController->actualizarInformacion($datos);
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
            case 'usuario/eliminar':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                $resultado = $usuarioController->eliminarUsuario((int) $_GET['id']);
                if ($resultado) {
                    apiResponse(null, 'Usuario eliminado');
                }
                apiError('Error al eliminar usuario');
                break;
            case 'staff_torneo/eliminar':
                if (!isset($_GET['ejecutor']) || !isset($_GET['staff']) || !isset($_GET['torneo'])) {
                    apiError('Parámetros requeridos: ejecutor, staff, torneo');
                }
                $resultado = $staffTorneoController->eliminarStaffDeTorneo($_GET['ejecutor'], $_GET['staff'], $_GET['torneo']);
                if ($resultado) {
                    apiResponse(null, 'Staff eliminado del torneo');
                }
                apiError('Error al eliminar staff del torneo');
                break;
            case 'rol/eliminar':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                $resultado = $rolController->eliminarRol((int) $_GET['id']);
                if ($resultado) {
                    apiResponse(null, 'Rol eliminado');
                }
                apiError('Error al eliminar rol');
                break;
            case 'zona/eliminar':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                $resultado = $zonaController->eliminarZona((int) $_GET['id']);
                if ($resultado) {
                    apiResponse(null, 'Zona eliminada');
                }
                apiError('Error al eliminar zona');
                break;
            case 'luchador/eliminar':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                $resultado = $luchadorController->eliminarLuchador((int) $_GET['id']);
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