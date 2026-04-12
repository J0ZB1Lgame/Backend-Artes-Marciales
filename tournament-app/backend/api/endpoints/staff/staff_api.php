<?php
// ================== HEADERS API ==================

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// ================== IMPORTS ==================

require_once __DIR__ . '/../controllers/staff/StaffController.php';

// ================== ROUTER ==================

$controller = new StaffController();
$metodo = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($metodo === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {

    if ($metodo === 'GET') {

        if ($action === 'listar') {
            $data = $controller->listarStaff();
            $controller->jsonResponse($data, "Lista de staff obtenida");

        } elseif ($action === 'obtener' && isset($_GET['id'])) {
            $data = $controller->obtenerStaff($_GET['id']);
            $controller->jsonResponse($data, "Staff obtenido");

        } elseif ($action === 'turnos') {
            $data = $controller->listarAsignacionesTurno();
            $controller->jsonResponse($data, "Turnos listados");

        } elseif ($action === 'usuarios') {
            $data = $controller->listarUsuarios();
            $controller->jsonResponse($data, "Usuarios listados");

        } elseif ($action === 'turnos-lista') {
            $data = $controller->listarTurnos();
            $controller->jsonResponse($data, "Turnos listados");

        } elseif ($action === 'tipos-rol') {
            $data = $controller->listarTiposRol();
            $controller->jsonResponse($data, "Tipos de rol listados");

        } elseif ($action === 'roles-asignados') {
            $id_staff = $_GET['id_staff'] ?? null;
            $data = $controller->listarRolesAsignados($id_staff);
            $controller->jsonResponse($data, "Roles asignados listados");

        } else {
            $controller->jsonError("Acción no válida", 404);
        }

    } elseif ($metodo === 'POST') {

        $datos = json_decode(file_get_contents('php://input'), true);

        if ($action === 'registrar') {
            $resultado = $controller->registrarStaff($datos);
            if ($resultado) {
                $controller->jsonResponse($resultado, "Staff registrado exitosamente", 201);
            } else {
                $controller->jsonError("Error al registrar staff");
            }

        } elseif ($action === 'asignar-rol') {
            if (!isset($datos['id_staff']) || !isset($datos['id_tipo_rol'])) {
                $controller->jsonError("Faltan parámetros requeridos");
            }
            $resultado = $controller->asignarRol($datos['id_staff'], $datos['id_tipo_rol']);
            if ($resultado) {
                $controller->jsonResponse(null, "Rol asignado exitosamente", 201);
            } else {
                $controller->jsonError("Error al asignar rol");
            }

        } elseif ($action === 'asignar-turno') {
            if (!isset($datos['id_staff']) || !isset($datos['id_turno']) || !isset($datos['fecha'])) {
                $controller->jsonError("Faltan parámetros requeridos");
            }
            $resultado = $controller->asignarTurno($datos['id_staff'], $datos['id_turno'], $datos['fecha']);
            if ($resultado) {
                $controller->jsonResponse($resultado, "Turno asignado exitosamente", 201);
            } else {
                $controller->jsonError("Error al asignar turno");
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
            $resultado = $controller->actualizarStaff($datos['id'], $datos);
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
            $resultado = $controller->eliminarStaff($_GET['id']);
            if ($resultado) {
                $controller->jsonResponse(null, "Staff eliminado exitosamente");
            } else {
                $controller->jsonError("Error al eliminar staff");
            }

        } elseif ($action === 'revocar-rol') {
            if (!isset($_GET['id_staff']) || !isset($_GET['id_tipo_rol'])) {
                $controller->jsonError("Parámetros requeridos: id_staff, id_tipo_rol");
            }
            $resultado = $controller->revocarRol($_GET['id_staff'], $_GET['id_tipo_rol']);
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
