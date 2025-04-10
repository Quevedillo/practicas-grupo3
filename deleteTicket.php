<?php
require 'database.php';

// Verificar si se ha recibido el comment_id por POST
if (isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];

    // Eliminar el comentario de la base de datos
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);

    // Obtener el ticket_id del comentario para redirigir
    $ticket_id = $_GET['ticket_id']; // O tomar el ticket_id de otro modo si lo prefieres
    header("Location: ver_comentarios.php?ticket_id=$ticket_id"); // Redirigir a la página de comentarios del ticket
    exit();
} else {
    echo "Comentario no encontrado.";
    exit();
}
?>
