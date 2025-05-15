<?php
session_start();
require '../Conexion/database.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../sesion/login.php');
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
    header("Location: ../Ticket/ver_ticket.php?id=$ticket_id");
    exit();
}

// Obtener comentarios
$sql_comments = "SELECT c.comment, c.created_at, u.username 
                 FROM comments c 
                 JOIN users u ON c.user_id = u.id 
                 WHERE c.ticket_id = :ticket_id 
                 ORDER BY c.created_at ASC";
$stmt = $pdo->prepare($sql_comments);
$stmt->execute(['ticket_id' => $ticket_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener archivos adjuntos
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
    <link rel="stylesheet" href="../css/estilodashboard.css">
</head>
<body>
    <div class="container">
        <h2>Ticket: <?php echo htmlspecialchars($ticket['title']); ?></h2>
        <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
        <p><strong>Estado:</strong> <?php echo $ticket['status']; ?> | <strong>Prioridad:</strong> <?php echo $ticket['priority']; ?></p>
        <p><strong>Creado:</strong> <?php echo $ticket['created_at']; ?></p>

        <?php if (!empty($attachments)): ?>
            <p><strong>Archivos adjuntos:</strong></p>
            <ul>
                <?php foreach ($attachments as $file): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($file['filepath']); ?>" download="<?php echo htmlspecialchars($file['filename']); ?>">
                            <?php echo htmlspecialchars($file['filename']); ?> (<?php echo round($file['filesize'] / 1024, 2); ?> KB)
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

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
        <?php endif; ?>

        <form method="POST">
            <label for="comment">Agregar comentario:</label><br>
            <textarea name="comment" id="comment" rows="4" cols="50" required></textarea><br>
            <button type="submit">Enviar comentario</button>
        </form>

        <br>
        <a href="../Cliente/misTickets.php">← Volver a Mis Tickets</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeButton = document.getElementById('theme-button');
            const body = document.body;

            if (localStorage.getItem('darkMode') === 'enabled') {
                body.classList.add('dark-mode');
                themeButton.textContent = 'Modo Claro';
            }

            themeButton?.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                const isDarkMode = body.classList.contains('dark-mode');
                themeButton.textContent = isDarkMode ? 'Modo Claro' : 'Modo Oscuro';
                localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
            });
        });
    </script>
</body>
</html>
