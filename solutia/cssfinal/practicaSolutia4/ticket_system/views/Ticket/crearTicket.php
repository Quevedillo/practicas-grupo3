<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
$database = new Database();
$pdo = $database->getConnection();

if (!isset($_SESSION['id'])) {
    header("Location: ../sesion/login.php");
    exit();
}

$user_id = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $category_id = $_POST['category'];
    $priority = $_POST['priority'];
    $description = trim($_POST['description']);
    $attachment_path = null;

    if (empty($title) || empty($category_id) || empty($priority) || empty($description)) {
        $_SESSION['error_message'] = "Todos los campos son obligatorios.";
        header("Location: crearTicket.php");
        exit();
    }

    if (!empty($_FILES['attachment']['name'])) {
        $upload_dir = "uploads/";
    
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                $_SESSION['error_message'] = "Error al crear la carpeta de subida.";
                header("Location: crearTicket.php");
                exit();
            }
        }
    
        $filename = basename($_FILES["attachment"]["name"]);
        $target_file = $upload_dir . time() . "_" . $filename;
        
        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
            $attachment_path = $target_file;
        } else {
            $_SESSION['error_message'] = "Error al subir el archivo.";
            header("Location: crearTicket.php");
            exit();
        }
    }
    
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO tickets (user_id, category_id, title, description, priority, status, created_at) VALUES (?, ?, ?, ?, ?, 'open', NOW())");
        $stmt->execute([$user_id, $category_id, $title, $description, $priority]);

        $ticket_id = $pdo->lastInsertId();

        if ($attachment_path) {
            $stmt = $pdo->prepare("INSERT INTO attachments (ticket_id, filename, filepath, filesize) VALUES (?, ?, ?, ?)");
            $stmt->execute([$ticket_id, $filename, $attachment_path, $_FILES['attachment']['size']]);
        }

        $pdo->commit();
        $_SESSION['success_message'] = "Se ha creado el ticket con éxito";
        header("Location: ../Cliente/dashboard.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Error al crear el ticket: " . $e->getMessage();
        header("Location: ../Ticket/crearTicket.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Ticket - Sistema de Tickets</title>
    <link rel="stylesheet" href="../css/estilocreacionticket.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
            <img src="https://camaradesevilla.com/wp-content/uploads/2024/07/S00-logo-Grupo-Solutia-v01-1.png" alt="Logo del Sistema">
            </div>
            <div class="header-right">
                <div class="theme-toggle">
                    <button onclick="toggleDarkMode()">Modo Oscuro</button>
                </div>
                <div class="user-menu">
                    <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'Usuario'); ?></span>
                </div>
            </div>
        </header>

        <nav class="navbar">
        <ul>
                <li><a href="../cliente/dashboard.php" class="active">Panel</a></li>
                <li><a href="../cliente/misTickets.php">Mis Tickets</a></li>
                <li><a href="../cliente/gestionPerfilUsuario.php">Editar Perfil</a></li>
                <li><a href="../cliente/clienteTecnico.php">Comunicación</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <?php 
                        echo $_SESSION['error_message']; 
                        unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="new-ticket-form">
                <h2>Crear Nuevo Ticket</h2>
                <form action="../Ticket/crearTicket.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Título:</label>
                        <input type="text" id="title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="category">Categoría:</label>
                        <select id="category" name="category" required>
                            <option value="1">Hardware</option>
                            <option value="2">Software</option>
                            <option value="3">Red</option>
                            <option value="4">Otros</option>
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
                        <button type="button" class="cancel-button" onclick="window.location.href='../cliente/dashboard.php'">Cancelar</button>
                        <button type="submit" class="create-ticket-button">Crear Ticket</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }
    </script>
</body>
</html>