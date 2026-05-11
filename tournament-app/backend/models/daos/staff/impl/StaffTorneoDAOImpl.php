<?php

require_once __DIR__ . '/../../../../core/database.php';
require_once __DIR__ . '/../../base/BaseDAO.php';
require_once __DIR__ . '/../interfaces/IStaffTorneoDAO.php';

class StaffTorneoDAOImpl extends BaseDAO implements IStaffTorneoDAO {

    private $table = "staff_torneo";

    public function __construct() {
        $database = Database::getInstance();
        parent::__construct($database->getConnection());
    }

    private function selectBase(): string {
        return "SELECT
                    st.id_staff,
                    st.nombre,
                    st.apellido,
                    st.documento     AS numero_documento,
                    st.telefono,
                    st.email,
                    st.estado,
                    st.id_rol,
                    st.id_zona,
                    r.nombre         AS cargo,
                    z.nombre         AS turno
                FROM {$this->table} st
                LEFT JOIN roles r ON st.id_rol  = r.id_rol
                LEFT JOIN zonas z ON st.id_zona = z.id_zona";
    }

    private function rolIdDesdeNombre(?string $cargo): ?int {
        if (!$cargo) return null;
        $row = $this->fetch("SELECT id_rol FROM roles WHERE nombre = ? LIMIT 1", [$cargo], "s");
        return $row ? (int)$row['id_rol'] : null;
    }

    private function zonaIdDesdeNombre(?string $turno): ?int {
        if (!$turno) return null;
        $row = $this->fetch("SELECT id_zona FROM zonas WHERE nombre = ? LIMIT 1", [$turno], "s");
        return $row ? (int)$row['id_zona'] : null;
    }

    public function getAll() {
        return $this->fetchAll($this->selectBase() . " ORDER BY st.id_staff DESC");
    }

    public function getById($id) {
        return $this->fetch(
            $this->selectBase() . " WHERE st.id_staff = ? LIMIT 1",
            [$id],
            "i"
        );
    }

    public function create($data) {
        $id_rol  = isset($data['id_rol'])  ? (int)$data['id_rol']
                                           : $this->rolIdDesdeNombre($data['cargo']  ?? null);
        $id_zona = isset($data['id_zona']) ? (int)$data['id_zona']
                                           : $this->zonaIdDesdeNombre($data['turno'] ?? null);

        $sql = "INSERT INTO {$this->table}
                    (nombre, apellido, documento, telefono, email, id_rol, id_zona, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->execute($sql, [
            $data['nombre']                                     ?? '',
            $data['apellido']                                   ?? null,
            $data['numero_documento'] ?? $data['documento']     ?? null,
            $data['telefono']                                   ?? null,
            $data['email']                                      ?? null,
            $id_rol,
            $id_zona,
            $data['estado']                                     ?? 'activo',
        ], "sssssiis");
    }

    public function update($id, $data) {
        $id_rol  = isset($data['id_rol'])  ? (int)$data['id_rol']
                                           : $this->rolIdDesdeNombre($data['cargo']  ?? null);
        $id_zona = isset($data['id_zona']) ? (int)$data['id_zona']
                                           : $this->zonaIdDesdeNombre($data['turno'] ?? null);

        $sql = "UPDATE {$this->table}
                SET nombre = ?, apellido = ?, documento = ?, telefono = ?,
                    email = ?, id_rol = ?, id_zona = ?, estado = ?
                WHERE id_staff = ?";

        return $this->execute($sql, [
            $data['nombre']                                     ?? '',
            $data['apellido']                                   ?? null,
            $data['numero_documento'] ?? $data['documento']     ?? null,
            $data['telefono']                                   ?? null,
            $data['email']                                      ?? null,
            $id_rol,
            $id_zona,
            $data['estado']                                     ?? 'activo',
            $id,
        ], "sssssiisi");
    }

    public function delete($id) {
        return $this->execute(
            "DELETE FROM {$this->table} WHERE id_staff = ?",
            [$id],
            "i"
        );
    }

    public function search($search) {
        $like = "%{$search}%";
        $sql  = $this->selectBase() . "
                WHERE st.nombre   LIKE ? OR st.apellido  LIKE ?
                   OR st.documento LIKE ? OR r.nombre    LIKE ? OR z.nombre LIKE ?
                ORDER BY st.id_staff DESC";
        return $this->fetchAll($sql, [$like, $like, $like, $like, $like], "sssss");
    }

    public function countAll() {
        return $this->fetch("SELECT COUNT(*) AS total FROM {$this->table}");
    }

    // ── Métodos de la interfaz legacy ────────────────────────────────────
    public function crear($staffTorneo)  { return $this->create(['nombre' => $staffTorneo->getNombre()]); }
    public function obtenerPorId($id)    { return $this->getById($id); }
    public function actualizar($s)       { return false; }
    public function eliminarPorId($id)   { return $this->delete($id); }
    public function listarTodos()        { return $this->getAll(); }
}

?>
