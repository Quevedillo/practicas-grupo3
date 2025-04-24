<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['comment_id']) && !empty($_POST['ticket_id']) && isset($_SESSION['id'])) {
        $comment_id = (int)$_POST['comment_id'];
        $ticket_id = (int)$_POST['ticket_id'];
        $current_user_id = $_SESSION['id'];
        $current_user_role = $_SESSION['role'] ?? 'client';

        // Verificar que el comentario pertenece al usuario o es admin
        $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
        $stmt->execute([$comment_id]);
        $comment = $stmt->fetch();

        if ($comment && ($comment['user_id'] == $current_user_id || $current_user_role === 'admin')) {
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->execute([$comment_id]);
            header("Location: ver_comentarios.php?ticket_id=$ticket_id");
            exit();
        } else {
            echo "No tienes permisos para eliminar este comentario.";
        }
    } else {
        echo "Datos incompletos o usuario no autenticado.";
    }
} else {
    echo "Método no permitido.";
}
?>
