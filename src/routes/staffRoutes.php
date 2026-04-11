<?php
// ============================================================
// ARCHIVO: staffRoutes.php
// UBICACIÓN: backend-artes-marciales/src/routes/staffRoutes.php
// ============================================================

$db      = (new Database())->getConnection();
$usuario = new Usuario($db);

$method = $_SERVER["REQUEST_METHOD"];
$body   = json_decode(file_get_contents("php://input"), true) ?? [];

// ─── Middleware: valida token ─────────────────────────────────────────────────
function autenticar(Usuario $usuario): array {
    $headers = getallheaders();
    $auth    = $headers["Authorization"] ?? $headers["authorization"] ?? "";

    if (!str_starts_with($auth, "Bearer ")) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Token requerido."]);
        exit();
    }

    $token = trim(substr($auth, 7));
    $user  = $usuario->validarToken($token);

    if (!$user) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Token inválido o sesión expirada."]);
        exit();
    }

    return $user;
}

// Instancia el modelo según tipo: torneo | combate | juez
$staffModel = match($tipo) {
    "combate" => new StaffCombate($db),
    "juez"    => new Juez($db),
    default   => new StaffTorneo($db),
};

autenticar($usuario);

// ─── GET /staff/{tipo}/listar ─────────────────────────────────────────────────
if ($method === "GET" && $action === "listar") {
    http_response_code(200);
    echo json_encode(["success" => true, "data" => $staffModel->obtenerTodos()]);
    exit();
}

// ─── GET /staff/{tipo}/obtener?id=X ───────────────────────────────────────────
if ($method === "GET" && $action === "obtener") {
    $id = (int)($params["id"] ?? 0);

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID inválido."]);
        exit();
    }

    $miembro = $staffModel->obtenerPorId($id);

    if (!$miembro) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Miembro no encontrado."]);
        exit();
    }

    http_response_code(200);
    echo json_encode(["success" => true, "data" => $miembro]);
    exit();
}

// ─── POST /staff/{tipo}/registrar ─────────────────────────────────────────────
// Crea usuario + staff en una sola petición
if ($method === "POST" && $action === "registrar") {

    $requeridos = ["nombre", "turno", "username", "password", "rol"];
    foreach ($requeridos as $campo) {
        if (empty($body[$campo])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "El campo '{$campo}' es requerido."]);
            exit();
        }
    }

    if ($staffModel instanceof StaffTorneo && empty($body["zona"])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "El campo 'zona' es requerido para staff de torneo."]);
        exit();
    }

    // 1. Crear usuario
    $idUsuario = $usuario->crear($body["username"], $body["password"], $body["rol"]);

    if (!$idUsuario) {
        http_response_code(409);
        echo json_encode(["success" => false, "message" => "El username ya existe."]);
        exit();
    }

    // 2. Crear staff
    $datos = [
        "idUsuario" => $idUsuario,
        "nombre"    => $body["nombre"],
        "turno"     => $body["turno"],
    ];

    if ($staffModel instanceof StaffTorneo)  $datos["zona"]      = $body["zona"];
    if ($staffModel instanceof StaffCombate) $datos["idCombate"] = $body["idCombate"] ?? null;

    $staffId = $staffModel->registrar($datos);

    if (!$staffId) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error al registrar el miembro de staff."]);
        exit();
    }

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Miembro de staff registrado correctamente.",
        "data"    => ["staffId" => $staffId, "idUsuario" => $idUsuario]
    ]);
    exit();
}

// ─── PUT /staff/{tipo}/actualizar?id=X ────────────────────────────────────────
if ($method === "PUT" && $action === "actualizar") {
    $id = (int)($params["id"] ?? 0);

    if ($id <= 0 || empty($body)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID y datos son requeridos."]);
        exit();
    }

    $ok = $staffModel->actualizar($id, $body);

    http_response_code($ok ? 200 : 404);
    echo json_encode([
        "success" => $ok,
        "message" => $ok ? "Miembro actualizado correctamente." : "No se pudo actualizar."
    ]);
    exit();
}

// ─── PATCH /staff/{tipo}/turno?id=X ──────────────────────────────────────────
if ($method === "PATCH" && $action === "turno") {
    $id    = (int)($params["id"] ?? 0);
    $turno = trim($body["turno"] ?? "");

    if ($id <= 0 || empty($turno)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID y turno son requeridos."]);
        exit();
    }

    $ok = $staffModel->asignarTurno($id, $turno);

    http_response_code($ok ? 200 : 404);
    echo json_encode([
        "success" => $ok,
        "message" => $ok ? "Turno actualizado." : "Miembro no encontrado."
    ]);
    exit();
}

// ─── DELETE /staff/{tipo}/eliminar?id=X ───────────────────────────────────────
if ($method === "DELETE" && $action === "eliminar") {
    $id = (int)($params["id"] ?? 0);

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID inválido."]);
        exit();
    }

    $ok = $staffModel->eliminar($id);

    http_response_code($ok ? 200 : 404);
    echo json_encode([
        "success" => $ok,
        "message" => $ok ? "Miembro eliminado correctamente." : "Miembro no encontrado."
    ]);
    exit();
}

// ─── GET /staff/combate/por-combate?idCombate=X ───────────────────────────────
if ($method === "GET" && $action === "por-combate") {
    if (!($staffModel instanceof StaffCombate)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Solo disponible para staff de combate."]);
        exit();
    }

    $idCombate = (int)($params["idCombate"] ?? 0);

    if ($idCombate <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "idCombate inválido."]);
        exit();
    }

    http_response_code(200);
    echo json_encode(["success" => true, "data" => $staffModel->obtenerPorCombate($idCombate)]);
    exit();
}

// ─── Ruta no encontrada ───────────────────────────────────────────────────────
http_response_code(404);
echo json_encode(["success" => false, "message" => "Acción de staff no encontrada."]);