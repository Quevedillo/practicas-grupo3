<?php
require 'database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['comment_id']) && !empty($_POST['ticket_id']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'tech') {
        $comment_id = (int)$_POST['comment_id'];
        $ticket_id = (int)$_POST['ticket_id'];

        // Verificamos que el comentario existe y pertenece al ticket
        $checkStmt = $pdo->prepare("SELECT * FROM comments WHERE id = ? AND ticket_id = ?");
        $checkStmt->execute([$comment_id, $ticket_id]);

        if ($checkStmt->rowCount() === 1) {
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
            if ($stmt->execute([$comment_id])) {
                header("Location: ver_comentarios.php?ticket_id=$ticket_id");
                exit();
            } else {
                echo "Error al eliminar el comentario.";
            }
        } else {
            echo "No se encontró el comentario o no pertenece al ticket.";
        }
    } else {
        echo "Datos incompletos o usuario no autenticado.";
    }
} else {
    echo "Método no permitido.";
}
?>
