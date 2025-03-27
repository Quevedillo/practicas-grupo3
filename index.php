<?php
session_start();
// Si el usuario ya está logueado, redirigir según su rol
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'index_admin.php' : 'index_usuario.php'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Sistema de Tickets</title>
    <link rel="stylesheet" href="estilologin.css"> <!-- Reutilizamos los mismos estilos -->
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
            <div class="login-box" style="max-width: 600px;">
                <h1>Sistema de Gestión de Tickets</h1>
                <div class="welcome-message">
                    <p>Bienvenido al sistema de tickets de soporte técnico. Gestiona y realiza seguimiento de todos tus problemas técnicos en un solo lugar.</p>
                    
                    <div class="features">
                        <h3>Características principales:</h3>
                        <ul>
                            <li>Creación y seguimiento de tickets</li>
                            <li>Notificaciones en tiempo real</li>
                            <li>Soporte para múltiples categorías</li>
                            <li>Sistema de prioridades</li>
                            <li>Gestión de usuarios (para administradores)</li>
                        </ul>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="login.php" class="login-button" style="display: inline-block; width: auto; padding: 10px 20px; margin: 10px;">Iniciar Sesión</a>
                        <a href="register.php" class="login-button" style="display: inline-block; width: auto; padding: 10px 20px; margin: 10px; background-color: #3498db;">Registrarse</a>
                    </div>
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
            
            // Guardar preferencia en localStorage
            localStorage.setItem('darkMode', body.classList.contains('dark-mode'));
        });

        // Cargar preferencia de tema al cargar la página
        if (localStorage.getItem('darkMode') === 'true') {
            body.classList.add('dark-mode');
            themeButton.textContent = 'Modo Claro';
        }
    </script>
</body>
</html>