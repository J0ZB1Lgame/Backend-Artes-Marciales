<?php
session_start();
include("Controlador/conexion.php");
include("Controlador/StaffController.php");

$staffController = new StaffController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['registrar'])) {
        $id_usuario = $_POST['id_usuario'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $tipo_documento = $_POST['tipo_documento'];
        $numero_documento = $_POST['numero_documento'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $staffController->registrarStaff($id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email);
        echo "Staff registrado exitosamente.";
    } elseif (isset($_POST['actualizar'])) {
        $id = $_POST['id'];
        $id_usuario = $_POST['id_usuario'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $tipo_documento = $_POST['tipo_documento'];
        $numero_documento = $_POST['numero_documento'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $estado = $_POST['estado'];
        $staffController->actualizarStaff($id, $id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email, $estado);
        echo "Staff actualizado exitosamente.";
    } elseif (isset($_POST['eliminar'])) {
        $id = $_POST['id'];
        $staffController->eliminarStaff($id);
        echo "Staff eliminado exitosamente.";
    }
}

$staffs = $staffController->listarStaff();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Staff</title>
</head>
<body>
    <h1>Gestión de Staff</h1>

    <h2>Registrar Staff</h2>
    <form method="POST">
        ID Usuario: <input type="number" name="id_usuario" required><br>
        Nombre: <input type="text" name="nombre" required><br>
        Apellido: <input type="text" name="apellido" required><br>
        Tipo Documento: <input type="text" name="tipo_documento"><br>
        Número Documento: <input type="text" name="numero_documento"><br>
        Teléfono: <input type="text" name="telefono"><br>
        Email: <input type="email" name="email"><br>
        <button type="submit" name="registrar">Registrar</button>
    </form>

    <h2>Lista de Staff</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>ID Usuario</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Estado</th>
        </tr>
        <?php foreach ($staffs as $staff): ?>
        <tr>
            <td><?php echo $staff->getIdStaff(); ?></td>
            <td><?php echo $staff->getIdUsuario(); ?></td>
            <td><?php echo $staff->getNombre(); ?></td>
            <td><?php echo $staff->getApellido(); ?></td>
            <td><?php echo $staff->getEstado(); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Actualizar Staff</h2>
    <form method="POST">
        ID: <input type="number" name="id" required><br>
        ID Usuario: <input type="number" name="id_usuario"><br>
        Nombre: <input type="text" name="nombre"><br>
        Apellido: <input type="text" name="apellido"><br>
        Tipo Documento: <input type="text" name="tipo_documento"><br>
        Número Documento: <input type="text" name="numero_documento"><br>
        Teléfono: <input type="text" name="telefono"><br>
        Email: <input type="email" name="email"><br>
        Estado: <select name="estado">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
        </select><br>
        <button type="submit" name="actualizar">Actualizar</button>
    </form>

    <h2>Eliminar Staff</h2>
    <form method="POST">
        ID: <input type="number" name="id" required><br>
        <button type="submit" name="eliminar">Eliminar</button>
    </form>
</body>
</html>