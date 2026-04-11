<?php
// ============================================================
// ARCHIVO: staffModels.php
// UBICACIÓN: backend-artes-marciales/src/models/staffModels.php
// ============================================================

// ─── Staff (base abstracta) ───────────────────────────────────────────────────
abstract class Staff {
    protected PDO    $conn;
    protected string $table;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function obtenerTodos(): array {
        $query = "SELECT s.*, u.username, u.rol
                  FROM staff s
                  JOIN usuario u ON u.idUsuario = s.idUsuario
                  WHERE s.estado = 1 AND s.tipo = :tipo
                  ORDER BY s.id DESC";

        $stmt = $this->conn->prepare($query);
        $tipo = $this->getTipo();
        $stmt->bindParam(":tipo", $tipo);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): array|false {
        $query = "SELECT s.*, u.username, u.rol
                  FROM staff s
                  JOIN usuario u ON u.idUsuario = s.idUsuario
                  WHERE s.id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch() ?: false;
    }

    public function eliminar(int $id): bool {
        $query = "UPDATE staff SET estado = 0 WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function asignarTurno(int $id, string $turno): bool {
        $query = "UPDATE staff SET turno = :turno WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":turno", $turno);
        $stmt->bindParam(":id",    $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    abstract protected function getTipo(): string;
    abstract public function registrar(array $datos): int|false;
    abstract public function actualizar(int $id, array $datos): bool;
}


// ─── StaffTorneo ──────────────────────────────────────────────────────────────
// zona: String según diagrama final
class StaffTorneo extends Staff {

    protected function getTipo(): string { return "torneo"; }

    public function registrar(array $datos): int|false {
        try {
            $this->conn->beginTransaction();

            // 1. Inserta en staff base
            $query = "INSERT INTO staff (idUsuario, nombre, turno, estado, tipo)
                      VALUES (:idUsuario, :nombre, :turno, 1, 'torneo')";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $datos["idUsuario"]);
            $stmt->bindParam(":nombre",    $datos["nombre"]);
            $stmt->bindParam(":turno",     $datos["turno"]);
            $stmt->execute();

            $id = (int) $this->conn->lastInsertId();

            // 2. Inserta en staff_torneo con zona
            $query2 = "INSERT INTO staff_torneo (id, zona) VALUES (:id, :zona)";
            $stmt2  = $this->conn->prepare($query2);
            $stmt2->bindParam(":id",   $id);
            $stmt2->bindParam(":zona", $datos["zona"]);
            $stmt2->execute();

            $this->conn->commit();
            return $id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function actualizar(int $id, array $datos): bool {
        $camposStaff   = [];
        $camposTorneo  = [];

        if (isset($datos["nombre"])) $camposStaff[] = "nombre = :nombre";
        if (isset($datos["turno"]))  $camposStaff[] = "turno = :turno";
        if (isset($datos["zona"]))   $camposTorneo[] = "zona = :zona";

        if (empty($camposStaff) && empty($camposTorneo)) return false;

        if (!empty($camposStaff)) {
            $q    = "UPDATE staff SET " . implode(", ", $camposStaff) . " WHERE id = :id";
            $stmt = $this->conn->prepare($q);
            if (isset($datos["nombre"])) $stmt->bindParam(":nombre", $datos["nombre"]);
            if (isset($datos["turno"]))  $stmt->bindParam(":turno",  $datos["turno"]);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        }

        if (!empty($camposTorneo)) {
            $q    = "UPDATE staff_torneo SET " . implode(", ", $camposTorneo) . " WHERE id = :id";
            $stmt = $this->conn->prepare($q);
            if (isset($datos["zona"])) $stmt->bindParam(":zona", $datos["zona"]);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        }

        return true;
    }
}


// ─── StaffCombate ─────────────────────────────────────────────────────────────
class StaffCombate extends Staff {

    protected function getTipo(): string { return "combate"; }

    public function registrar(array $datos): int|false {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO staff (idUsuario, nombre, turno, estado, tipo)
                      VALUES (:idUsuario, :nombre, :turno, 1, 'combate')";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $datos["idUsuario"]);
            $stmt->bindParam(":nombre",    $datos["nombre"]);
            $stmt->bindParam(":turno",     $datos["turno"]);
            $stmt->execute();

            $id        = (int) $this->conn->lastInsertId();
            $idCombate = $datos["idCombate"] ?? null;
            $estado    = "activo";

            $query2 = "INSERT INTO staff_combate (id, idCombate, estado)
                       VALUES (:id, :idCombate, :estado)";
            $stmt2  = $this->conn->prepare($query2);
            $stmt2->bindParam(":id",        $id);
            $stmt2->bindParam(":idCombate", $idCombate);
            $stmt2->bindParam(":estado",    $estado);
            $stmt2->execute();

            $this->conn->commit();
            return $id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function actualizar(int $id, array $datos): bool {
        if (isset($datos["nombre"]) || isset($datos["turno"])) {
            $campos = [];
            if (isset($datos["nombre"])) $campos[] = "nombre = :nombre";
            if (isset($datos["turno"]))  $campos[] = "turno = :turno";

            $q    = "UPDATE staff SET " . implode(", ", $campos) . " WHERE id = :id";
            $stmt = $this->conn->prepare($q);
            if (isset($datos["nombre"])) $stmt->bindParam(":nombre", $datos["nombre"]);
            if (isset($datos["turno"]))  $stmt->bindParam(":turno",  $datos["turno"]);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        }

        if (isset($datos["idCombate"]) || isset($datos["estado"])) {
            $campos = [];
            if (isset($datos["idCombate"])) $campos[] = "idCombate = :idCombate";
            if (isset($datos["estado"]))    $campos[] = "estado = :estado";

            $q    = "UPDATE staff_combate SET " . implode(", ", $campos) . " WHERE id = :id";
            $stmt = $this->conn->prepare($q);
            if (isset($datos["idCombate"])) $stmt->bindParam(":idCombate", $datos["idCombate"]);
            if (isset($datos["estado"]))    $stmt->bindParam(":estado",    $datos["estado"]);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        }

        return true;
    }

    public function obtenerPorCombate(int $idCombate): array {
        $query = "SELECT s.*, u.username, sc.estado AS estadoCombate
                  FROM staff s
                  JOIN staff_combate sc ON sc.id = s.id
                  JOIN usuario u ON u.idUsuario = s.idUsuario
                  WHERE sc.idCombate = :idCombate AND sc.estado = 'activo'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idCombate", $idCombate);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}


// ─── Juez (extiende Staff) ────────────────────────────────────────────────────
class Juez extends Staff {

    protected function getTipo(): string { return "juez"; }

    public function registrar(array $datos): int|false {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO staff (idUsuario, nombre, turno, estado, tipo)
                      VALUES (:idUsuario, :nombre, :turno, 1, 'juez')";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":idUsuario", $datos["idUsuario"]);
            $stmt->bindParam(":nombre",    $datos["nombre"]);
            $stmt->bindParam(":turno",     $datos["turno"]);
            $stmt->execute();

            $id = (int) $this->conn->lastInsertId();

            $query2 = "INSERT INTO juez (id) VALUES (:id)";
            $stmt2  = $this->conn->prepare($query2);
            $stmt2->bindParam(":id", $id);
            $stmt2->execute();

            $this->conn->commit();
            return $id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function actualizar(int $id, array $datos): bool {
        $campos = [];
        if (isset($datos["nombre"])) $campos[] = "nombre = :nombre";
        if (isset($datos["turno"]))  $campos[] = "turno = :turno";

        if (empty($campos)) return false;

        $q    = "UPDATE staff SET " . implode(", ", $campos) . " WHERE id = :id";
        $stmt = $this->conn->prepare($q);
        if (isset($datos["nombre"])) $stmt->bindParam(":nombre", $datos["nombre"]);
        if (isset($datos["turno"]))  $stmt->bindParam(":turno",  $datos["turno"]);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}