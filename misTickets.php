<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

require 'database.php';

// Traer categorías
$catStmt = $pdo->query("SELECT id, name FROM categories");
$categorias = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Traer tickets del usuario con JOIN a categorías
$sql = "SELECT t.id, t.title, t.description, t.created_at, t.status, t.category_id, c.name AS category_name
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
    <title>Mis Tickets</title>
    <link rel="stylesheet" href="estilodashboard.css">
    <style>
        .ticket-filter { margin-bottom: 20px; background: #f0f0f0; padding: 15px; border-radius: 10px; }
        .ticket-filter form { display: flex; flex-wrap: wrap; gap: 15px; align-items: center; }
        .ticket-filter label { font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <header class="header">
        <div class="logo">
            <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" alt="Logo">
        </div>
        <div class="header-right">
            <div class="theme-toggle">
                <button id="theme-button">Modo Oscuro</button>
            </div>
            <div class="user-menu">
                <span><?= htmlspecialchars($_SESSION['username']); ?> ▼</span>
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

        <div class="ticket-filter">
            <h3>Filtrar</h3>
            <form id="filter-form">
                <label for="estado">Estado:</label>
                <select id="estado">
                    <option value="">Todos</option>
                    <option value="open">Abierto</option>
                    <option value="in_progress">En Progreso</option>
                    <option value="resolved">Resuelto</option>
                    <option value="closed">Cerrado</option>
                </select>

                <label for="categoria">Categoría:</label>
                <select id="categoria">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['name'] ?>"><?= $cat['name'] ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha">

                <button type="submit">Aplicar</button>
                <button type="button" id="reset">Reiniciar</button>
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
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tickets as $ticket): ?>
                <tr
                    data-estado="<?= $ticket['status'] ?>"
                    data-categoria="<?= $ticket['category_name'] ?>"
                    data-fecha="<?= date('Y-m-d', strtotime($ticket['created_at'])) ?>">
                    <td><?= htmlspecialchars($ticket['title']) ?></td>
                    <td><?= htmlspecialchars($ticket['description']) ?></td>
                    <td><?= htmlspecialchars($ticket['category_name']) ?></td>
                    <td><?= htmlspecialchars($ticket['status']) ?></td>
                    <td><?= htmlspecialchars($ticket['created_at']) ?></td>
                    <td>
                        <form action="eliminar_ticket.php" method="POST" onsubmit="return confirm('¿Eliminar este ticket?');">
                            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                            <button type="submit" class="delete-button">Eliminar</button>
                        </form>
                        <a href="editar_ticket.php?id=<?= $ticket['id'] ?>" class="edit-button">Editar</a>
                        <a href="ver_ticket.php?id=<?= $ticket['id'] ?>" class="view-button">Ver</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No hay tickets disponibles.</p>
        <?php endif; ?>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const themeBtn = document.getElementById('theme-button');
        const body = document.body;

        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            themeBtn.textContent = 'Modo Claro';
        }

        themeBtn.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', body.classList.contains('dark-mode') ? 'enabled' : 'disabled');
            themeBtn.textContent = body.classList.contains('dark-mode') ? 'Modo Claro' : 'Modo Oscuro';
        });

        // Filtro
        const form = document.getElementById('filter-form');
        const resetBtn = document.getElementById('reset');
        const rows = document.querySelectorAll('tbody tr');

        form.addEventListener('submit', e => {
            e.preventDefault();
            const estado = document.getElementById('estado').value;
            const categoria = document.getElementById('categoria').value;
            const fecha = document.getElementById('fecha').value;

            rows.forEach(row => {
                const rowEstado = row.dataset.estado;
                const rowCategoria = row.dataset.categoria;
                const rowFecha = row.dataset.fecha;

                let visible = true;
                if (estado && rowEstado !== estado) visible = false;
                if (categoria && rowCategoria !== categoria) visible = false;
                if (fecha && rowFecha !== fecha) visible = false;

                row.style.display = visible ? '' : 'none';
            });
        });

        resetBtn.addEventListener('click', () => {
            form.reset();
            rows.forEach(row => row.style.display = '');
        });
    });
</script>
</body>
</html>
