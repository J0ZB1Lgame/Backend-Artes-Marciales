<?php
// ============================================================
// ARCHIVO: index.php
// UBICACIÓN: backend-artes-marciales/index.php
// ============================================================

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit();
}

require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/src/models/usuarioModels.php";
require_once __DIR__ . "/src/models/staffModels.php";

$uri    = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$uri    = trim($uri, "/");
$partes = explode("/", $uri);

// Elimina el prefijo de la carpeta del proyecto
// hasta encontrar "auth" o "staff"
while (!empty($partes) && $partes[0] !== "auth" && $partes[0] !== "staff") {
    array_shift($partes);
}

$modulo = $partes[0] ?? "";
$tipo   = $partes[1] ?? "";
$action = $partes[2] ?? "";
$params = $_GET;

switch ($modulo) {

    case "auth":
        $action = $tipo;
        require __DIR__ . "/src/routes/authRoutes.php";
        break;

    case "staff":
        if (!in_array($tipo, ["torneo", "combate", "juez"])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Tipo de staff inválido. Use 'torneo', 'combate' o 'juez'."
            ]);
            exit();
        }
        require __DIR__ . "/src/routes/staffRoutes.php";
        break;

    default:
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Módulo '{$modulo}' no encontrado."
        ]);
        exit();
}