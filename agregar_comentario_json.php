<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'], $_POST['comment'])) {
    $ticket_id = $_POST['ticket_id'];
    $new_comment = trim($_POST['comment']);
    
    // Verificar que el ticket pertenece al usuario
    $stmt = $pdo->prepare("SELECT id, description FROM tickets WHERE id = ? AND user_id = ?");
    $stmt->execute([$ticket_id, $_SESSION['id']]);
    $ticket = $stmt->fetch();
    
    if ($ticket) {
        // Procesar la descripción existente para añadir el comentario
        $new_description = addCommentToDescription($ticket['description'], $new_comment, $_SESSION);
        
        // Actualizar el ticket con la nueva descripción
        $stmt = $pdo->prepare("UPDATE tickets SET description = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$new_description, $ticket_id]);
    }
}

// Función para añadir comentario a la descripción
function addCommentToDescription($oldDescription, $newComment, $session) {
    // Intentar decodificar la descripción actual
    $data = json_decode($oldDescription, true);
    
    // Si no es JSON válido, crear nueva estructura
    if (json_last_error() !== JSON_ERROR_NONE) {
        $data = [
            'original_description' => $oldDescription,
            'comments' => []
        ];
    } elseif (!isset($data['comments'])) {
        // Si es JSON pero no tiene comentarios, inicializar array
        $data['comments'] = [];
    }
    
    // Añadir nuevo comentario
    $data['comments'][] = [
        'user_id' => $session['id'],
        'username' => $session['username'],
        'comment' => $newComment,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    return json_encode($data, JSON_UNESCAPED_UNICODE);
}

header("Location: misTickets.php");
exit();
?>