<?php
/**
 * Script de Configuración de Base de Datos
 * Ejecuta este archivo una sola vez: http://localhost/Backend-Artes-Marciales/tournament-app/backend/setup_database.php
 */

// Configuración de conexión directa (sin la DB)
$host = 'localhost';
$user = 'root';
$pass = '';
$port = 3306;

// Conectar sin especificar base de datos
$conn = new mysqli($host, $user, $pass, '', $port);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Leer el archivo SQL
$sqlFile = __DIR__ . '/../database/tournament_db.sql';

if (!file_exists($sqlFile)) {
    die('No se encontró el archivo: ' . $sqlFile);
}

$sqlContent = file_get_contents($sqlFile);

// Ejecutar el SQL
$queries = explode(';', $sqlContent);

$successCount = 0;
$errorCount = 0;
$errors = [];
$messages = [];

foreach ($queries as $query) {
    $query = trim($query);
    
    // Saltar comentarios y líneas vacías
    if (empty($query) || substr($query, 0, 2) === '--') {
        continue;
    }
    
    if ($conn->query($query) === true) {
        $successCount++;
        if (stripos($query, 'CREATE') !== false) {
            preg_match('/CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+(\w+)/i', $query, $matches);
            if (!empty($matches[1])) {
                $messages[] = '✅ Tabla creada: <code>' . $matches[1] . '</code>';
            }
        }
    } else {
        $errorCount++;
        $errors[] = [
            'query' => $query,
            'error' => $conn->error
        ];
    }
}

// ================== INSERTAR USUARIO DE PRUEBA ==================
$conn2 = new mysqli($host, $user, $pass, 'torneo_db', $port);
if ($conn2->connect_error) {
    $errorCount++;
    $errors[] = ['query' => 'SELECT * FROM usuario', 'error' => $conn2->connect_error];
} else {
    $conn2->set_charset("utf8");
    
    // Verificar si ya existe usuario admin
    $checkUser = $conn2->query("SELECT COUNT(*) as cnt FROM usuario WHERE username = 'admin'");
    $row = $checkUser->fetch_assoc();
    
    if ($row['cnt'] == 0) {
        // Crear usuario de prueba
        $username = 'admin';
        $password = password_hash('password123', PASSWORD_BCRYPT, ['cost' => 12]);
        $rol = 'Administrador';
        
        $stmt = $conn2->prepare("INSERT INTO usuario (username, password, rol, estado) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $estado = 1;
            $stmt->bind_param("sssi", $username, $password, $rol, $estado);
            
            if ($stmt->execute()) {
                $successCount++;
                $messages[] = '✅ Usuario de prueba creado: <code>admin / password123</code>';
            } else {
                $errorCount++;
                $errors[] = ['query' => 'INSERT usuario', 'error' => $stmt->error];
            }
            $stmt->close();
        }
    } else {
        $messages[] = 'ℹ️ Usuario admin ya existe';
    }
    
    $conn2->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Base de Datos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .stat {
            padding: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .stat-success {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
        }
        .stat-error {
            background: #ffebee;
            border: 1px solid #ffcdd2;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin: 10px 0;
        }
        .stat-success .stat-number {
            color: #2e7d32;
        }
        .stat-error .stat-number {
            color: #c62828;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #f5f5f5;
            font-weight: bold;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ Configuración de Base de Datos</h1>
        
        <div class="info">
            <strong>ℹ️ Información:</strong> Este script ejecuta el archivo SQL para crear la base de datos y tablas necesarias.
        </div>

        <div class="stats">
            <div class="stat stat-success">
                <div>✅ Consultas Exitosas</div>
                <div class="stat-number"><?php echo $successCount; ?></div>
            </div>
            <div class="stat <?php echo $errorCount > 0 ? 'stat-error' : 'stat-success'; ?>">
                <div><?php echo $errorCount > 0 ? '❌ Errores' : '✅ Sin Errores'; ?></div>
                <div class="stat-number"><?php echo $errorCount; ?></div>
            </div>
        </div>

        <?php if ($successCount > 0): ?>
            <div class="success">
                <strong>✅ ¡Base de datos configurada exitosamente!</strong>
                <p>Se ejecutaron correctamente <?php echo $successCount; ?> consultas SQL.</p>
                
                <p><strong>📊 Tablas y datos creados:</strong></p>
                <ul>
                    <?php foreach ($messages as $msg): ?>
                        <li><?php echo $msg; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($errorCount > 0): ?>
            <div class="error">
                <strong>❌ Se encontraron errores:</strong>
                <table>
                    <thead>
                        <tr>
                            <th>Consulta</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($errors as $err): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars(substr($err['query'], 0, 50)); ?>...</code></td>
                                <td><?php echo htmlspecialchars($err['error']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="info" style="margin-top: 30px;">
            <strong>⏭️ Próximos pasos:</strong>
            <ol>
                <li>Verifica que no haya errores arriba ✅</li>
                <li>Intenta crear un usuario nuevamente en Postman</li>
                <li>Si aún tienes problemas, revisa la consola de errores de PHP en XAMPP</li>
            </ol>
        </div>

        <div class="info" style="background: #e3f2fd; border-color: #90caf9; color: #1565c0; margin-top: 30px;">
            <strong>🧪 Prueba en Postman:</strong>
            <h3>1️⃣ Iniciar Sesión (Recomendado primero)</h3>
            <pre style="background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto;">
POST http://localhost/Backend-Artes-Marciales/tournament-app/backend/api/endpoints/login/login_api.php?action=iniciar-sesion

Headers:
Content-Type: application/json

Body (JSON):
{
    "username": "admin",
    "password": "password123"
}

Respuesta esperada:
{
    "status": "success",
    "message": "Sesión iniciada",
    "data": {
        "idUsuario": 1,
        "username": "admin",
        "rol": "Administrador"
    }
}
            </pre>

            <h3>2️⃣ Crear Nuevo Usuario</h3>
            <pre style="background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto;">
POST http://localhost/Backend-Artes-Marciales/tournament-app/backend/api/endpoints/login/login_api.php?action=crear-usuario

Headers:
Content-Type: application/json

Body (JSON):
{
    "username": "operador",
    "password": "securepass123",
    "rol": "Árbitro"
}

Respuesta esperada:
{
    "status": "success",
    "message": "Usuario creado",
    "data": {
        "idUsuario": 2,
        "username": "operador",
        "password": "***",
        "rol": "Árbitro",
        "estado": true
    }
}
            </pre>

            <h3>3️⃣ Listar Todos los Usuarios</h3>
            <pre style="background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto;">
GET http://localhost/Backend-Artes-Marciales/tournament-app/backend/api/endpoints/login/login_api.php?action=listar-usuarios

Headers:
Content-Type: application/json
            </pre>
        </div>
    </div>
</body>
</html>
