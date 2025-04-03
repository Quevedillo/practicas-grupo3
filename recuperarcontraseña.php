<?php
session_start();

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'ticket';
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
    $redirect = match($_SESSION['role']) {
        'admin' => 'index_admin.php',
        'tech' => 'index_tecnico.php',
        default => 'index_usuario.php'
    };
    header("Location: $redirect");
    exit();
}

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['reset_password'])) {
        // Procesar olvidé contraseña
        $email = trim($_POST['email']);
        
        // Verificar si el email existe
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Generar token único
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));
            
            // Guardar token en la base de datos
            $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_expires = :expires WHERE id = :id");
            $stmt->execute([
                'token' => $token,
                'expires' => $expires,
                'id' => $user['id']
            ]);
            
            // Enviar email (simulado en este ejemplo)
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=$token";
            $_SESSION['message'] = "Se ha enviado un enlace de recuperación a tu email.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "No existe una cuenta con ese email.";
            $_SESSION['message_type'] = 'error';
        }
    } else {
        // Procesar login normal
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Buscar el usuario en la base de datos
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el usuario existe
        if ($user) {
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
                $_SESSION['message'] = "Contraseña incorrecta.";
                $_SESSION['message_type'] = 'error';
            }
        } else {
            $_SESSION['message'] = "Usuario no encontrado.";
            $_SESSION['message_type'] = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Tickets</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #45a049;
            --dark-color: #333;
            --light-color: #f4f4f9;
            --danger-color: #e74c3c;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --info-color: #3498db;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
            transition: all 0.3s ease;
        }
        
        body.dark-mode {
            background-color: #1a1a1a;
            color: #f0f0f0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        .logo img {
            max-height: 60px;
        }
        
        .theme-toggle button {
            background-color: var(--dark-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .dark-mode .theme-toggle button {
            background-color: var(--light-color);
            color: var(--dark-color);
        }
        
        .login-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            max-width: 450px;
            margin: 0 auto;
            transition: all 0.3s ease;
        }
        
        .dark-mode .login-box {
            background: #2d2d2d;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .login-box h1 {
            text-align: center;
            margin-bottom: 25px;
            color: var(--primary-color);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .dark-mode .form-group input {
            background-color: #3d3d3d;
            border-color: #555;
            color: #f0f0f0;
        }
        
        .form-group input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
        }
        
        .login-button {
            width: 100%;
            background: var(--primary-color);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .login-button:hover {
            background: var(--secondary-color);
        }
        
        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .links a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 600;
        }
        
        .message.error {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
        
        .message.success {
            background-color: rgba(46, 204, 113, 0.2);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
        
        .reset-form {
            display: none;
            margin-top: 20px;
        }
        
        .reset-form.active {
            display: block;
        }
        
        .back-to-login {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: var(--info-color);
            cursor: pointer;
        }
        
        .back-to-login:hover {
            text-decoration: underline;
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
                <button id="theme-button"><i class="fas fa-moon"></i> Modo Oscuro</button>
            </div>
        </header>

        <main class="main-content">
            <div class="login-box">
                <h1>Sistema de Tickets de Soporte</h1>
                
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="message <?php echo $_SESSION['message_type']; ?>">
                        <?php 
                        echo htmlspecialchars($_SESSION['message']); 
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                    </div>
                <?php endif; ?>
                
                <form class="login-form" action="login.php" method="POST">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Usuario:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="login-button">Iniciar Sesión</button>
                </form>
                
                <form class="reset-form" id="resetForm" action="login.php" method="POST">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                        <input type="text" id="email" name="email" required>
                    </div>
                    <button type="submit" name="reset_password" class="login-button">Enviar Enlace de Recuperación</button>
                    <span class="back-to-login"><i class="fas fa-arrow-left"></i> Volver al login</span>
                </form>
                
                <div class="links">
                    <a href="#" id="forgot-password">¿Olvidó su contraseña?</a>
                    <a href="register.php">Registrarse</a>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Script para cambiar entre modo oscuro y modo claro
        const themeButton = document.getElementById('theme-button');
        const body = document.body;
        const icon = themeButton.querySelector('i');

        // Verificar preferencia del usuario
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            icon.classList.replace('fa-moon', 'fa-sun');
            themeButton.innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
        }

        themeButton.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                icon.classList.replace('fa-moon', 'fa-sun');
                themeButton.innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
                localStorage.setItem('darkMode', 'enabled');
            } else {
                icon.classList.replace('fa-sun', 'fa-moon');
                themeButton.innerHTML = '<i class="fas fa-moon"></i> Modo Oscuro';
                localStorage.setItem('darkMode', 'disabled');
            }
        });

        // Mostrar/ocultar formulario de recuperación
        const forgotPassword = document.getElementById('forgot-password');
        const loginForm = document.querySelector('.login-form');
        const resetForm = document.getElementById('resetForm');
        const backToLogin = document.querySelector('.back-to-login');

        forgotPassword.addEventListener('click', (e) => {
            e.preventDefault();
            loginForm.style.display = 'none';
            resetForm.classList.add('active');
        });

        backToLogin.addEventListener('click', () => {
            loginForm.style.display = 'block';
            resetForm.classList.remove('active');
        });
    </script>
</body>
</html>