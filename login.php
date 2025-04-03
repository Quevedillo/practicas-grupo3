<?php
// Configuración inicial de sesión más estricta
session_start([
    'cookie_lifetime' => 86400, // 1 día
    'cookie_secure'   => isset($_SERVER['HTTPS']), // Solo enviar cookies sobre HTTPS
    'cookie_httponly' => true, // Prevenir acceso a cookies via JavaScript
    'use_strict_mode' => true // Mejor seguridad para IDs de sesión
]);

include("database.php");

// Limpiar cualquier sesión existente si llegan a la página de login
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
    session_regenerate_id(true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['email_or_username']) && !empty($_POST['password'])) {
        $email_or_username = trim($_POST['email_or_username']);
        $password = $_POST['password'];

        try {
            $sql = "SELECT * FROM users WHERE email = :email_or_username OR username = :email_or_username";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email_or_username', $email_or_username, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch();
                
                if (password_verify($password, $user['password'])) {
                    // Regenerar ID de sesión para prevenir fijación
                    session_regenerate_id(true);
                    
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['last_login'] = time();
                    
                    // Redirección con JavaScript como respaldo
                    echo '<script>window.location.href = "dashboard.php";</script>';
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Credenciales incorrectas";
                }
            } else {
                $error = "Usuario/Email no encontrado";
            }
        } catch (PDOException $e) {
            error_log("Error de base de datos: " . $e->getMessage());
            $error = "Error del sistema. Por favor intente más tarde.";
        }
    } else {
        $error = "Por favor complete todos los campos";
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

                <?php if (isset($error)): ?>
                    <div class="error-message" style="color: red; margin-bottom: 15px;">
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                        <label for="email_or_username">Usuario o Correo electrónico:</label>
                        <input type="text" id="email_or_username" name="email_or_username" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="login-button">Iniciar Sesión</button>
                </form>
                <div class="links">
                    <a href="recuperarcontraseña.php">¿Olvidó su contraseña?</a>
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
        
        // Limpiar mensajes de error al empezar a escribir
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', () => {
                const errorMsg = document.querySelector('.error-message');
                if (errorMsg) errorMsg.style.display = 'none';
            });
        });
    </script>
</body>
</html>