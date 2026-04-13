<?php

require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../models/daos/login/impl/UsuarioDAOImpl.php';

class UsuarioController {
    private $usuarioDAO;

    public function __construct() {
        global $conn;
        $this->usuarioDAO = new UsuarioDAOImpl();
    }

    /**
     * Crear un nuevo usuario con contraseña hasheada
     * @param array $datos Datos del usuario (username, password, rol)
     * @return array|false Usuario creado como array o false en caso de error
     */
    public function crearUsuario($datos) {
        // Validar datos de entrada
        if (!isset($datos['username']) || !isset($datos['password']) || !isset($datos['rol'])) {
            return false;
        }

        $username = trim($datos['username']);
        $password = $datos['password'];
        $rol = trim($datos['rol']);

        // Validaciones
        if (empty($username) || empty($password) || empty($rol)) {
            throw new Exception('Los campos username, password y rol no pueden estar vacíos');
        }

        if (strlen($username) < 3 || strlen($username) > 50) {
            throw new Exception('El username debe tener entre 3 y 50 caracteres');
        }

        if (strlen($password) < 6) {
            throw new Exception('La contraseña debe tener al menos 6 caracteres');
        }

        // Hashear la contraseña usando BCRYPT
        $passwordHashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Crear objeto Usuario con contraseña hasheada
        $usuario = new Usuario(null, $username, $passwordHashed, $rol, true);
        $usuarioCreado = $this->usuarioDAO->crear($usuario);
        
        // Devolver como array para serialización JSON correcta
        return [
            'idUsuario' => $usuarioCreado->getIdUsuario(),
            'username' => $usuarioCreado->getUsername(),
            'rol' => $usuarioCreado->getRol(),
            'estado' => (bool) $usuarioCreado->getEstado()
            // NO incluir contraseña en la respuesta
        ];
    }

    /**
     * Buscar usuario por ID
     * @param int $id ID del usuario
     * @return array|null Usuario encontrado como array o null
     */
    public function buscarUsuario(int $id) {
        if ($id <= 0) {
            return null;
        }
        
        $usuario = $this->usuarioDAO->obtenerPorId($id);
        
        if (!$usuario) {
            return null;
        }
        
        return [
            'idUsuario' => $usuario->getIdUsuario(),
            'username' => $usuario->getUsername(),
            'rol' => $usuario->getRol(),
            'estado' => (bool) $usuario->getEstado()
            // NO incluir contraseña por seguridad
        ];
    }

    /**
     * Obtener todos los usuarios (sin devolver contraseñas)
     * @return array Lista de usuarios como arrays
     */
    public function mostrarUsuario(): array {
        $usuarios = $this->usuarioDAO->listarTodos();
        
        $usuariosArray = [];
        foreach ($usuarios as $usuario) {
            $usuariosArray[] = [
                'idUsuario' => $usuario->getIdUsuario(),
                'username' => $usuario->getUsername(),
                'rol' => $usuario->getRol(),
                'estado' => (bool) $usuario->getEstado()
                // NO incluir contraseña en la respuesta por seguridad
            ];
        }
        
        return $usuariosArray;
    }

    /**
     * Actualizar información del usuario
     * @param array $datos Datos a actualizar (id_usuario, username, rol, estado)
     * @return bool True si se actualizó correctamente
     */
    public function actualizarInformacion($datos) {
        if (!isset($datos['id_usuario'])) {
            return false;
        }

        $usuario = $this->usuarioDAO->obtenerPorId($datos['id_usuario']);
        if (!$usuario) {
            return false;
        }

        if (isset($datos['username']) && !empty($datos['username'])) {
            $usuario->setUsername(trim($datos['username']));
        }

        if (isset($datos['rol']) && !empty($datos['rol'])) {
            $usuario->setRol(trim($datos['rol']));
        }

        if (isset($datos['estado'])) {
            $usuario->setEstado((bool) $datos['estado']);
        }

        return $this->usuarioDAO->actualizar($usuario);
    }

    /**
     * Actualizar username del usuario
     * @param int $id ID del usuario
     * @param string $username Nuevo username
     * @return bool True si se actualizó correctamente
     */
    public function actualizarUsername(int $id, string $username): bool {
        if ($id <= 0 || empty($username)) {
            return false;
        }

        $usuario = $this->usuarioDAO->obtenerPorId($id);
        if (!$usuario) {
            return false;
        }

        $usuario->setUsername(trim($username));
        return $this->usuarioDAO->actualizar($usuario);
    }

    /**
     * Actualizar contraseña del usuario de forma segura
     * @param int $id ID del usuario
     * @param string $password Nueva contraseña
     * @return bool True si se actualizó correctamente
     */
    public function actualizarPassword(int $id, string $password): bool {
        if ($id <= 0 || empty($password)) {
            return false;
        }

        if (strlen($password) < 6) {
            throw new Exception('La contraseña debe tener al menos 6 caracteres');
        }

        $usuario = $this->usuarioDAO->obtenerPorId($id);
        if (!$usuario) {
            return false;
        }

        // Hashear la contraseña con BCRYPT
        $passwordHashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $usuario->setPassword($passwordHashed);
        
        return $this->usuarioDAO->actualizar($usuario);
    }

    /**
     * Verificar estado del usuario
     * @param int $id ID del usuario
     * @return bool|null Estado del usuario o null si no existe
     */
    public function verEstado(int $id) {
        $usuario = $this->usuarioDAO->obtenerPorId($id);
        return $usuario ? $usuario->getEstado() : null;
    }

    /**
     * Eliminar usuario por ID
     * @param int $id ID del usuario
     * @return bool True si se eliminó correctamente
     */
    public function eliminarUsuario(int $id): bool {
        if ($id <= 0) {
            return false;
        }
        return $this->usuarioDAO->eliminarPorId($id);
    }

    public function validarPermisos(string $accion): bool {
        $rolActual = $_SESSION['rol'] ?? null;
        if (!$rolActual) {
            return false;
        }

        $permisosPorRol = [
            'Administrador' => ['*'],
            'Coordinador' => ['listar', 'obtener', 'usuarios', 'turnos', 'registrar', 'roles-asignados'],
            'staff' => ['iniciar-sesion', 'listar', 'usuarios']
        ];

        $permisos = $permisosPorRol[$rolActual] ?? [];
        if (in_array('*', $permisos, true)) {
            return true;
        }
        return in_array($accion, $permisos, true);
    }

    /**
     * Respuesta JSON exitosa
     * @param mixed $data Datos a devolver
     * @param string $message Mensaje de respuesta
     * @param int $status Código HTTP
     */
    public function jsonResponse($data, $message = "OK", $status = 200) {
        http_response_code($status);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    /**
     * Respuesta JSON con error
     * @param string $message Mensaje de error
     * @param int $status Código HTTP
     */
    public function jsonError($message, $status = 400) {
        http_response_code($status);
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }
}

?>