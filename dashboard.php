<?php
session_start();
require 'database.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$sql = "SELECT * FROM tickets WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $_SESSION['id']]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?> ▼</span>
                    <div class="user-dropdown">
                        <a href="logout.php">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </header>

        <nav class="navbar">
            <ul>
                <li><a href="dashboard.php" class="active">Panel</a></li>
                <li><a href="misTickets.php">Mis Tickets</a></li>
                <li><a href="gestionPerfilUsuario.php">Editar Perfil</a></li>
                <li><a href="clienteTecnico.php">Comunicación</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="dashboard-summary">
                <h2>Resumen</h2>
                <div class="summary-cards">
                    <div class="card">
                        <h3>Tickets Abiertos</h3>
                        <p><?php echo count(array_filter($tickets, function($ticket) { 
                            return $ticket['status'] == 'open' || $ticket['status'] == 'in_progress'; 
                        })); ?></p>
                    </div>
                    <div class="card">
                        <h3>Tickets Resueltos</h3>
                        <p><?php echo count(array_filter($tickets, function($ticket) { 
                            return $ticket['status'] == 'resolved' || $ticket['status'] == 'closed'; 
                        })); ?></p>
                    </div>
                    <div class="card">
                        <h3>Total Tickets</h3>
                        <p><?php echo count($tickets); ?></p>
                    </div>
                </div>
                <button class="new-ticket-button"><a href="crearTicket.php">+ Nuevo Ticket</a></button>
            </div>

            <div class="recent-tickets">
                <h2>Tickets Recientes</h2>
                <?php if (count($tickets) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha de creación</th>
                            <th>Fecha actualización</th>
                            <th>Ver detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ticket['id']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['description']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['priority']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['updated_at']); ?></td>
                            <td><a href="ver_ticket.php?id=<?php echo $ticket['id']; ?>">Ver detalles</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>No hay tickets registrados.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeButton = document.getElementById('theme-button');
            const body = document.body;
            
            // Check for saved theme preference
            if (localStorage.getItem('darkMode') === 'enabled') {
                body.classList.add('dark-mode');
                themeButton.textContent = 'Modo Claro';
            }
            
            themeButton.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                const isDarkMode = body.classList.contains('dark-mode');
                
                if (isDarkMode) {
                    themeButton.textContent = 'Modo Claro';
                    localStorage.setItem('darkMode', 'enabled');
                } else {
                    themeButton.textContent = 'Modo Oscuro';
                    localStorage.setItem('darkMode', 'disabled');
                }
            });
        });
    </script>
</body>
</html>
