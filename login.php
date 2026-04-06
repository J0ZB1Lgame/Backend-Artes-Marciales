<?php
session_start();

// Si ya hay sesión activa, entrar al menú
if (isset($_SESSION['id'])) {
    header("Location: ../modelo/menu.php");
    exit();
}

include("Controlador/conexion.php");

$error = "";

if (isset($_POST['login'])) {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    // Buscar usuario por username
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE username = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($clave === $row['password_hash']) { // Cambiar a password_verify si usas hash

            // Guardar datos en sesión
            $_SESSION['id'] = $row['id_usuario'];  // ID real de la tabla usuario
            $_SESSION['usuario'] = $row['username'];
            $_SESSION['estado'] = $row['estado'];
            $_SESSION['fecha_creacion'] = $row['fecha_creacion'];

            header("Location: staff_management.php");
            exit();
        } else {
            $error = "<div class='alert error'>❌ Contraseña incorrecta</div>";
        }
    } else {
        $error = "<div class='alert error'>❌ Usuario no encontrado</div>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - Torneo de Artes Marciales</title>

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #eef2f7;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    header {
      background: #2d6a4f;
      padding: 20px;
      text-align: center;
      color: white;
      font-size: 26px;
      font-weight: bold;
    }

    .container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 30px;
    }

    .login-box {
      background: white;
      padding: 40px;
      width: 350px;
      border-radius: 14px;
      box-shadow: 0 0 15px rgba(0,0,0,0.15);
      text-align: center;
    }

    .login-box h2 {
      margin-bottom: 15px;
      color: #2d6a4f;
      font-size: 26px;
    }

    .login-box input {
      width: 92%;
      padding: 12px;
      margin: 10px 0;
      border: 2px solid #2d6a4f;
      border-radius: 8px;
      font-size: 15px;
    }

    .login-box button {
      background: #2d6a4f;
      color: white;
      width: 96%;
      padding: 12px;
      border: none;
      margin-top: 15px;
      font-size: 17px;
      border-radius: 8px;
      cursor: pointer;
    }

    .alert {
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 15px;
    }

    .alert.error {
      background: #ffe5e5;
      border: 1px solid #ff7b7b;
      color: #b30000;
    }

    footer {
      text-align: center;
      padding: 15px;
      background: #2d6a4f;
      color: white;
    }
  </style>
</head>

<body>

<header>Torneo de Artes Marciales</header>

<div class="container">
    <div class="login-box">
        <h2 style="color:white; background-color:#2d6a4f; padding:5px; border-radius:6px;">Iniciar Sesión</h2>

        <?= $error ?>

        <form method="POST">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="clave" placeholder="Contraseña" required>
            <button type="submit" name="login">Entrar</button>
        </form>

        <h2 style="margin-top: 25px;">Registrarse</h2>

        <form method="POST" action="registroU.php">
            <button type="submit">Registrarse</button>
        </form>
    </div>
</div>

<footer>© 2026 Torneo de Artes Marciales</footer>

</body>
</html>
