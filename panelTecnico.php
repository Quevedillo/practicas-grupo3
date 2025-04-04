<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'tecnico') {
    header('Location: login.php');
    exit();
}

require 'database.php';

$sql = "SELECT t.id, t.title, t.description, t.status, t.created_at, u.username 
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        WHERE t.technician_id = :technician_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['technician_id' => $_SESSION['id']]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Técnico</title>
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
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?> ▼</span>
                    <div class="user-dropdown">
                        <a href="logout.php">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </header>

        <nav class="navbar">
            <ul>
                <li><a href="dashboard-tecnico.php" class="active">Panel Técnico</a></li>
                <li><a href="gestionPerfilUsuario.php">Editar Perfil</a></li>
                <li><a href="clienteTecnico.php">Comunicación</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <h2>Tickets Asignados</h2>
            <?php if (count($tickets) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Usuario</th>
                        <th>Estado</th>
                        <th>Fecha de creación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ticket['id']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['description']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['username']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No hay tickets asignados.</p>
            <?php endif; ?>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeButton = document.getElementById('theme-button');
            const body = document.body;

            if (localStorage.getItem('darkMode') === 'enabled') {
                body.classList.add('dark-mode');
                themeButton.textContent = 'Modo Claro';
            }

            themeButton.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                const isDarkMode = body.classList.contains('dark-mode');

                themeButton.textContent = isDarkMode ? 'Modo Claro' : 'Modo Oscuro';
                localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
            });
        });
    </script>
</body>
</html>
