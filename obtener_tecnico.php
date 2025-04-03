<?php
require 'database.php';

try {
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'tech'");
    $stmt->execute();
    $tecnicos = $stmt->fetchAll();
    echo json_encode($tecnicos);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al obtener técnicos: " . $e->getMessage()]);
}
