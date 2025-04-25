<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'], $_POST['ticket_id'])) {
    $comment_id = $_POST['comment_id'];
    $ticket_id = $_POST['ticket_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'], $_POST['ticket_id'])) {
    $comment_id = $_POST['comment_id'];
    $ticket_id = $_POST['ticket_id'];

    // Eliminar el comentario
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);

    // Redirigir al ticket
    header("Location: ver_comentarios.php?ticket_id=" . urlencode($ticket_id));
    exit();
} else {
    echo "No se pudo eliminar el comentario.";
}