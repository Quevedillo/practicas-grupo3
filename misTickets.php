<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

require 'database.php';

// Obtener tickets del usuario
$sql = "SELECT id, title, description, created_at, status FROM tickets WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $_SESSION['id']]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Función para procesar los comentarios desde la descripción
function getTicketComments($description) {
    // Intentar decodificar como JSON
    $data = json_decode($description, true);
    
    // Si es JSON válido y tiene comentarios
    if (json_last_error() === JSON_ERROR_NONE && isset($data['comments'])) {
        return $data['comments'];
    }
    
    // Si no es JSON, tratar la descripción como primer comentario
    return [
        [
            'user_id' => $_SESSION['id'],
            'username' => $_SESSION['username'],
            'comment' => $description,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ];
}

// Función para añadir un comentario a la descripción
function addCommentToDescription($oldDescription, $newComment) {
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
        'user_id' => $_SESSION['id'],
        'username' => $_SESSION['username'],
        'comment' => $newComment,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    return json_encode($data, JSON_UNESCAPED_UNICODE);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tickets</title>
    <link rel="stylesheet" href="estilodashboard.css">
    <style>
        .comment-section {
            margin-top: 15px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .comment {
            margin-bottom: 10px;
            padding: 8px;
            background: white;
            border-radius: 4px;
            border-left: 3px solid #4CAF50;
        }
        .comment-header {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        .comment-date {
            font-size: 0.8em;
            color: #666;
        }
        .comment-form {
            margin-top: 15px;
        }
        .comment-form textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
            min-height: 80px;
        }
        .comment-form button {
            margin-top: 8px;
            padding: 8px 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .comment-form button:hover {
            background: #45a049;
        }
        .ticket-details {
            cursor: pointer;
        }
        .ticket-details:hover {
            background-color: #f9f9f9;
        }
        .original-description {
            font-style: italic;
            margin-bottom: 15px;
            padding: 8px;
            background: #f0f0f0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" alt="Logo del Sistema">
            </div>
            <div class="header-right">
                <div class="theme-toggle">
                    <button id="theme-button">Modo Oscuro</button>
                </div>
                <div class="user-menu">
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?> ▼</span>
                    <div class="user-dropdown">
                        <a href="logout.php">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </header>

        <nav class="navbar">
            <ul>
                <li><a href="dashboard.php">Panel</a></li>
                <li><a href="misTickets.php" class="active">Mis Tickets</a></li>
                <li><a href="gestionPerfilUsuario.php">Editar Perfil</a></li>
                <li><a href="clienteTecnico.php">Comunicación</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <h2>Mis Tickets</h2>
            <?php if (count($tickets) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Estado</th>
                        <th>Fecha de creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                    <tr class="ticket-details" onclick="toggleComments(<?php echo $ticket['id']; ?>)">
                        <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                        <td>
                            <form action="eliminar_ticket.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este ticket?');">
                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                <button type="submit" class="delete-button">Eliminar</button>
                            </form>
                            <a href="editar_ticket.php?id=<?php echo $ticket['id']; ?>" class="edit-button">Editar</a>
                        </td>
                    </tr>
                    <tr id="comments-row-<?php echo $ticket['id']; ?>" style="display: none;">
                        <td colspan="4">
                            <div class="comment-section">
                                <h3>Historial</h3>
                                <?php 
                                $commentsData = json_decode($ticket['description'], true);
                                $isJson = (json_last_error() === JSON_ERROR_NONE);
                                
                                if ($isJson && isset($commentsData['original_description'])): ?>
                                    <div class="original-description">
                                        <strong>Descripción original:</strong>
                                        <p><?php echo htmlspecialchars($commentsData['original_description']); ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php 
                                $comments = getTicketComments($ticket['description']);
                                if (count($comments) > 0): 
                                    foreach ($comments as $comment): ?>
                                        <div class="comment">
                                            <div class="comment-header">
                                                <?php echo htmlspecialchars($comment['username'] ?? 'Usuario'); ?>
                                                <span class="comment-date">- <?php echo htmlspecialchars($comment['created_at'] ?? ''); ?></span>
                                            </div>
                                            <div class="comment-text"><?php echo htmlspecialchars($comment['comment']); ?></div>
                                        </div>
                                    <?php endforeach; 
                                else: ?>
                                    <p>No hay comentarios aún.</p>
                                <?php endif; ?>
                                
                                <form class="comment-form" action="agregar_comentario_json.php" method="POST">
                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                    <textarea name="comment" placeholder="Escribe tu comentario aquí..." required></textarea>
                                    <button type="submit">Agregar Comentario</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No tienes tickets registrados.</p>
            <?php endif; ?>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeButton = document.getElementById('theme-button');
            const body = document.body;

            if (localStorage.getItem('darkMode') === 'enabled') {
                body.classList.add('dark-mode');
                themeButton.textContent = 'Modo Claro';
            }

            themeButton.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                const isDarkMode = body.classList.contains('dark-mode');

                themeButton.textContent = isDarkMode ? 'Modo Claro' : 'Modo Oscuro';
                localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
            });
        });

        function toggleComments(ticketId) {
            const commentsRow = document.getElementById(`comments-row-${ticketId}`);
            commentsRow.style.display = commentsRow.style.display === 'none' ? 'table-row' : 'none';
        }
    </script>
</body>
</html>