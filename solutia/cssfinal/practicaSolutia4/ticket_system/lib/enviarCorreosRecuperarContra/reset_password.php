<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
$host = '192.167.1.248';
$dbname = 'ticket_system';
$user = 'force4-8';
$pass = 'force_1453';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Verificar si el correo electrónico está en la URL
if (isset($_GET['email']) && filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
    $email = $_GET['email'];
    
    // Verificar si el correo electrónico existe en la base de datos
    $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // El usuario existe, mostrar formulario de cambio de contraseña
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['new_password']) && !empty($_POST['new_password'])) {
                $new_password = $_POST['new_password'];
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Actualizar la contraseña en la base de datos
                $update_stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
                $update_stmt->execute(['password' => $hashed_password, 'email' => $email]);

                // Redirigir al login con mensaje de éxito
                header('Location: ../../views/sesion/login.php?success=1');
                exit();
            }
        }

        // Mostrar formulario de cambio de contraseña
        echo "<h2>Cambiar contraseña</h2>";
        echo "<form method='POST'>";
        echo "    <label for='new_password'>Nueva contraseña:</label>";
        echo "    <input type='password' name='new_password' id='new_password' required>";
        echo "    <button type='submit'>Cambiar contraseña</button>";
        echo "</form>";
        echo "<p><a href='../sesion/login.php'>Volver al login</a></p>";
    } else {
        echo "No se encontró un usuario con ese correo electrónico.";
        echo "<p><a href='olvidarContra.php'>Volver a intentar</a></p>";
        exit();
    }
} else {
    echo "Acceso no autorizado. Asegúrate de que el enlace es correcto.";
    echo "<p><a href='olvidarContra.php'>Solicitar nuevo enlace</a></p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
</head>
<body>
    <h2>Restablecer Contraseña</h2>
    <form method="POST">
        <label for="new_password">Nueva Contraseña:</label>
        <input type="password" name="new_password" required>
        <button type="submit">Restablecer Contraseña</button>
    </form>
</body>
</html>
