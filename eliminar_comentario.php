<?php
require 'database.php';

if (isset($_POST['comment_id']) && isset($_POST['ticket_id'])) {
    $comment_id = $_POST['comment_id'];
    $ticket_id = $_POST['ticket_id'];

    // Eliminar el comentario
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);

    // Redirigir de nuevo al ticket
    header("Location: ver_comentarios.php?ticket_id=$ticket_id");
    exit();
} else {
    echo "Datos insuficientes para eliminar el comentario.";
    exit();
}
?>