<?php
session_start();
include("Controlador/conexion.php");
include("Controlador/StaffController.php");

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$staffController = new StaffController();
$message = '';
$staffs = [];
$usuarios = [];
$turnos = [];
$roles = [];
$asignaciones = [];
$staffRoles = [];

try {
    $staffs = $staffController->listarStaff();
    $usuarios = $staffController->listarUsuarios();
    $turnos = $staffController->listarTurnos();
    $roles = $staffController->listarTiposRol();
    $asignaciones = $staffController->listarAsignacionesTurno();
    $staffRoles = $staffController->listarRolesAsignados();
} catch (Exception $e) {
    $message = 'Error al cargar datos: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['registrar'])) {
            $id_usuario = $_POST['id_usuario'];
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $tipo_documento = trim($_POST['tipo_documento'] ?? '');
            $numero_documento = trim($_POST['numero_documento'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $staffController->registrarStaff($id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email);
            $message = 'Staff registrado exitosamente.';
        } elseif (isset($_POST['actualizar'])) {
            $id = $_POST['id'];
            $id_usuario = $_POST['id_usuario'];
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $tipo_documento = trim($_POST['tipo_documento'] ?? '');
            $numero_documento = trim($_POST['numero_documento'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $estado = $_POST['estado'];
            $staffController->actualizarStaff($id, $id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email, $estado);
            $message = 'Staff actualizado exitosamente.';
        } elseif (isset($_POST['eliminar'])) {
            $id = $_POST['id'];
            $staffController->eliminarStaff($id);
            $message = 'Staff eliminado exitosamente.';
        } elseif (isset($_POST['asignar_rol'])) {
            $id_staff = $_POST['id_staff_rol'];
            $id_tipo_rol = $_POST['id_tipo_rol'];
            $staffController->asignarRol($id_staff, $id_tipo_rol);
            $message = 'Rol asignado al staff exitosamente.';
        } elseif (isset($_POST['asignar_turno'])) {
            $id_staff = $_POST['id_staff_turno'];
            $id_turno = $_POST['id_turno'];
            $fecha = $_POST['fecha'];
            $staffController->asignarTurno($id_staff, $id_turno, $fecha);
            $message = 'Turno asignado exitosamente.';
        } elseif (isset($_POST['revocar_rol'])) {
            $id_staff = $_POST['id_staff_revocar'];
            $id_tipo_rol = $_POST['id_tipo_rol_revocar'];
            $staffController->revocarRol($id_staff, $id_tipo_rol);
            $message = 'Rol revocado exitosamente.';
        }
        // Recargar datos después de la operación
        $staffs = $staffController->listarStaff();
        $turnos = $staffController->listarTurnos();
        $roles = $staffController->listarTiposRol();
        $asignaciones = $staffController->listarAsignacionesTurno();
        $staffRoles = $staffController->listarRolesAsignados();
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
    }
}

// Crear mapeos para referencias rápidas
$staffById = [];
foreach ($staffs as $staff) {
    $staffById[$staff['id_staff']] = $staff['nombre'] . ' ' . $staff['apellido'];
}
$turnoById = [];
foreach ($turnos as $turno) {
    $turnoById[$turno['id_turno']] = $turno['nombre'];
}
$rolById = [];
foreach ($roles as $rol) {
    $rolById[$rol['id_tipo_rol']] = $rol['nombre'];
}
?>
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Staff</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f6f8fa; color: #1f2937; }
        .panel { background: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; padding: 18px; margin-bottom: 20px; }
        h1, h2, h3 { color: #111827; }
        form { display: grid; gap: 10px; margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 4px; }
        input[type="text"], input[type="email"], input[type="number"], input[type="date"], select { width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; }
        button { background: #2563eb; color: white; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 10px; border: 1px solid #d1d5db; text-align: left; }
        th { background: #e5e7eb; }
        .actions button { margin-right: 6px; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; }
        .success { background: #d1fae5; border: 1px solid #34d399; color: #065f46; }
        .error { background: #fee2e2; border: 1px solid #f87171; color: #991b1b; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media(max-width: 900px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <h1>Gestión de Staff</h1>

    <?php if ($message): ?>
        <div class="alert <?php echo strpos($message, 'Error') === 0 ? 'error' : 'success'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="panel grid-2">
        <div>
            <h2>Registrar nuevo staff</h2>
            <form method="POST">
                <label for="id_usuario">Usuario</label>
                <select name="id_usuario" id="id_usuario" required>
                    <option value="">Seleccionar usuario</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?php echo htmlspecialchars($usuario['id_usuario']); ?>"><?php echo htmlspecialchars($usuario['username']); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" required>
                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" required>
                <label for="tipo_documento">Tipo de documento</label>
                <input type="text" name="tipo_documento" id="tipo_documento">
                <label for="numero_documento">Número de documento</label>
                <input type="text" name="numero_documento" id="numero_documento">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono">
                <label for="email">Email</label>
                <input type="email" name="email" id="email">
                <button type="submit" name="registrar">Registrar staff</button>
            </form>
        </div>
        <div>
            <h2>Actualizar staff</h2>
            <form method="POST" id="formActualizarStaff">
                <label for="id_actualizar">ID de staff</label>
                <input type="number" name="id" id="id_actualizar" required>
                <label for="id_usuario_actualizar">Usuario</label>
                <select name="id_usuario" id="id_usuario_actualizar" required>
                    <option value="">Seleccionar usuario</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?php echo htmlspecialchars($usuario['id_usuario']); ?>"><?php echo htmlspecialchars($usuario['username']); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="nombre_actualizar">Nombre</label>
                <input type="text" name="nombre" id="nombre_actualizar" required>
                <label for="apellido_actualizar">Apellido</label>
                <input type="text" name="apellido" id="apellido_actualizar" required>
                <label for="tipo_documento_actualizar">Tipo de documento</label>
                <input type="text" name="tipo_documento" id="tipo_documento_actualizar">
                <label for="numero_documento_actualizar">Número de documento</label>
                <input type="text" name="numero_documento" id="numero_documento_actualizar">
                <label for="telefono_actualizar">Teléfono</label>
                <input type="text" name="telefono" id="telefono_actualizar">
                <label for="email_actualizar">Email</label>
                <input type="email" name="email" id="email_actualizar">
                <label for="estado_actualizar">Estado</label>
                <select name="estado" id="estado_actualizar">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
                <button type="submit" name="actualizar">Actualizar staff</button>
            </form>
        </div>
    </div>

    <div class="panel">
        <h2>Lista de staff</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Usuario</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Documento</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staffs as $staff): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($staff['id_staff']); ?></td>
                        <td><?php echo htmlspecialchars($staff['id_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($staff['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($staff['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($staff['tipo_documento']) . ' ' . htmlspecialchars($staff['numero_documento']); ?></td>
                        <td><?php echo htmlspecialchars($staff['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($staff['email']); ?></td>
                        <td><?php echo htmlspecialchars($staff['estado']); ?></td>
                        <td class="actions">
                            <button type="button" onclick="llenarFormulario(<?php echo htmlspecialchars($staff['id_staff']); ?>, <?php echo htmlspecialchars($staff['id_usuario']); ?>, '<?php echo htmlspecialchars(addslashes($staff['nombre'])); ?>', '<?php echo htmlspecialchars(addslashes($staff['apellido'])); ?>', '<?php echo htmlspecialchars(addslashes($staff['tipo_documento'])); ?>', '<?php echo htmlspecialchars(addslashes($staff['numero_documento'])); ?>', '<?php echo htmlspecialchars(addslashes($staff['telefono'])); ?>', '<?php echo htmlspecialchars(addslashes($staff['email'])); ?>', '<?php echo htmlspecialchars($staff['estado']); ?>')">Editar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="panel grid-2">
        <div>
            <h2>Eliminar staff</h2>
            <form method="POST">
                <label for="id_eliminar">ID de staff</label>
                <input type="number" name="id" id="id_eliminar" required>
                <button type="submit" name="eliminar">Eliminar staff</button>
            </form>
        </div>
        <div>
            <h2>Asignar rol</h2>
            <form method="POST">
                <label for="id_staff_rol">ID Staff</label>
                <select name="id_staff_rol" id="id_staff_rol" required>
                    <option value="">Seleccionar staff</option>
                    <?php foreach ($staffs as $staff): ?>
                        <option value="<?php echo htmlspecialchars($staff['id_staff']); ?>"><?php echo htmlspecialchars($staff['nombre'] . ' ' . $staff['apellido']); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="id_tipo_rol">Tipo de rol</label>
                <select name="id_tipo_rol" id="id_tipo_rol" required>
                    <option value="">Seleccionar rol</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo htmlspecialchars($rol['id_tipo_rol']); ?>"><?php echo htmlspecialchars($rol['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="asignar_rol">Asignar rol</button>
            </form>
        </div>
    </div>

    <div class="panel grid-2">
        <div>
            <h2>Asignar turno</h2>
            <form method="POST">
                <label for="id_staff_turno">ID Staff</label>
                <select name="id_staff_turno" id="id_staff_turno" required>
                    <option value="">Seleccionar staff</option>
                    <?php foreach ($staffs as $staff): ?>
                        <option value="<?php echo htmlspecialchars($staff['id_staff']); ?>"><?php echo htmlspecialchars($staff['nombre'] . ' ' . $staff['apellido']); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="id_turno">Turno</label>
                <select name="id_turno" id="id_turno" required>
                    <option value="">Seleccionar turno</option>
                    <?php foreach ($turnos as $turno): ?>
                        <option value="<?php echo htmlspecialchars($turno['id_turno']); ?>"><?php echo htmlspecialchars($turno['nombre']) . ' (' . htmlspecialchars($turno['hora_inicio']) . ' - ' . htmlspecialchars($turno['hora_fin']) . ')'; ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="fecha">Fecha</label>
                <input type="date" name="fecha" id="fecha" required>
                <button type="submit" name="asignar_turno">Asignar turno</button>
            </form>
        </div>
        <div>
            <h2>Revocar rol</h2>
            <form method="POST">
                <label for="id_staff_revocar">ID Staff</label>
                <select name="id_staff_revocar" id="id_staff_revocar" required>
                    <option value="">Seleccionar staff</option>
                    <?php foreach ($staffs as $staff): ?>
                        <option value="<?php echo htmlspecialchars($staff['id_staff']); ?>"><?php echo htmlspecialchars($staff['nombre'] . ' ' . $staff['apellido']); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="id_tipo_rol_revocar">Tipo de rol</label>
                <select name="id_tipo_rol_revocar" id="id_tipo_rol_revocar" required>
                    <option value="">Seleccionar rol</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo htmlspecialchars($rol['id_tipo_rol']); ?>"><?php echo htmlspecialchars($rol['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="revocar_rol">Revocar rol</button>
            </form>
        </div>
    </div>

    <div class="panel">
        <h2>Asignaciones de turno activas</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Asignación</th>
                    <th>Staff</th>
                    <th>Turno</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($asignaciones as $asignacion): ?>
                    <?php if ($asignacion['estado'] === 'activo'): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($asignacion['id_asignacion']); ?></td>
                            <td><?php echo htmlspecialchars($staffById[$asignacion['id_staff']] ?? $asignacion['id_staff']); ?></td>
                            <td><?php echo htmlspecialchars($turnoById[$asignacion['id_turno']] ?? $asignacion['id_turno']); ?></td>
                            <td><?php echo htmlspecialchars($asignacion['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($asignacion['estado']); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="panel">
        <h2>Roles asignados por staff</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Staff</th>
                    <th>Staff</th>
                    <th>Rol</th>
                    <th>Fecha de asignación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staffRoles as $staffRol): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($staffRol['id_staff']); ?></td>
                        <td><?php echo htmlspecialchars($staffById[$staffRol['id_staff']] ?? $staffRol['id_staff']); ?></td>
                        <td><?php echo htmlspecialchars($rolById[$staffRol['id_tipo_rol']] ?? $staffRol['id_tipo_rol']); ?></td>
                        <td><?php echo htmlspecialchars($staffRol['fecha_asignacion'] ?? 'N/A'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function llenarFormulario(id, idUsuario, nombre, apellido, tipoDocumento, numeroDocumento, telefono, email, estado) {
            document.getElementById('id_actualizar').value = id;
            document.getElementById('id_usuario_actualizar').value = idUsuario;
            document.getElementById('nombre_actualizar').value = nombre;
            document.getElementById('apellido_actualizar').value = apellido;
            document.getElementById('tipo_documento_actualizar').value = tipoDocumento;
            document.getElementById('numero_documento_actualizar').value = numeroDocumento;
            document.getElementById('telefono_actualizar').value = telefono;
            document.getElementById('email_actualizar').value = email;
            document.getElementById('estado_actualizar').value = estado;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
</html>