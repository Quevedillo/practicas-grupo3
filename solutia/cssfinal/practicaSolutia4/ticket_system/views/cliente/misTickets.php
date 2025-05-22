<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
$database = new Database();
$pdo = $database->getConnection();

if (!isset($_SESSION['id'])) {
    header('Location: ../sesion/login.php');
    exit();
}

// Filtrado
$estado = $_GET['status'] ?? '';
$categoria = $_GET['category'] ?? '';
$fecha_inicio = $_GET['start_date'] ?? '';
$fecha_fin = $_GET['end_date'] ?? '';
$orderBy = $_GET['orderby'] ?? 'created_at';
$orderDir = $_GET['dir'] ?? 'desc';
$allowedFields = ['title', 'category_name', 'created_at'];
$allowedDir = ['asc', 'desc'];

if (!in_array($orderBy, $allowedFields)) {
    $orderBy = 'created_at';
}
if (!in_array($orderDir, $allowedDir)) {
    $orderDir = 'desc';
}

$sql = "SELECT t.id, t.title, t.description, t.created_at, t.status, c.name AS category_name 
        FROM tickets t 
        JOIN categories c ON t.category_id = c.id 
        WHERE t.user_id = :user_id";

$params = ['user_id' => $_SESSION['id']];

if (!empty($estado)) {
    $sql .= " AND t.status = :status";
    $params['status'] = $estado;
}
if (!empty($categoria)) {
    $sql .= " AND c.name = :category";
    $params['category'] = $categoria;
}
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $sql .= " AND DATE(t.created_at) BETWEEN :start_date AND :end_date";
    $params['start_date'] = $fecha_inicio;
    $params['end_date'] = $fecha_fin;
}

$sql .= " ORDER BY $orderBy $orderDir";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Función para alternar dirección de orden
function toggleDir($currentDir) {
    return $currentDir === 'asc' ? 'desc' : 'asc';
}

function linkWithOrder($field, $label, $currentField, $currentDir) {
    $newDir = ($field === $currentField) ? toggleDir($currentDir) : 'asc';
    $query = $_GET;
    $query['orderby'] = $field;
    $query['dir'] = $newDir;
    $url = htmlspecialchars($_SERVER['PHP_SELF']) . '?' . http_build_query($query);
    return "<a href=\"$url\">$label</a>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Tickets</title>
    <link rel="stylesheet" href="../css/estilodashboard.css">
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
                    <a class="dropdown-item" href="/solutia/cssfinal/practicaSolutia4/index.php?controller=user&action=logout">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </header>

    <nav class="navbar">
    <ul>
                <li><a href="../cliente/dashboard.php">Panel</a></li>
                <li><a href="../cliente/misTickets.php" class="active">Mis Tickets</a></li>
                <li><a href="../cliente/gestionPerfilUsuario.php">Editar Perfil</a></li>
                <li><a href="../cliente/clienteTecnico.php">Comunicación</a></li>
            </ul>
    </nav>

    <main class="main-content">
        <h2>Mis Tickets</h2>

        <!-- Formulario de filtrado -->
        <form method="GET" class="filter-form">
            <label for="status">Estado:</label>
            <select name="status" id="status">
                <option value="">Todos</option>
                <option value="open" <?= $estado === 'open' ? 'selected' : '' ?>>Abierto</option>
                <option value="in_progress" <?= $estado === 'in_progress' ? 'selected' : '' ?>>En progreso</option>
                <option value="resolved" <?= $estado === 'resolved' ? 'selected' : '' ?>>Resuelto</option>
                <option value="closed" <?= $estado === 'closed' ? 'selected' : '' ?>>Cerrado</option>
            </select>

            <label for="category">Categoría:</label>
            <select name="category" id="category">
                <option value="">Todas</option>
                <option value="Hardware" <?= $categoria === 'Hardware' ? 'selected' : '' ?>>Hardware</option>
                <option value="Software" <?= $categoria === 'Software' ? 'selected' : '' ?>>Software</option>
                <option value="Red" <?= $categoria === 'Red' ? 'selected' : '' ?>>Red</option>
                <option value="Otros" <?= $categoria === 'Otros' ? 'selected' : '' ?>>Otros</option>
            </select>

            <label for="start_date">Desde:</label>
            <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($fecha_inicio) ?>">

            <label for="end_date">Hasta:</label>
            <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($fecha_fin) ?>">

            <button type="submit">Filtrar</button>
        </form>

        <?php if (count($tickets) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th><?= linkWithOrder('title', 'Título', $orderBy, $orderDir) ?></th>
                    <th>Descripción</th>
                    <th><?= linkWithOrder('category_name', 'Categoría', $orderBy, $orderDir) ?></th>
                    <th><?= linkWithOrder('created_at', 'Fecha de creación', $orderBy, $orderDir) ?></th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?= htmlspecialchars($ticket['title']) ?></td>
                    <td><?= htmlspecialchars($ticket['description']) ?></td>
                    <td><?= htmlspecialchars($ticket['category_name']) ?></td>
                    <td><?= htmlspecialchars($ticket['created_at']) ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', htmlspecialchars($ticket['status']))) ?></td>
                    <td>
                        <a href="../Ticket/editar_ticket.php?id=<?= $ticket['id'] ?>" class="edit-button">Editar</a>
                        <a href="../Ticket/ver_ticket.php?id=<?= $ticket['id'] ?>" class="view-button">Ver Comentarios</a>
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
document.addEventListener('DOMContentLoaded', function () {
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
