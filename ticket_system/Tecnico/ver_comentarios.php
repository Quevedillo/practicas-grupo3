<?php
// Requerir el archivo de conexión
require '../Conexion/database.php';

// Validar si existe el parámetro 'ticket_id' en la URL
if (!isset($_GET['ticket_id']) || !is_numeric($_GET['ticket_id'])) {
    die("Error: No se proporcionó un 'ticket_id' válido.");
}

$ticket_id = intval($_GET['ticket_id']);

// Procesar la eliminación de comentarios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment_id'])) {
    $delete_id = intval($_POST['delete_comment_id']);
    $delete_sql = "DELETE FROM comments WHERE id = :id";

    try {
        $stmt = $pdo->prepare($delete_sql);
        $stmt->execute(['id' => $delete_id]);
        header("Location: ../Tecnico/ver_comentarios.php?ticket_id=" . $ticket_id);
        exit();
    } catch (PDOException $e) {
        die("Error al eliminar el comentario: " . $e->getMessage());
    }
}

// Agregar un nuevo comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_comment'])) {
    $comment = trim($_POST['new_comment']);
    $user_id = 1; // Simulación de usuario autenticado

    if (!empty($comment)) {
        $insert_sql = "INSERT INTO comments (ticket_id, user_id, comment, created_at) VALUES (:ticket_id, :user_id, :comment, NOW())";

        try {
            $stmt = $pdo->prepare($insert_sql);
            $stmt->execute([
                'ticket_id' => $ticket_id,
                'user_id' => $user_id,
                'comment' => $comment
            ]);
            header("Location: ../Tecnico/ver_comentarios.php?ticket_id=" . $ticket_id);
            exit();
        } catch (PDOException $e) {
            die("Error al agregar el comentario: " . $e->getMessage());
        }
    } else {
        echo "El comentario no puede estar vacío.";
    }
}

// Obtener los comentarios relacionados con el ticket
$sql = "SELECT id, user_id, comment, created_at FROM comments WHERE ticket_id = :ticket_id";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ticket_id' => $ticket_id]);
    $comments = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al obtener los comentarios: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentarios del Ticket #<?= htmlspecialchars($ticket_id) ?></title>
    <link rel="stylesheet" href="../css/estilodashboard.css">
    <style>
        /* Estilos mínimos para el diseño */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        .chat-messages {
            margin: 20px 0;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .chat-message {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .chat-message:last-child {
            border-bottom: none;
        }
        .comment-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .comment-form button {
            display: inline-block;
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Comentarios del Ticket #<?= htmlspecialchars($ticket_id) ?></h1>
        </header>

        <main>
            <h3>Historial de Comentarios</h3>
            <div class="chat-messages">
                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="chat-message">
                            <strong>Usuario <?= htmlspecialchars($comment['user_id']) ?>:</strong>
                            <p><?= htmlspecialchars($comment['comment']) ?></p>
                            <p><small>Publicado el <?= htmlspecialchars($comment['created_at']) ?></small></p>
                            <form method="POST" action="ver_comentarios.php?ticket_id=<?= htmlspecialchars($ticket_id) ?>" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este comentario?');">
                                <input type="hidden" name="delete_comment_id" value="<?= htmlspecialchars($comment['id']) ?>">
                                <button type="submit">Eliminar</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay comentarios disponibles.</p>
                <?php endif; ?>
            </div>

            <h3>Agregar un Comentario</h3>
            <form method="POST" action="../Tecnico/ver_comentarios.php?ticket_id=<?= htmlspecialchars($ticket_id) ?>" class="comment-form">
                <textarea name="new_comment" rows="4" placeholder="Escribe tu comentario..." required></textarea>
                <button type="submit">Agregar Comentario</button>
            </form>
        </main>
    </div>
</body>
</html>