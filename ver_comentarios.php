<?php
require 'database.php';

// Verificar que el ticket_id esté presente en la URL
if (isset($_GET['ticket_id'])) {
    $ticket_id = $_GET['ticket_id'];

    // Obtener el ticket y sus comentarios
    $stmt = $pdo->prepare("SELECT t.*, u.username as cliente, c.name as categoria 
                           FROM tickets t
                           JOIN users u ON t.user_id = u.id
                           JOIN categories c ON t.category_id = c.id
                           WHERE t.id = ?");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch();

    // Obtener los comentarios para el ticket
    $commentStmt = $pdo->prepare("SELECT c.comment, c.created_at, u.username
                                  FROM comments c
                                  JOIN users u ON c.user_id = u.id
                                  WHERE c.ticket_id = ?
                                  ORDER BY c.created_at DESC");
    $commentStmt->execute([$ticket_id]);
    $comments = $commentStmt->fetchAll();
} else {
    echo "Ticket no encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentarios del Ticket #<?php echo $ticket['id']; ?></title>
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
            </div>
        </header>

        <nav class="navbar">
            <ul>
                <li><a href="panel_tecnico.php" class="active">Panel Técnico</a></li>
                <li><a href="gestionPerfilTecnico.php">Editar Perfil</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="ticket-details">
                <h2>Detalles del Ticket #<?php echo htmlspecialchars($ticket['id']); ?></h2>
                <p><strong>Título:</strong> <?php echo htmlspecialchars($ticket['title']); ?></p>
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($ticket['cliente']); ?></p>
                <p><strong>Categoría:</strong> <?php echo htmlspecialchars($ticket['categoria']); ?></p>
                <p><strong>Prioridad:</strong> <?php echo htmlspecialchars($ticket['priority']); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($ticket['status']); ?></p>
                <p><strong>Creado el:</strong> <?php echo htmlspecialchars($ticket['created_at']); ?></p>
            </div>

            <div class="comments-section">
                <h3>Comentarios</h3>
                <?php if (count($comments) > 0): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-item">
                            <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                            <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                            <p><small>Publicado el <?php echo htmlspecialchars($comment['created_at']); ?></small></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay comentarios para este ticket.</p>
                <?php endif; ?>
            </div>

            <div class="back-button">
                <a href="dashboardTecnico.php" class="btn btn-primary">Volver al Panel</a>
            </div>
        </main>
    </div>
</body>
</html>
