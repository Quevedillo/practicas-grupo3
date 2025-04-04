<?php
// Incluir el archivo de conexión a la base de datos
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticket_id = $_POST['ticket_id'];

    try {
        // Preparar la consulta SQL para eliminar el ticket
        $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = :ticket_id");

        // Ejecutar la consulta pasando el ticket_id como parámetro
        $stmt->execute(['ticket_id' => $ticket_id]);

        // Redirigir al usuario después de eliminar el ticket (opcional)
        header('Location: misTickets.php'); // Cambia a la página que prefieras
        exit();
    } catch (PDOException $e) {
        echo "Error al eliminar el ticket: " . $e->getMessage();
    }
} else {
    echo "No se ha recibido un ID de ticket válido.";
}
?>
