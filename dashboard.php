<?php
// crear conexion (fvalencia)
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
    
    require 'database.php';
// fin crear conexion (fvalencia)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Tickets</title>
    <link rel="stylesheet" href="estilodashboard.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" alt="Logo del Sistema">
            </div>
            <div class="header-right">
                <div class="theme-toggle">
                    <button id="theme-button">Modo Oscuro</button>
                </div>
                <div class="user-menu">
                    <span>USUARIO ▼</span>
                    <div class="user-dropdown">
                        <a href="logout.php">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </header>

        <nav class="navbar">
            <ul>
                <li><a href="#">Panel</a></li>
                <li><a href="#">Mis Tickets</a></li>
                <li><a href="#">Perfil</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="dashboard-summary">
                <h2>Resumen</h2>
                <div class="summary-cards">
                    <div class="card">
                        <h3>Tickets Abiertos</h3>
                        <p><?php ?></p>
                    </div>
                    <div class="card">
                        <h3>Tickets Resueltos</h3>
                        <p>10</p>
                    </div>
                    <div class="card">
                        <h3>Total Tickets</h3>
                        <p>15</p>
                    </div>
                </div>
                <button class="new-ticket-button">+ Nuevo Ticket</button>
            </div>

            <div class="recent-tickets">
                <h2>Tickets Recientes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Problema con el software</td>
                            <td><span class="status open">Abierto</span></td>
                            <td>2023-10-01</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Error en la red</td>
                            <td><span class="status resolved">Resuelto</span></td>
                            <td>2023-10-02</td>
                        </tr>
                    </tbody>
                </table>
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