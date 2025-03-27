<?php
session_start();

// Habilitar reporting de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'index_admin.php' : 'index_usuario.php'));
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
        $stored_password = $user['password'];
        
        // Si la contraseña no está cifrada, cifrarla y actualizarla en la base de datos
        if (!password_get_info($stored_password)['algo']) { 
            $hashed_password = password_hash($stored_password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $update_stmt->execute(['password' => $hashed_password, 'id' => $user['id']]);
            $stored_password = $hashed_password;
        }
        
        // Verificar la contraseña introducida por el usuario con la almacenada
        if (password_verify($password, $stored_password)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirigir según el rol
            header('Location: ' . ($user['role'] === 'admin' ? 'index_admin.php' : 'index_usuario.php'));
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
    <link rel="stylesheet" href="estilologin.css">
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

        themeButton.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                themeButton.textContent = 'Modo Claro';
            } else {
                themeButton.textContent = 'Modo Oscuro';
            }
        });
    </script>
</body>
</html>