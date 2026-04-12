<?php
// ================== HEADERS API ==================

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// ================== IMPORTS ==================

require_once __DIR__ . '/../../controllers/staff/StaffTorneoController.php';

// ================== ROUTER ==================

$controller = new StaffTorneoController();
$metodo = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($metodo === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {

    if ($metodo === 'GET') {

        if (!$action) {
            $data = $controller->listarStaff();
            $controller->jsonResponse($data, "Lista de staff obtenida");

        } elseif ($action === 'obtener' && isset($_GET['id'])) {
            $data = $controller->obtenerStaff((int) $_GET['id']);
            $controller->jsonResponse($data, "Staff obtenido");

        } elseif ($action === 'listar') {
            $data = $controller->listarStaff();
            $controller->jsonResponse($data, "Lista de staff obtenida");

        } elseif ($action === 'usuarios') {
            $data = $controller->mostrarUsuario();
            $controller->jsonResponse($data, "Usuarios listados");

        } elseif ($action === 'tipos-rol') {
            $data = $controller->mostrarRol();
            $controller->jsonResponse($data, "Tipos de rol listados");

        } elseif ($action === 'turnos' || $action === 'turnos-lista') {
            $data = $controller->listarTurnos();
            $controller->jsonResponse($data, "Turnos listados");

        } elseif ($action === 'roles-asignados') {
            $id_staff = isset($_GET['id_staff']) ? (int) $_GET['id_staff'] : null;
            $data = $controller->listarRolesAsignados($id_staff);
            $controller->jsonResponse($data, "Roles asignados listados");

        } elseif ($action === 'permisos') {
            $data = $controller->listarPermisos();
            $controller->jsonResponse($data, "Permisos listados");

        } else {
            $controller->jsonError("Acción no válida", 404);
        }

    } elseif ($metodo === 'POST') {

        $datos = json_decode(file_get_contents('php://input'), true);

        if (!$action) {
            $resultado = $controller->registrarStaff($datos['ejecutor'] ?? 0, $datos);
            if ($resultado) {
                $controller->jsonResponse($resultado, "Staff registrado exitosamente", 201);
            } else {
                $controller->jsonError("Error al registrar staff");
            }

        } elseif ($action === 'asignar-rol') {
            if (!isset($datos['id_staff']) || !isset($datos['id_tipo_rol'])) {
                $controller->jsonError("Faltan parámetros requeridos");
            }
            $resultado = $controller->asignarRolAStaff((int) $datos['id_staff'], (int) $datos['id_tipo_rol']);
            if ($resultado) {
                $controller->jsonResponse(null, "Rol asignado exitosamente", 201);
            } else {
                $controller->jsonError("Error al asignar rol");
            }

        } elseif ($action === 'asignar-turno') {
            if (!isset($datos['id_staff']) || !isset($datos['id_turno']) || !isset($datos['fecha'])) {
                $controller->jsonError("Faltan parámetros requeridos");
            }
            $resultado = $controller->asignarTurno((int) $datos['id_staff'], (int) $datos['id_turno'], $datos['fecha']);
            if ($resultado) {
                $controller->jsonResponse($resultado, "Turno asignado exitosamente", 201);
            } else {
                $controller->jsonError("Error al asignar turno");
            }

        } elseif ($action === 'crear-permiso') {
            if (!isset($datos['nombre'])) {
                $controller->jsonError("Falta el nombre del permiso");
            }
            $resultado = $controller->crearPermiso($datos);
            if ($resultado) {
                $controller->jsonResponse($resultado, "Permiso creado exitosamente", 201);
            } else {
                $controller->jsonError("Error al crear permiso");
            }

        } else {
            $controller->jsonError("Acción no válida", 404);
        }

    } elseif ($metodo === 'PUT') {

        $datos = json_decode(file_get_contents('php://input'), true);

        if ($action === 'actualizar') {
            if (!isset($datos['id'])) {
                $controller->jsonError("ID requerido");
            }
            $resultado = $controller->actualizarStaff((int) $datos['id'], $datos);
            if ($resultado) {
                $controller->jsonResponse(null, "Staff actualizado exitosamente");
            } else {
                $controller->jsonError("Error al actualizar staff");
            }
        } else {
            $controller->jsonError("Acción no válida", 404);
        }

    } elseif ($metodo === 'DELETE') {

        if ($action === 'eliminar') {
            if (!isset($_GET['id'])) {
                $controller->jsonError("ID requerido");
            }
            $resultado = $controller->eliminarStaff((int) $_GET['id']);
            if ($resultado) {
                $controller->jsonResponse(null, "Staff eliminado exitosamente");
            } else {
                $controller->jsonError("Error al eliminar staff");
            }

        } elseif ($action === 'revocar-rol') {
            if (!isset($_GET['id_staff']) || !isset($_GET['id_tipo_rol'])) {
                $controller->jsonError("Parámetros requeridos: id_staff, id_tipo_rol");
            }
            $resultado = $controller->revocarRol((int) $_GET['id_staff'], (int) $_GET['id_tipo_rol']);
            if ($resultado) {
                $controller->jsonResponse(null, "Rol revocado exitosamente");
            } else {
                $controller->jsonError("Error al revocar rol");
            }

        } else {
            $controller->jsonError("Acción no válida", 404);
        }

    } else {
        $controller->jsonError("Método no permitido", 405);
    }

} catch (Exception $e) {
    $controller->jsonError("Error del servidor: " . $e->getMessage(), 500);
}

?>
