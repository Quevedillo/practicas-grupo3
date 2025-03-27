<?php
// clienteTecnico.php
session_start();
require 'database.php'; // 

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obtener rol del usuario
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Procesar envío de mensaje
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mensaje'])) {
    $mensaje = htmlspecialchars($_POST['mensaje']);
    
    // Aquí deberías insertar el mensaje en tu tabla de comentarios
    // Ejemplo básico (adaptar a tu estructura):
    try {
        $stmt = $conn->prepare("INSERT INTO comments (ticket_id, user_id, comment) VALUES (?, ?, ?)");
        // Necesitarías el ticket_id, esto es un ejemplo
        $stmt->execute([1, $user_id, $mensaje]); 
        
        $_SESSION['mensaje_exito'] = "Mensaje enviado correctamente.";
    } catch(PDOException $e) {
        $_SESSION['mensaje_error'] = "Error al enviar: " . $e->getMessage();
    }
    header("Location: clienteTecnico.php");
    exit();
}

// Obtener mensajes existentes (ejemplo básico)
$mensajes = [];
try {
    $stmt = $conn->prepare("SELECT c.*, u.username, u.role 
                           FROM comments c 
                           JOIN users u ON c.user_id = u.id 
                           ORDER BY c.created_at ASC");
    $stmt->execute();
    $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error al cargar mensajes: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunicación Cliente-Técnico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .client-message { background-color: #e3f2fd; margin-left: auto; max-width: 70%; }
        .tech-message { background-color: #f1f1f1; margin-right: auto; max-width: 70%; }
        .message-box { border-radius: 10px; padding: 10px; margin-bottom: 10px; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h3 class="text-center">Comunicación Cliente-Técnico</h3>

                <!-- Mostrar mensajes de éxito/error -->
                <?php if (isset($_SESSION['mensaje_exito'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['mensaje_exito'] ?></div>
                    <?php unset($_SESSION['mensaje_exito']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['mensaje_error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['mensaje_error'] ?></div>
                    <?php unset($_SESSION['mensaje_error']); ?>
                <?php endif; ?>

                <!-- Formulario de mensaje -->
                <div class="mb-4">
                    <h5>Enviar un mensaje</h5>
                    <form method="POST">
                        <textarea name="mensaje" class="form-control my-2" placeholder="Escribe tu mensaje aquí..." required></textarea>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </form>
                </div>

                <!-- Historial de mensajes -->
                <div>
                    <h5>Historial de comunicación</h5>
                    <div id="mensajes">
                        <?php foreach ($mensajes as $msg): ?>
                            <div class="message-box <?= $msg['role'] == 'client' ? 'client-message' : 'tech-message' ?>">
                                <p><strong><?= htmlspecialchars($msg['username']) ?>:</strong> 
                                <?= nl2br(htmlspecialchars($msg['comment'])) ?></p>
                                <small><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (empty($mensajes)): ?>
                            <div class="alert alert-info">No hay mensajes todavía.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>