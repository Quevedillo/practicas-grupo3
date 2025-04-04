<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

require 'database.php';  // Asegúrate de que este archivo tenga la conexión PDO correcta

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
    <form action="procesar_edicion.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $ticket['id']; ?>">
        <label for="titulo">Título:</label><br>
        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($ticket['titulo']); ?>" required><br><br>

        <label for="descripcion">Descripción:</label><br>
        <textarea id="descripcion" name="descripcion" rows="5" required><?php echo htmlspecialchars($ticket['descripcion']); ?></textarea><br><br>

        <label for="estado">Estado:</label><br>
        <select id="estado" name="estado">
            <option value="Abierto" <?php if ($ticket['estado'] == 'Abierto') echo 'selected'; ?>>Abierto</option>
            <option value="En Proceso" <?php if ($ticket['estado'] == 'En Proceso') echo 'selected'; ?>>En Proceso</option>
            <option value="Cerrado" <?php if ($ticket['estado'] == 'Cerrado') echo 'selected'; ?>>Cerrado</option>
        </select><br><br>

        <button type="submit">Guardar Cambios</button>
    </form>
</body>
</html>

<?php
$stmt->close();
$conexion->close();
?>
