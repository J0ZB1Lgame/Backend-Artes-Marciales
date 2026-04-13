<?php
// ================== HEADERS API ==================
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once __DIR__ . '/../../controllers/login/UsuarioController.php';
require_once __DIR__ . '/../../controllers/login/SesionController.php';
require_once __DIR__ . '/../../controllers/login/LogController.php';

$usuarioController = new UsuarioController();
$sesionController = new SesionController();
$logController = new LogController();
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
    if ($metodo === 'POST') {
        $datos = json_decode(file_get_contents('php://input'), true);
        if (!is_array($datos)) {
            $datos = [];
        }
        switch ($action) {
            // ================== AUTENTICACIÓN ==================
            case 'login':
            case 'iniciar-sesion':
                if (!isset($datos['username']) || !isset($datos['password'])) {
                    apiError('Faltan parámetros: username, password');
                }
                $resultado = $sesionController->iniciarSesion($datos['username'], $datos['password']);
                if ($resultado) {
                    apiResponse($resultado, 'Sesión iniciada', 200);
                }
                apiError('Usuario o contraseña inválidos', 401);
                break;
            
            case 'logout':
            case 'cerrar-sesion':
                if (!isset($datos['id_sesion'])) {
                    apiError('Falta id_sesion');
                }
                $sesionController->cerrarSesion((int) $datos['id_sesion']);
                apiResponse(null, 'Sesión cerrada', 200);
                break;
            
            // ================== USUARIOS ==================
            case 'crear-usuario':
                $resultado = $usuarioController->crearUsuario($datos);
                if ($resultado) {
                    apiResponse($resultado, 'Usuario creado', 201);
                }
                apiError('Error al crear usuario');
                break;
            
            case 'actualizar-usuario':
                if (!isset($datos['id_usuario'])) {
                    apiError('ID de usuario requerido');
                }
                $resultado = $usuarioController->actualizarInformacion($datos);
                if ($resultado) {
                    apiResponse(null, 'Usuario actualizado');
                }
                apiError('Error al actualizar usuario');
                break;
            
            case 'actualizar-password':
                if (!isset($datos['id_usuario']) || !isset($datos['password'])) {
                    apiError('Faltan parámetros: id_usuario, password');
                }
                $resultado = $usuarioController->actualizarPassword((int) $datos['id_usuario'], $datos['password']);
                if ($resultado) {
                    apiResponse(null, 'Password actualizado');
                }
                apiError('Error al actualizar password');
                break;
            
            default:
                apiError('Acción no válida', 404);
        }
    } elseif ($metodo === 'GET') {
        switch ($action) {
            case 'listar-usuarios':
                apiResponse($usuarioController->mostrarUsuario(), 'Usuarios listados');
                break;
            case 'buscar-usuario':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                apiResponse($usuarioController->buscarUsuario((int) $_GET['id']), 'Usuario encontrado');
                break;
            case 'consultar-log':
                apiResponse($logController->consultarLog(), 'Historial de logs obtenido');
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
            case 'actualizar-usuario':
                if (!isset($datos['id_usuario'])) {
                    apiError('ID de usuario requerido');
                }
                $resultado = $usuarioController->actualizarInformacion($datos);
                if ($resultado) {
                    apiResponse(null, 'Usuario actualizado');
                }
                apiError('Error al actualizar usuario');
                break;
            case 'actualizar-password':
                if (!isset($datos['id_usuario']) || !isset($datos['password'])) {
                    apiError('Faltan parámetros: id_usuario, password');
                }
                $resultado = $usuarioController->actualizarPassword((int) $datos['id_usuario'], $datos['password']);
                if ($resultado) {
                    apiResponse(null, 'Password actualizado');
                }
                apiError('Error al actualizar password');
                break;
            default:
                apiError('Acción no válida', 404);
        }
    } elseif ($metodo === 'DELETE') {
        switch ($action) {
            case 'eliminar-usuario':
                if (!isset($_GET['id'])) {
                    apiError('ID requerido');
                }
                $resultado = $usuarioController->eliminarUsuario((int) $_GET['id']);
                if ($resultado) {
                    apiResponse(null, 'Usuario eliminado');
                }
                apiError('Error al eliminar usuario');
                break;
            default:
                apiError('Acción no válida', 404);
        }
    } else {
        apiError('Método no permitido', 405);
    }
} catch (Exception $e) {
    apiError('Error interno del servidor: ' . $e->getMessage(), 500);
}
?>