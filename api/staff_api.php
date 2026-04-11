<?php
// ================== HEADERS API ==================

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// ================== IMPORTS ==================

require_once __DIR__ . '/../Controlador/StaffController.php';

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
            $data = $controller->listarRolesAsignados();
            $controller->jsonResponse($data, "Roles asignados listados");

        } else {
            throw new Exception("Acción GET no válida");
        }

    } elseif ($metodo === 'POST') {

        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);

        if ($action === 'registrar') {
            $id = $controller->registrarStaff(
                $datos['id_usuario'],
                $datos['nombre'],
                $datos['apellido'],
                $datos['tipo_documento'] ?? '',
                $datos['numero_documento'] ?? '',
                $datos['telefono'] ?? '',
                $datos['email'] ?? ''
            );
            $controller->jsonResponse($id, "Staff registrado correctamente", 201);

        } elseif ($action === 'asignar-rol') {
            $controller->asignarRol(
                $datos['id_staff'],
                $datos['id_tipo_rol']
            );
            $controller->jsonResponse(null, "Rol asignado correctamente");

        } elseif ($action === 'asignar-turno') {
            $controller->asignarTurno(
                $datos['id_staff'],
                $datos['id_turno'],
                $datos['fecha']
            );
            $controller->jsonResponse(null, "Turno asignado correctamente");

        } else {
            throw new Exception("Acción POST no válida");
        }

    } elseif ($metodo === 'PUT') {

        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);

        if ($action === 'actualizar') {
            $controller->actualizarStaff(
                $datos['id'],
                $datos['id_usuario'],
                $datos['nombre'],
                $datos['apellido'],
                $datos['tipo_documento'] ?? '',
                $datos['numero_documento'] ?? '',
                $datos['telefono'] ?? '',
                $datos['email'] ?? '',
                $datos['estado'] ?? ''
            );
            $controller->jsonResponse(null, "Staff actualizado correctamente");

        } else {
            throw new Exception("Acción PUT no válida");
        }

    } elseif ($metodo === 'DELETE') {

        if ($action === 'eliminar' && isset($_GET['id'])) {
            $controller->eliminarStaff($_GET['id']);
            $controller->jsonResponse(null, "Staff eliminado");

        } elseif ($action === 'revocar-rol' && isset($_GET['id_staff']) && isset($_GET['id_tipo_rol'])) {
            $controller->revocarRol($_GET['id_staff'], $_GET['id_tipo_rol']);
            $controller->jsonResponse(null, "Rol revocado correctamente");

        } else {
            throw new Exception("Acción DELETE no válida");
        }

    } else {
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
    }

} catch (Exception $e) {
    $controller->jsonError($e->getMessage());
}
?>
