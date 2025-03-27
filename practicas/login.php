<?php
session_start();

// Configuración de la base de datos CORREGIDA
$host = 'localhost';
$dbname = 'ticket';  // Nombre correcto según tu SQL
$user = 'root';
$pass = '';

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Verificar si el usuario ya está logueado
if (isset($_SESSION['user_id'])) {
    // Redirigir según el rol - verifica que estos archivos existan
    $redirect = match($_SESSION['role']) {
        'admin' => 'index_admin.php',
        'tech' => 'index_tecnico.php',  // Añadido para técnicos
        default => 'index_usuario.php'
    };
    header("Location: $redirect");
    exit();
}

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Buscar el usuario en la base de datos
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe
    if ($user) {
        // Verificación para contraseñas en texto plano (como en tu SQL)
        if ($user['password'] === $password) {
            // Si coincide con texto plano, creamos hash y actualizamos
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $update_stmt->execute(['password' => $hashed_password, 'id' => $user['id']]);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            $redirect = match($user['role']) {
                'admin' => 'index_admin.php',
                'tech' => 'index_tecnico.php',
                default => 'index_usuario.php'
            };
            header("Location: $redirect");
            exit();
        } elseif (password_verify($password, $user['password'])) {
            // Si la contraseña ya estaba hasheada y es correcta
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            $redirect = match($user['role']) {
                'admin' => 'index_admin.php',
                'tech' => 'index_tecnico.php',
                default => 'index_usuario.php'
            };
            header("Location: $redirect");
            exit();
        } else {
            $error_message = "Contraseña incorrecta.";
        }
    } else {
        $error_message = "Usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Tickets</title>
    <style>
        /* Estilos básicos si no tienes el CSS */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .login-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: 50px auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .login-button {
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
        .dark-mode {
            background-color: #333;
            color: #fff;
        }
        .dark-mode .login-box {
            background: #444;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" alt="Logo del Sistema">
            </div>
            <div class="theme-toggle">
                <button id="theme-button">Modo Oscuro</button>
            </div>
        </header>

        <main class="main-content">
            <div class="login-box">
                <h1>Sistema de Tickets de Soporte</h1>
                <?php if (isset($error_message)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <form class="login-form" action="login.php" method="POST">
                    <div class="form-group">
                        <label for="username">Usuario:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="login-button">Iniciar Sesión</button>
                </form>
                <div class="links">
                    <a href="#">¿Olvidó su contraseña?</a>
                    <a href="register.php">Registrarse</a>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Script para cambiar entre modo oscuro y modo claro
        const themeButton = document.getElementById('theme-button');
        const body = document.body;

        // Verificar preferencia del usuario
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            themeButton.textContent = 'Modo Claro';
        }

        themeButton.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                themeButton.textContent = 'Modo Claro';
                localStorage.setItem('darkMode', 'enabled');
            } else {
                themeButton.textContent = 'Modo Oscuro';
                localStorage.setItem('darkMode', 'disabled');
            }
        });
    </script>
</body>
</html>