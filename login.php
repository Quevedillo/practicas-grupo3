<?php
session_start();
include("database.php"); // Incluir el archivo de conexión PDO

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {  // Verificar si los datos están presentes
        $email = $_POST['email'];  // Obtener email del formulario
        $password = $_POST['password'];  // Obtener contraseña del formulario

        // Usar PDO para hacer la consulta
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);  // Vincular el email
        $stmt->execute();

        // Verificar si el usuario existe
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();  // Obtener los datos del usuario
            if (password_verify($password, $user['password'])) {
                // Si la contraseña es correcta, iniciar sesión
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: dashboard.php");  // Redirigir al dashboard
                exit();
            } else {
                echo "Contraseña incorrecta";
            }
        } else {
            echo "Usuario no encontrado";
        }
    } else {
        echo "Por favor, complete todos los campos.";
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

                <!-- Mostrar error si las credenciales son incorrectas -->
                <?php if (isset($error)): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>

                <form class="login-form" method="POST">
                    <div class="form-group">
                        <label for="email">Correo electrónico:</label>
                        <input type="email" id="email" name="email" required>
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
    </script>
</body>
</html>
