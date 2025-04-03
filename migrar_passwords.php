<?php
// migrar_passwords.php
include("database.php");

echo "<pre>"; // Mejor formato para la salida
echo "=== INICIANDO MIGRACIÓN DE CONTRASEÑAS ===\n\n";

try {
    // 1. Verificar conexión a la base de datos
    $pdo->query("SELECT 1")->fetch();
    echo "✓ Conexión a la base de datos funcionando\n";

    // 2. Obtener todos los usuarios
    $stmt = $pdo->query("SELECT id, username, password FROM users");
    $users = $stmt->fetchAll();

    echo "✓ Encontrados " . count($users) . " usuarios\n\n";

    $migrados = 0;
    $ya_hasheados = 0;

    foreach ($users as $user) {
        echo "Procesando usuario: {$user['username']}\n";
        echo "Contraseña actual: {$user['password']}\n";

        // Verificar si la contraseña ya está hasheada
        if (password_verify('cualquiercosa', $user['password']) || 
            strpos($user['password'], '$2y$') === 0) {
            echo "→ Ya tiene contraseña hasheada (no se modifica)\n";
            $ya_hasheados++;
        } else {
            // Hashear la contraseña en texto plano
            $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
            
            // Actualizar la base de datos
            $update = $pdo->query("UPDATE users SET password = '$hashedPassword' WHERE id = {$user['id']}");
            
            // Verificar si se actualizó
            $updated = $update->rowCount();
            
            if ($updated > 0) {
                echo "→ Contraseña migrada a: $hashedPassword\n";
                $migrados++;
            } else {
                echo "→ Error al actualizar este usuario\n";
            }
        }
        echo str_repeat("-", 50) . "\n";
    }

    echo "\nRESULTADO FINAL:\n";
    echo "- Usuarios migrados: $migrados\n";
    echo "- Usuarios ya hasheados: $ya_hasheados\n";
    echo "- Total procesados: " . count($users) . "\n\n";
    echo "=== MIGRACIÓN COMPLETADA ===\n";

} catch (PDOException $e) {
    echo "\nERROR DURANTE LA MIGRACIÓN:\n";
    echo $e->getMessage() . "\n";
    echo "En la línea: " . $e->getLine() . "\n";
}
echo "</pre>";
?>