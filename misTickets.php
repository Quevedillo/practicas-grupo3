<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

require 'database.php';

$sql = "SELECT t.id, t.title, t.description, t.created_at, t.status, c.name AS category_name
        FROM tickets t
        JOIN categories c ON t.category_id = c.id
        WHERE t.user_id = :user_id";
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

            <!-- Filtros -->
            <div class="filtros">
                <form id="filtro-form">
                    <label for="estado">Estado:</label>
                    <select id="estado">
                        <option value="">Todos</option>
                        <option value="open">Abierto</option>
                        <option value="in_progress">En Proceso</option>
                        <option value="resolved">Resuelto</option>
                        <option value="closed">Cerrado</option>
                    </select>

                    <label for="categoria">Categoría:</label>
                    <select id="categoria">
                        <option value="">Todas</option>
                        <option value="Hardware">Hardware</option>
                        <option value="Software">Software</option>
                        <option value="Red">Red</option>
                        <option value="Otros">Otros</option>
                    </select>

                    <label for="fecha_inicio">Desde:</label>
                    <input type="date" id="fecha_inicio">

                    <label for="fecha_fin">Hasta:</label>
                    <input type="date" id="fecha_fin">

                    <button type="submit">Aplicar</button>
                    <button type="reset" onclick="location.reload()">Reiniciar</button>
                </form>
            </div>

            <?php if (count($tickets) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Fecha de creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="ticket-table">
                    <?php foreach ($tickets as $ticket): ?>
                    <tr data-estado="<?php echo $ticket['status']; ?>" data-categoria="<?php echo $ticket['category_name']; ?>" data-fecha="<?php echo substr($ticket['created_at'], 0, 10); ?>">
                        <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['description']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                        <td>
                            <form action="eliminar_ticket.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este ticket?');">
                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                <button type="submit" class="delete-button">Eliminar</button>
                            </form>
                            <a href="editar_ticket.php?id=<?php echo $ticket['id']; ?>" class="edit-button">Editar</a>
                            <a href="ver_ticket.php?id=<?php echo $ticket['id']; ?>" class="view-button">Ver Comentarios</a>
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

            const form = document.getElementById('filtro-form');
            const rows = document.querySelectorAll('#ticket-table tr');

            form.addEventListener('submit', e => {
                e.preventDefault();
                const estado = document.getElementById('estado').value;
                const categoria = document.getElementById('categoria').value;
                const fechaInicio = document.getElementById('fecha_inicio').value;
                const fechaFin = document.getElementById('fecha_fin').value;

                rows.forEach(row => {
                    const rowEstado = row.dataset.estado;
                    const rowCategoria = row.dataset.categoria;
                    const rowFecha = row.dataset.fecha;

                    let visible = true;
                    if (estado && rowEstado !== estado) visible = false;
                    if (categoria && rowCategoria !== categoria) visible = false;
                    if (fechaInicio && rowFecha < fechaInicio) visible = false;
                    if (fechaFin && rowFecha > fechaFin) visible = false;

                    row.style.display = visible ? '' : 'none';
                });
            });
        });
    </script>
</body>
</html>
