<?php

require_once __DIR__ . '/../../../../backend/config/conexion.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/StaffTorneoDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/ZonaDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/RolDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/daos/staff/impl/LuchadorDAOImpl.php';
require_once __DIR__ . '/../../../../backend/models/entities/staff/StaffTorneo.php';
require_once __DIR__ . '/../../../../backend/models/entities/staff/Luchador.php';
require_once __DIR__ . '/../../../../backend/models/entities/staff/Zona.php';
require_once __DIR__ . '/../../../../backend/models/entities/staff/Rol.php';

class StaffTorneoController {
    private $staffTorneoDAO;
    private $zonaDAO;
    private $rolDAO;
    private $luchadorDAO;
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->staffTorneoDAO = new StaffTorneoDAOImpl();
        $this->zonaDAO = new ZonaDAOImpl();
        $this->rolDAO = new RolDAOImpl();
        $this->luchadorDAO = new LuchadorDAOImpl();
    }

    public function jsonResponse($data, $message = "OK", $status = 200) {
        http_response_code($status);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public function jsonError($message, $status = 400) {
        http_response_code($status);
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }

    // ======= Staff Torneo =======
    public function registrarStaff($ejecutor, $datosStaff) {
        $staffTorneo = new StaffTorneo(null, $datosStaff['nombre'] ?? '');
        $resultado = $this->staffTorneoDAO->crear($staffTorneo);
        // Log removed
        return $resultado;
    }

    public function listarStaffPorTorneo($ejecutor, $torneo) {
        $staffList = $this->staffTorneoDAO->listarTodos();
        $response = [];

        foreach ($staffList as $staffTorneo) {
            if ($staffTorneo->getNombre() === $torneo) {
                $response[] = [
                    'id_staff_torneo' => $staffTorneo->getIdStaffTorneo(),
                    'nombre' => $staffTorneo->getNombre()
                ];
            }
        }

        return $response;
    }

    public function asignarStaffATorneo($ejecutor, $staff, $torneo) {
        $staffTorneos = $this->staffTorneoDAO->listarTodos();
        foreach ($staffTorneos as $registro) {
            if ($registro->getNombre() === $torneo) {
                $this->conn->query("UPDATE staff_torneo SET id_staff = {$staff} WHERE id_staff_torneo = {$registro->getIdStaffTorneo()}");
                // Log removed
                return true;
            }
        }
        $sql = "INSERT INTO staff_torneo (id_staff, nombre) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $staff, $torneo);
        $success = $stmt->execute();
        // Log removed
        return $success;
    }

    public function asignarZona($ejecutor, $staff, string $zona) {
        $zonas = $this->zonaDAO->listarTodos();
        foreach ($zonas as $registro) {
            if ($registro->getNombre() === $zona) {
                // Log removed
                return true;
            }
        }
        return false;
    }

    public function asignarRol($ejecutor, $staff, $rol) {
        // Log removed
        return true;
    }

    public function modificarRolEnTorneo($ejecutor, $staff, $nuevoRol) {
        $this->reportarLog($ejecutor, "Modificar rol en torneo para staff {$staff} a {$nuevoRol}");
        return true;
    }

    public function modificarZonaEnTorneo($ejecutor, $staff, string $nuevaZona) {
        // Log removed
        return true;
    }

    // ======= Rol =======
    public function crearRol($rol) {
        if (is_array($rol)) {
            $rol = new Rol(null, $rol['nombre'] ?? '', $rol['descripcion'] ?? '');
        }
        return $this->rolDAO->crear($rol);
    }

    public function buscarRol(int $id) {
        return $this->rolDAO->obtenerPorId($id);
    }

    public function mostrarRol(): array {
        $roles = $this->rolDAO->listarTodos();
        $response = [];
        foreach ($roles as $rol) {
            $response[] = [
                'id_rol' => $rol->getIdRol(),
                'nombre' => $rol->getNombre(),
                'descripcion' => $rol->getDescripcion()
            ];
        }
        return $response;
    }

    public function actualizarInformacionRol($rol) {
        if (is_array($rol)) {
            $rolActual = $this->rolDAO->obtenerPorId($rol['id_rol'] ?? 0);
            if (!$rolActual) {
                return false;
            }
            if (isset($rol['nombre'])) {
                $rolActual->setNombre($rol['nombre']);
            }
            if (isset($rol['descripcion'])) {
                $rolActual->setDescripcion($rol['descripcion']);
            }
            return $this->rolDAO->actualizar($rolActual);
        }
        return $this->rolDAO->actualizar($rol);
    }

    public function eliminarRol(int $id): bool {
        return $this->rolDAO->eliminarPorId($id);
    }

    // ======= Zona =======
    public function crearZona($zona) {
        if (is_array($zona)) {
            $zona = new Zona(null, $zona['nombre'] ?? '', $zona['descripcion'] ?? '');
        }
        return $this->zonaDAO->crear($zona);
    }

    public function buscarZona(int $id) {
        return $this->zonaDAO->obtenerPorId($id);
    }

    public function mostrarZona(): array {
        $zonas = $this->zonaDAO->listarTodos();
        $response = [];
        foreach ($zonas as $zona) {
            $response[] = [
                'id_zona' => $zona->getIdZona(),
                'nombre' => $zona->getNombre(),
                'descripcion' => $zona->getDescripcion()
            ];
        }
        return $response;
    }

    public function actualizarInformacionZona($zona) {
        if (is_array($zona)) {
            $zonaActual = $this->zonaDAO->obtenerPorId($zona['id_zona'] ?? 0);
            if (!$zonaActual) {
                return false;
            }
            if (isset($zona['nombre'])) {
                $zonaActual->setNombre($zona['nombre']);
            }
            if (isset($zona['descripcion'])) {
                $zonaActual->setDescripcion($zona['descripcion']);
            }
            return $this->zonaDAO->actualizar($zonaActual);
        }
        return $this->zonaDAO->actualizar($zona);
    }

    public function eliminarZona(int $id): bool {
        return $this->zonaDAO->eliminarPorId($id);
    }

    // ======= Luchador =======
    public function crearLuchador($luchador) {
        if (is_array($luchador)) {
            $luchador = new Luchador(
                null,
                $luchador['nombre'] ?? '',
                $luchador['especie'] ?? '',
                isset($luchador['nivelDePoderKi']) ? (float) $luchador['nivelDePoderKi'] : 0.0,
                $luchador['origen'] ?? '',
                isset($luchador['estado']) ? (bool) $luchador['estado'] : true
            );
        }
        return $this->luchadorDAO->crear($luchador);
    }

    public function buscarLuchador(string $nombre) {
        return $this->luchadorDAO->buscarPorNombre($nombre);
    }

    public function mostrarLuchadores(): array {
        $luchadores = $this->luchadorDAO->listarTodos();
        $response = [];
        foreach ($luchadores as $luchador) {
            $response[] = [
                'id_luchador' => $luchador->getIdLuchador(),
                'nombre' => $luchador->getNombre(),
                'especie' => $luchador->getEspecie(),
                'nivelDePoderKi' => $luchador->getNivelDePoderKi(),
                'origen' => $luchador->getOrigen(),
                'estado' => $luchador->getEstado()
            ];
        }
        return $response;
    }

    public function actualizarInformacionLuchador($luchador) {
        if (is_array($luchador)) {
            $actual = $this->luchadorDAO->buscarPorNombre($luchador['nombre'] ?? '');
            if (!$actual) {
                return false;
            }
            if (isset($luchador['especie'])) {
                $actual->setEspecie($luchador['especie']);
            }
            if (isset($luchador['nivelDePoderKi'])) {
                $actual->setNivelDePoderKi((float) $luchador['nivelDePoderKi']);
            }
            if (isset($luchador['origen'])) {
                $actual->setOrigen($luchador['origen']);
            }
            if (isset($luchador['estado'])) {
                $actual->setEstado((bool) $luchador['estado']);
            }
            return $this->luchadorDAO->actualizar($actual);
        }
        return $this->luchadorDAO->actualizar($luchador);
    }

    public function verEstadoLuchador(int $id) {
        $luchador = $this->luchadorDAO->obtenerPorId($id);
        return $luchador ? $luchador->getEstado() : null;
    }

    public function eliminarLuchador(int $id): bool {
        return $this->luchadorDAO->eliminarPorId($id);
    }

    public function modificarRolEnTorneo($ejecutor, $staff, $nuevoRol) {
        // Log removed
        return true;
    }

    public function modificarZonaEnTorneo($ejecutor, $staff, string $nuevaZona) {
        // Log removed
        return true;
    }

    public function eliminarStaffDeTorneo($ejecutor, $staff, $torneo) {
        $sql = "DELETE FROM staff_torneo WHERE id_staff = ? AND nombre = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $staff, $torneo);
        $success = $stmt->execute();
        // Log removed
        return $success;
    }

    public function listarStaff(): array {
        $staffList = $this->staffTorneoDAO->listarTodos();
        $response = [];

        foreach ($staffList as $staffTorneo) {
            $response[] = [
                'id_staff' => $staffTorneo->getIdStaffTorneo(),
                'nombre' => $staffTorneo->getNombre()
            ];
        }

        return $response;
    }

    public function obtenerStaff(int $id) {
        $staff = $this->staffTorneoDAO->obtenerPorId($id);
        if (!$staff) {
            return null;
        }
        return [
            'id_staff' => $staff->getIdStaffTorneo(),
            'nombre' => $staff->getNombre()
        ];
    }

    public function actualizarStaff(int $id, array $datos) {
        $staff = $this->staffTorneoDAO->obtenerPorId($id);
        if (!$staff) {
            return false;
        }
        if (isset($datos['nombre'])) {
            $staff->setNombre($datos['nombre']);
        }
        return $this->staffTorneoDAO->actualizar($staff);
    }

    public function eliminarStaff(int $id): bool {
        return $this->staffTorneoDAO->eliminarPorId($id);
    }

    public function listarTurnos(): array {
        return [];
    }

    public function asignarRolAStaff(int $id_staff, int $id_tipo_rol): bool {
        return $this->asignarRol($id_staff, $id_staff, $id_tipo_rol);
    }

    public function asignarTurno(int $id_staff, int $id_turno, string $fecha): bool {
        return false;
    }

    public function revocarRol(int $id_staff, int $id_tipo_rol): bool {
        return false;
    }

    // ======= Usuario =======
    public function crearUsuario($usuario) {
        if (is_array($usuario)) {
            $usuario = new Usuario(
                null,
                $usuario['username'] ?? '',
                $usuario['password'] ?? '',
                $usuario['rol'] ?? 'staff',
                isset($usuario['estado']) ? (bool) $usuario['estado'] : true
            );
        }
        return $this->usuarioDAO->crear($usuario);
    }

    public function buscarUsuario(int $id) {
        return $this->usuarioDAO->obtenerPorId($id);
    }

    public function mostrarUsuario(): array {
        $usuarios = $this->usuarioDAO->listarTodos();
        $response = [];

        foreach ($usuarios as $usuario) {
            $response[] = [
                'id_usuario' => $usuario->getIdUsuario(),
                'username' => $usuario->getUsername(),
                'rol' => $usuario->getRol(),
                'estado' => $usuario->getEstado()
            ];
        }

        return $response;
    }

    public function actualizarInformacionUsuario($usuario) {
        if (is_array($usuario)) {
            $usuarioActual = $this->usuarioDAO->obtenerPorId($usuario['id_usuario'] ?? 0);
            if (!$usuarioActual) {
                return false;
            }
            if (isset($usuario['username'])) {
                $usuarioActual->setUsername($usuario['username']);
            }
            if (isset($usuario['password'])) {
                $usuarioActual->setPassword($usuario['password']);
            }
            if (isset($usuario['rol'])) {
                $usuarioActual->setRol($usuario['rol']);
            }
            if (isset($usuario['estado'])) {
                $usuarioActual->setEstado((bool) $usuario['estado']);
            }
            return $this->usuarioDAO->actualizar($usuarioActual);
        }
        return $this->usuarioDAO->actualizar($usuario);
    }

    public function actualizarUsername(int $id, string $username): bool {
        $usuario = $this->usuarioDAO->obtenerPorId($id);
        if (!$usuario) {
            return false;
        }
        $usuario->setUsername($username);
        return $this->usuarioDAO->actualizar($usuario);
    }

    public function actualizarPassword(int $id, string $password): bool {
        $usuario = $this->usuarioDAO->obtenerPorId($id);
        if (!$usuario) {
            return false;
        }
        $usuario->setPassword($password);
        return $this->usuarioDAO->actualizar($usuario);
    }

    public function verEstadoUsuario(int $id) {
        $usuario = $this->usuarioDAO->obtenerPorId($id);
        return $usuario ? $usuario->getEstado() : null;
    }

    public function eliminarUsuario(int $id): bool {
        return $this->usuarioDAO->eliminarPorId($id);
    }

    // ======= Sesión =======
    public function iniciarSesion(string $username, string $password): bool {
        $usuario = $this->usuarioDAO->obtenerPorUsername($username);
        if (!$usuario || $usuario->getPassword() !== $password || !$usuario->getEstado()) {
            return false;
        }
        $this->sesionActual = new Sesion(null, date('Y-m-d H:i:s'), '', $usuario);
        $this->sesionDAO->crearSesion($this->sesionActual);
        return true;
    }

    public function buscarSesion(int $id) {
        return $this->sesionDAO->buscarSesion($id);
    }

    public function cerrarSesion(): void {
        if ($this->sesionActual && $this->sesionActual->getIdSesion()) {
            $this->sesionDAO->cerrarSesion($this->sesionActual->getIdSesion());
        }
    }

    public function validarPermisos(string $rolRequerido): bool {
        $usuario = $this->getUsuarioLogueado();
        if (!$usuario) {
            return false;
        }
        return $usuario->getRol() === $rolRequerido || $usuario->getRol() === 'Administrador';
    }

    public function getUsuarioLogueado() {
        return $this->sesionActual ? $this->sesionActual->getUsuarioActivo() : null;
    }

    // ======= Logs =======
    public function registrarEvento(int $id): void {
        $evento = new Log(null, "Evento registrado por usuario {$id}", date('Y-m-d H:i:s'), $id);
        $this->logDAO->crearEvento($evento);
    }

    public function consultarLog(): array {
        $logs = $this->logDAO->consultarHistorial();
        $response = [];
        foreach ($logs as $log) {
            $response[] = [
                'id_log' => $log->getIdLog(),
                'accion' => $log->getAccion(),
                'fecha' => $log->getFecha(),
                'id_usuario' => $log->getIdUsuario()
            ];
        }
        return $response;
    }

    // ======= Rol =======
    public function crearRol($rol) {
        if (is_array($rol)) {
            $rol = new Rol(null, $rol['nombre'] ?? '', $rol['descripcion'] ?? '');
        }
        return $this->rolDAO->crear($rol);
    }

    public function buscarRol(int $id) {
        return $this->rolDAO->obtenerPorId($id);
    }

    public function mostrarRol(): array {
        $roles = $this->rolDAO->listarTodos();
        $response = [];
        foreach ($roles as $rol) {
            $response[] = [
                'id_rol' => $rol->getIdRol(),
                'nombre' => $rol->getNombre(),
                'descripcion' => $rol->getDescripcion()
            ];
        }
        return $response;
    }

    public function actualizarInformacionRol($rol) {
        if (is_array($rol)) {
            $rolActual = $this->rolDAO->obtenerPorId($rol['id_rol'] ?? 0);
            if (!$rolActual) {
                return false;
            }
            if (isset($rol['nombre'])) {
                $rolActual->setNombre($rol['nombre']);
            }
            if (isset($rol['descripcion'])) {
                $rolActual->setDescripcion($rol['descripcion']);
            }
            return $this->rolDAO->actualizar($rolActual);
        }
        return $this->rolDAO->actualizar($rol);
    }

    public function eliminarRol(int $id): bool {
        return $this->rolDAO->eliminarPorId($id);
    }

    // ======= Zona =======
    public function crearZona($zona) {
        if (is_array($zona)) {
            $zona = new Zona(null, $zona['nombre'] ?? '', $zona['descripcion'] ?? '');
        }
        return $this->zonaDAO->crear($zona);
    }

    public function buscarZona(int $id) {
        return $this->zonaDAO->obtenerPorId($id);
    }

    public function mostrarZona(): array {
        $zonas = $this->zonaDAO->listarTodos();
        $response = [];
        foreach ($zonas as $zona) {
            $response[] = [
                'id_zona' => $zona->getIdZona(),
                'nombre' => $zona->getNombre(),
                'descripcion' => $zona->getDescripcion()
            ];
        }
        return $response;
    }

    public function actualizarInformacionZona($zona) {
        if (is_array($zona)) {
            $zonaActual = $this->zonaDAO->obtenerPorId($zona['id_zona'] ?? 0);
            if (!$zonaActual) {
                return false;
            }
            if (isset($zona['nombre'])) {
                $zonaActual->setNombre($zona['nombre']);
            }
            if (isset($zona['descripcion'])) {
                $zonaActual->setDescripcion($zona['descripcion']);
            }
            return $this->zonaDAO->actualizar($zonaActual);
        }
        return $this->zonaDAO->actualizar($zona);
    }

    public function eliminarZona(int $id): bool {
        return $this->zonaDAO->eliminarPorId($id);
    }

    // ======= Luchador =======
    public function crearLuchador($luchador) {
        if (is_array($luchador)) {
            $luchador = new Luchador(
                null,
                $luchador['nombre'] ?? '',
                $luchador['especie'] ?? '',
                isset($luchador['nivelDePoderKi']) ? (float) $luchador['nivelDePoderKi'] : 0.0,
                $luchador['origen'] ?? '',
                isset($luchador['estado']) ? (bool) $luchador['estado'] : true
            );
        }
        return $this->luchadorDAO->crear($luchador);
    }

    public function buscarLuchador(string $nombre) {
        return $this->luchadorDAO->buscarPorNombre($nombre);
    }

    public function mostrarLuchadores(): array {
        $luchadores = $this->luchadorDAO->listarTodos();
        $response = [];
        foreach ($luchadores as $luchador) {
            $response[] = [
                'id_luchador' => $luchador->getIdLuchador(),
                'nombre' => $luchador->getNombre(),
                'especie' => $luchador->getEspecie(),
                'nivelDePoderKi' => $luchador->getNivelDePoderKi(),
                'origen' => $luchador->getOrigen(),
                'estado' => $luchador->getEstado()
            ];
        }
        return $response;
    }

    public function actualizarInformacionLuchador($luchador) {
        if (is_array($luchador)) {
            $actual = $this->luchadorDAO->buscarPorNombre($luchador['nombre'] ?? '');
            if (!$actual) {
                return false;
            }
            if (isset($luchador['especie'])) {
                $actual->setEspecie($luchador['especie']);
            }
            if (isset($luchador['nivelDePoderKi'])) {
                $actual->setNivelDePoderKi((float) $luchador['nivelDePoderKi']);
            }
            if (isset($luchador['origen'])) {
                $actual->setOrigen($luchador['origen']);
            }
            if (isset($luchador['estado'])) {
                $actual->setEstado((bool) $luchador['estado']);
            }
            return $this->luchadorDAO->actualizar($actual);
        }
        return $this->luchadorDAO->actualizar($luchador);
    }

    public function verEstadoLuchador(int $id) {
        $luchador = $this->luchadorDAO->obtenerPorId($id);
        return $luchador ? $luchador->getEstado() : null;
    }

    public function eliminarLuchador(int $id): bool {
        return $this->luchadorDAO->eliminarPorId($id);
    }
}

?>