<?php
include 'database.php';

$id_ticket = $_GET['id_ticket']; // o lo recibes por POST o sesión

$sql = "SELECT usuario, accion, fecha FROM acciones_ticket WHERE id_ticket = :id ORDER BY fecha ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_ticket]);

$acciones = $stmt->fetchAll();

echo "<h3>Historial de acciones del ticket #$id_ticket</h3>";
if ($acciones) {
    echo "<ul>";
    foreach ($acciones as $a) {
        echo "<li><strong>{$a['usuario']}</strong>: {$a['accion']} <em>({$a['fecha']})</em></li>";
    }
    echo "</ul>";
} else {
    echo "Este ticket no tiene historial aún.";
}
?>
