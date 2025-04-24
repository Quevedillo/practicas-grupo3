<?php
session_start();
require 'database.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    echo "Ticket no especificado.";
    exit();
}

$ticket_id = $_GET['id'];

// Obtener ticket
$sql_ticket = "SELECT * FROM tickets WHERE id = :id AND user_id = :user_id";
$stmt = $pdo->prepare($sql_ticket);
$stmt->execute(['id' => $ticket_id, 'user_id' => $_SESSION['id']]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    echo "Ticket no encontrado.";
    exit();
}

// Insertar nuevo comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['comment'])) {
    $sql_comment = "INSERT INTO comments (ticket_id, user_id, comment) VALUES (:ticket_id, :user_id, :comment)";
    $stmt = $pdo->prepare($sql_comment);
    $stmt->execute([
        'ticket_id' => $ticket_id,
        'user_id' => $_SESSION['id'],
        'comment' => $_POST['comment']
    ]);
    header("Location: ver_ticket.php?id=$ticket_id");
    exit();
}

// Obtener comentarios
$sql_comments = "SELECT c.comment, c.created_at, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.ticket_id = :ticket_id ORDER BY c.created_at ASC";
$stmt = $pdo->prepare($sql_comments);
$stmt->execute(['ticket_id' => $ticket_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener adjuntos
$sql_attachments = "SELECT * FROM attachments WHERE ticket_id = :ticket_id";
$stmt = $pdo->prepare($sql_attachments);
$stmt->execute(['ticket_id' => $ticket_id]);
$attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Ticket</title>
    <link rel="stylesheet" href="estilodashboard.css">
</head>
<body>
    <div class="container">
        <h2>Ticket: <?php echo htmlspecialchars($ticket['title']); ?></h2>
        <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
        <p><strong>Estado:</strong> <?php echo $ticket['status']; ?> | <strong>Prioridad:</strong> <?php echo $ticket['priority']; ?></p>
        <p><strong>Creado:</strong> <?php echo $ticket['created_at']; ?> | <strong>Última actualización:</strong> <?php echo $ticket['updated_at']; ?></p>

        <hr>

        <h3>Comentarios</h3>
        <?php if (count($comments) > 0): ?>
            <?php foreach ($comments as $c): ?>
                <div class="comment-box">
                    <p><strong><?php echo htmlspecialchars($c['username']); ?>:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($c['comment'])); ?></p>
                    <p><small><?php echo $c['created_at']; ?></small></p>
                    <hr>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay comentarios todavía.</p>
        <?php endif; ?>

        <form method="POST">
            <label for="comment">Agregar comentario:</label><br>
            <textarea name="comment" id="comment" rows="4" cols="50" required></textarea><br>
            <button type="submit">Enviar comentario</button>
        </form>

        <hr>

        <h3>Archivos Adjuntos</h3>
        <?php if (count($attachments) > 0): ?>
            <ul>
                <?php foreach ($attachments as $attachment): ?>
                    <li><a href="<?php echo htmlspecialchars($attachment['filepath']); ?>" download><?php echo htmlspecialchars($attachment['filename']); ?></a> (<?php echo round($attachment['filesize'] / 1024, 2); ?> KB)</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hay archivos adjuntos.</p>
        <?php endif; ?>

        <br>
        <a href="misTickets.php">← Volver a Mis Tickets</a>
    </div>
</body>
</html>
