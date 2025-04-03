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
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>

                <form class="login-form" method="POST">
                    <div class="form-group">
                        <label for="login_input">Usuario o Correo electrónico:</label>
                        <input type="text" id="login_input" name="login_input" required>
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