<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

require 'database.php';

$sql = "SELECT id, title, description, created_at FROM tickets WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $_SESSION['id']]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tickets</title>
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
                <li><a href="dashboard.php">Panel</a></li>
                <li><a href="misTickets.php" class="active">Mis Tickets</a></li>
                <li><a href="gestionPerfilUsuario.php">Editar Perfil</a></li>
                <li><a href="clienteTecnico.php">Comunicación</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <h2>Mis Tickets</h2>
            <?php if (count($tickets) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Fecha de creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['description']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                        <td>
                            <form action="eliminar_ticket.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este ticket?');">
                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                <button type="submit" class="delete-button">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No tienes tickets registrados.</p>
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
