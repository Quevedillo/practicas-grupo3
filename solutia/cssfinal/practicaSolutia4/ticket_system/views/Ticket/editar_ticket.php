<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
$database = new Database();
$pdo = $database->getConnection();

if (!isset($_SESSION['id'])) {
    header('Location: ../sesion/login.php');
    exit();
}

// Verificar que el 'id' del ticket se pase por la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("El ticket no existe.");
}

// Obtener el ID del ticket desde la URL
$id_ticket = $_GET['id'];

// Consultar los datos actuales del ticket
$sql = "SELECT * FROM tickets WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id_ticket]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die("El ticket no existe.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ticket</title>
</head>
<body>
    <h1>Editar Ticket</h1>
    <form action="../Ticket/procesar_edicion.php?id=<?php echo $ticket['id']; ?>" method="POST">
    <input type="hidden" name="id" value="<?php echo $ticket['id']; ?>">
    <label for="titulo">Título:</label><br>
    <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($ticket['title']); ?>" required><br><br>

    <label for="descripcion">Descripción:</label><br>
    <textarea id="descripcion" name="descripcion" rows="5" required><?php echo htmlspecialchars($ticket['description']); ?></textarea><br><br>

    <button type="submit">Guardar Cambios</button>
</form>

</body>
</html>
