<?php
// ============================================================
// ARCHIVO: authRoutes.php
// UBICACIÓN: backend-artes-marciales/src/routes/authRoutes.php
// ============================================================

$db      = (new Database())->getConnection();
$usuario = new Usuario($db);

$method = $_SERVER["REQUEST_METHOD"];
$body   = json_decode(file_get_contents("php://input"), true) ?? [];

function getTokenFromHeader(): ?string {
    $headers = getallheaders();
    $auth    = $headers["Authorization"] ?? $headers["authorization"] ?? "";
    if (str_starts_with($auth, "Bearer ")) {
        return trim(substr($auth, 7));
    }
    return null;
}

// ─── POST /auth/login ─────────────────────────────────────────────────────────
if ($method === "POST" && $action === "login") {

    $username = trim($body["username"] ?? "");
    $password = trim($body["password"] ?? "");

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Username y password son requeridos."]);
        exit();
    }

    $user = $usuario->login($username, $password);

    if (!$user) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Credenciales inválidas o usuario inactivo."]);
        exit();
    }

    $token = $usuario->crearSesion($user["idUsuario"]);

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Login exitoso.",
        "data"    => [
            "token"   => $token,
            "usuario" => [
                "idUsuario" => $user["idUsuario"],
                "username"  => $user["username"],
                "rol"       => $user["rol"]
            ]
        ]
    ]);
    exit();
}

// ─── POST /auth/logout ────────────────────────────────────────────────────────
if ($method === "POST" && $action === "logout") {

    $token = getTokenFromHeader();

    if (!$token) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Token requerido."]);
        exit();
    }

    $cerrado = $usuario->cerrarSesion($token);

    if (!$cerrado) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Sesión no encontrada o ya cerrada."]);
        exit();
    }

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Sesión cerrada correctamente."]);
    exit();
}

// ─── GET /auth/me ─────────────────────────────────────────────────────────────
if ($method === "GET" && $action === "me") {

    $token = getTokenFromHeader();

    if (!$token) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Token requerido."]);
        exit();
    }

    $user = $usuario->validarToken($token);

    if (!$user) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Token inválido o sesión expirada."]);
        exit();
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data"    => [
            "idUsuario" => $user["idUsuario"],
            "username"  => $user["username"],
            "rol"       => $user["rol"]
        ]
    ]);
    exit();
}

// ─── Ruta no encontrada ───────────────────────────────────────────────────────
http_response_code(404);
echo json_encode(["success" => false, "message" => "Acción de auth no encontrada."]);