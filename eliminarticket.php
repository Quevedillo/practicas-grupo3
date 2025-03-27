<?php
// eliminar-ticket.php
session_start();

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Incluir configuración de base de datos
require 'config/database.php';

$nombreUsuario = $_SESSION['username'] ?? "Usuario";
$mensaje = '';

// Procesar eliminación de ticket
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ticket_id'])) {
    $ticket_id = $_POST['ticket_id'];
    $user_id = $_SESSION['user_id'];
    
    try {
        // Verificar que el ticket pertenece al usuario antes de eliminar
        $stmt = $conn->prepare("SELECT id FROM tickets WHERE id = :ticket_id AND user_id = :user_id");
        $stmt->bindParam(':ticket_id', $ticket_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Eliminar el ticket
            $stmt = $conn->prepare("DELETE FROM tickets WHERE id = :ticket_id");
            $stmt->bindParam(':ticket_id', $ticket_id);
            $stmt->execute();
            
            $mensaje = "Ticket eliminado correctamente.";
        } else {
            $mensaje = "No tienes permiso para eliminar este ticket o no existe.";
        }
    } catch(PDOException $e) {
        $mensaje = "Error al eliminar el ticket: " . $e->getMessage();
    }
}

// Obtener tickets del usuario
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, title, status, created_at FROM tickets WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Tickets - Sistema de Tickets</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 800px;
            margin: 0 auto;
            border: 1px solid #ccc;
        }
        .header {
            background-color: #f0f0f0;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav {
            background-color: #e0e0e0;
            padding: 10px;
        }
        .content {
            padding: 20px;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>LOGO</div>
            <div><?php echo htmlspecialchars($nombreUsuario); ?> ▼</div>
        </div>
        <div class="nav">
            <a href="dashboard.php">Panel</a>   
            <a href="mis-tickets.php">Mis Tickets</a> 
            <a href="perfil.php">Perfil</a>
        </div>
        <div class="content">
            <h2>Mis Tickets - Eliminar</h2>
            
            <?php if (!empty($mensaje)): ?>
                <div class="message <?php echo strpos($mensaje, 'correctamente') !== false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($tickets)): ?>
                <p>No tienes tickets creados.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ticket['id']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['status']); ?></td>               
                                <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                                <td>
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display: inline;">
                                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este ticket?');">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>