<?php
// ============================================================
// ARCHIVO: usuarioModels.php
// UBICACIÓN: backend-artes-marciales/src/models/usuarioModels.php
// ============================================================

class Usuario {
    private PDO $conn;
    private string $table = "usuario";

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    // ─── LOGIN ────────────────────────────────────────────────────────────────

    public function login(string $username, string $password): array|false {
    $query = "SELECT idUsuario, username, password, rol, estado
              FROM {$this->table}
              WHERE username = :username
              LIMIT 1";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->execute();

    $user = $stmt->fetch();

    if (!$user) return false;
    if (!$user["estado"]) return false;

    // ✅ Comparación directa sin cifrado
    if ($user["password"] !== $password) return false;

    return $user;
}

    // ─── SESIÓN ───────────────────────────────────────────────────────────────

    public function crearSesion(int $idUsuario): string {
        $token      = bin2hex(random_bytes(32));
        $fechaInicio = date("Y-m-d H:i:s");

        $query = "INSERT INTO sesion (idUsuario, token, fechaInicio, usuarioActivo)
                  VALUES (:idUsuario, :token, :fechaInicio, 1)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idUsuario",   $idUsuario);
        $stmt->bindParam(":token",       $token);
        $stmt->bindParam(":fechaInicio", $fechaInicio);
        $stmt->execute();

        return $token;
    }

    public function cerrarSesion(string $token): bool {
        $fechaFin = date("Y-m-d H:i:s");

        $query = "UPDATE sesion
                  SET usuarioActivo = 0, fechaFin = :fechaFin
                  WHERE token = :token AND usuarioActivo = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":fechaFin", $fechaFin);
        $stmt->bindParam(":token",    $token);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function validarToken(string $token): array|false {
        $query = "SELECT u.idUsuario, u.username, u.rol, u.estado
                  FROM sesion s
                  JOIN {$this->table} u ON u.idUsuario = s.idUsuario
                  WHERE s.token = :token AND s.usuarioActivo = 1
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        return $stmt->fetch() ?: false;
    }

    // ─── CRUD ─────────────────────────────────────────────────────────────────

    public function crear(string $username, string $password, string $rol): int|false {
    $check = $this->conn->prepare(
        "SELECT idUsuario FROM {$this->table} WHERE username = :username LIMIT 1"
    );
    $check->bindParam(":username", $username);
    $check->execute();
    if ($check->fetch()) return false;

    // ✅ Sin hash
    $estado = true;

    $query = "INSERT INTO {$this->table} (username, password, rol, estado)
              VALUES (:username, :password, :rol, :estado)";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":password", $password);
    $stmt->bindParam(":rol",      $rol);
    $stmt->bindParam(":estado",   $estado);
    $stmt->execute();

    return (int) $this->conn->lastInsertId();
}

    public function cambiarPassword(int $idUsuario, string $nuevaPassword): bool {
        $hash  = password_hash($nuevaPassword, PASSWORD_BCRYPT);
        $query = "UPDATE {$this->table} SET password = :password WHERE idUsuario = :idUsuario";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":password",  $hash);
        $stmt->bindParam(":idUsuario", $idUsuario);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function actualizarEstado(int $idUsuario, bool $estado): bool {
        $query = "UPDATE {$this->table} SET estado = :estado WHERE idUsuario = :idUsuario";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado",    $estado);
        $stmt->bindParam(":idUsuario", $idUsuario);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}