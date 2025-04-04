<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "usuario", "contraseña", "base_de_datos");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener los datos del formulario
$id_ticket = $_POST['id'];
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$estado = $_POST['estado'];

// Actualizar el ticket en la base de datos
$sql = "UPDATE tickets SET titulo = ?, descripcion = ?, estado = ? WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssi", $titulo, $descripcion, $estado, $id_ticket);

if ($stmt->execute()) {
    echo "El ticket se ha actualizado correctamente.";
} else {
    echo "Error al actualizar el ticket: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
<br><a href="listar_tickets.php">Volver a la lista de tickets</a>
