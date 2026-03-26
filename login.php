<?php
session_start();

// Si ya hay sesión activa, entrar al menú
if (isset($_SESSION['id'])) {
    header("Location: ../modelo/menu.php");
    exit();
}

include("../Controlador/conexion.php");

$error = "";

if (isset($_POST['login'])) {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    // Buscar usuario por nombre
    $stmt = $conn->prepare("SELECT * FROM datos WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($clave === $row['clave']) { // <- Debes cambiar a password_hash en el futuro

            // Guardar datos en sesión
            $_SESSION['id'] = $row['id'];  // ID real de la tabla datos
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['id_perfil'] = $row['id_perfil'];
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['email'] = $row['email'];

            header("Location: ../modelo/menu.php");
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
  <title>Inicio - Verduras de Colombia</title>

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

<header>Verduras de Colombia</header>

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

<footer>© 2025 Frutas y Verduras de Colombia</footer>

</body>
</html>
