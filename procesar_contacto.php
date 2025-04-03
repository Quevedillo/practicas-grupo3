<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asunto = trim($_POST['asunto'] ?? '');
    $tecnico_id = trim($_POST['tecnico'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (empty($asunto) || empty($tecnico_id) || empty($mensaje)) {
        die("Error: Todos los campos son obligatorios");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO tickets (user_id, category_id, title, description, priority) VALUES (?, 4, ?, ?, 'medium')");
        $stmt->execute([$tecnico_id, $asunto, $mensaje]);
        header("Location: contacto.php?success=1");
        exit;
    } catch (PDOException $e) {
        die("Error al enviar el mensaje: " . $e->getMessage());
    }
} else {
    die("Acceso no permitido");
}
