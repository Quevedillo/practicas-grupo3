<?php
require 'database.php';
session_start();

if (!isset($_GET['id'])) {
    die('ID de ticket no proporcionado.');
}

$ticket_id = $_GET['id'];

// Obtener datos del ticket
$stmt = $pdo->prepare("
    SELECT t.*, u.username AS cliente, c.name AS categoria
    FROM tickets t
    JOIN users u ON t.user_id = u.id
    JOIN categories c ON t.category_id = c.id
    WHERE t.id = ?
");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die("Ticket no encontrado.");
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $nuevo_estado = $_POST['nuevo_estado'] ?? $ticket['status'];
    $comentario = trim($_POST['comentario'] ?? '');
    $user_id = $_SESSION['user_id'] ?? 1; // técnico logueado (ajustar según sesión real)

    // Actualizar estado
    if ($nuevo_estado !== $ticket['status']) {
        $update = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
        $update->execute([$nuevo_estado, $ticket_id]);
    }

    // Insertar comentario si hay
    if (!empty($comentario)) {
        $insert = $pdo->prepare("INSERT INTO comments (ticket_id, user_id, comment) VALUES (?, ?, ?)");
        $insert->execute([$ticket_id, $user_id, $comentario]);
    }

    // Redirigir al dashboard
    header("Location: dashboardTecnico.php");
    exit;
}

// Obtener comentarios y adjuntos
$comentarios = $pdo->prepare("
    SELECT c.*, u.username 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.ticket_id = ? 
    ORDER BY c.created_at DESC
");
$comentarios->execute([$ticket_id]);

$adjuntos = $pdo->prepare("SELECT * FROM attachments WHERE ticket_id = ?");
$adjuntos->execute([$ticket_id]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Ticket</title>
</head>
<body>
    <h1>Detalles del Ticket #<?php echo htmlspecialchars($ticket['id']); ?></h1>
    <p><strong>Título:</strong> <?php echo htmlspecialchars($ticket['title']); ?></p>
    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($ticket['cliente']); ?></p>
    <p><strong>Categoría:</strong> <?php echo htmlspecialchars($ticket['categoria']); ?></p>
    <p><strong>Prioridad:</strong> <?php echo htmlspecialchars($ticket['priority']); ?></p>
    <p><strong>Estado actual:</strong> <?php echo htmlspecialchars($ticket['status']); ?></p>
    <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>

    <form method="POST">
        <h3>Actualizar Estado</h3>
        <label for="nuevo_estado">Nuevo Estado:</label>
        <select name="nuevo_estado">
            <option value="open" <?php if ($ticket['status'] == 'open') echo 'selected'; ?>>Abierto</option>
            <option value="in_progress" <?php if ($ticket['status'] == 'in_progress') echo 'selected'; ?>>En progreso</option>
            <option value="resolved" <?php if ($ticket['status'] == 'resolved') echo 'selected'; ?>>Resuelto</option>
            <option value="closed" <?php if ($ticket['status'] == 'closed') echo 'selected'; ?>>Cerrado</option>
        </select>

        <h3>Agregar Comentario</h3>
        <textarea name="comentario" rows="4" cols="50" placeholder="Escribe un comentario..."></textarea>

        <h3>Archivos Adjuntos</h3>
        <ul>
            <?php foreach ($adjuntos as $archivo): ?>
                <li>
                    <a href="<?php echo htmlspecialchars($archivo['filepath']); ?>" download>
                        <?php echo htmlspecialchars($archivo['filename']); ?>
                    </a> (<?php echo round($archivo['filesize'] / 1024, 2); ?> KB)
                </li>
            <?php endforeach; ?>
        </ul>

        <h3>Comentarios</h3>
        <?php foreach ($comentarios as $com): ?>
            <div style="border: 1px solid #ccc; margin: 10px 0; padding: 5px;">
                <strong><?php echo htmlspecialchars($com['username']); ?></strong> (<?php echo $com['created_at']; ?>):<br>
                <?php echo nl2br(htmlspecialchars($com['comment'])); ?>
            </div>
        <?php endforeach; ?>

        <br><br>
        <button type="submit" name="actualizar">Actualizar</button>
        <div class="back-button">
            <a href="dashboardTecnico.php" class="btn btn-primary">Volver al Panel</a>
        </div>
    </form>
</body>
</html>
