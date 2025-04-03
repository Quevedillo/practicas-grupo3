<?php
// migrar_passwords.php
include("database.php");

echo "<h2>Iniciando migración de contraseñas</h2>";

try {
    // Obtener todos los usuarios
    $stmt = $pdo->query("SELECT id, username, password FROM users");
    $users = $stmt->fetchAll();

    echo "<p>Encontrados " . count($users) . " usuarios</p>";
    echo "<ul>";

    foreach ($users as $user) {
        // Verificar si la contraseña ya está hasheada
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            echo "<li>Usuario {$user['username']} - Ya tiene contraseña hasheada</li>";
            continue;
        }

        // Hashear la contraseña en texto plano
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
        
        // Actualizar la base de datos
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hashedPassword, $user['id']]);
        
        echo "<li>Usuario {$user['username']} - Contraseña migrada exitosamente</li>";
    }

    echo "</ul>";
    echo "<h3>Migración completada. Todos los usuarios ahora tienen contraseñas hasheadas.</h3>";

} catch (PDOException $e) {
    die("<p style='color:red'>Error durante la migración: " . $e->getMessage() . "</p>");
}
?>