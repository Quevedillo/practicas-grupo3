<?php
session_start(); // Manejo de autenticación
require 'database.php'; // Asegúrate de que este archivo contenga la conexión correcta a la base de datos

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id']; // Obtener el user_id de la sesión

// Si se envió el formulario, procesar el ticket
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $description = $_POST['description'];

    // Manejo de archivo adjunto
    $attachment_path = null;
    if (!empty($_FILES['attachment']['name'])) {
        $upload_dir = "uploads/";
        $filename = basename($_FILES["attachment"]["name"]);
        $target_file = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
            $attachment_path = $target_file;
        } else {
            echo "Error al subir el archivo.";
            exit();
        }
    }

    // Inserción del ticket en la base de datos usando PDO
    try {
        $stmt = $pdo->prepare("INSERT INTO tickets (user_id, title, category, priority, description, attachment, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $title, $category, $priority, $description, $attachment_path]);

        // Redirigir al usuario después de crear el ticket
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al crear el ticket: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Ticket - Sistema de Tickets</title>
    <link rel="stylesheet" href="estilocreacionticket.css">
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
                </div>
            </div>
        </header>

        <nav class="navbar">
            <ul>
                <li><a href="index.php">Panel</a></li>
                <li><a href="mis_tickets.php">Mis Tickets</a></li>
                <li><a href="perfil.php">Perfil</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="new-ticket-form">
                <h2>Nuevo Ticket</h2>
                <form action="crear_nuevo_ticket.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Título:</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Categoría:</label>
                        <select id="category" name="category" required>
                            <option value="hardware">Hardware</option>
                            <option value="software">Software</option>
                            <option value="network">Red</option>
                            <option value="other">Otros</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="priority">Prioridad:</label>
                        <select id="priority" name="priority" required>
                            <option value="low">Baja</option>
                            <option value="medium">Media</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Descripción:</label>
                        <textarea id="description" name="description" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="attachment">Adjuntar archivo:</label>
                        <input type="file" id="attachment" name="attachment">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="cancel-button" onclick="window.location.href='dashboard.php'">Cancelar</button>
                        <button type="submit" class="create-ticket-button">Crear Ticket</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Script para cambiar entre modo oscuro y modo claro
        const themeButton = document.getElementById('theme-button');
        const body = document.body;

        themeButton.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                themeButton.textContent = 'Modo Claro';
            } else {
                themeButton.textContent = 'Modo Oscuro';
            }
        });
    </script>
</body>
</html>
