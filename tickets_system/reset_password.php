<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'ticket_system';
$user = 'root';
$pass = '';

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
        // El usuario existe, proceder a cambiar la contraseña
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_password = $_POST['new_password'];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Actualizar la contraseña en la base de datos
            $update_stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
            $update_stmt->execute(['password' => $hashed_password, 'email' => $email]);

            // Mensaje de éxito y botón para volver al login
            echo "<p style='color: green;'>Tu contraseña ha sido restablecida correctamente.</p>";
            echo "<a href='login.php'><button>Volver al Login</button></a>";
            exit();
        }

    } else {
        echo "No se encontró un usuario con ese correo electrónico.";
        exit();
    }
} else {
    echo "Acceso no autorizado. Asegúrate de que el enlace es correcto.";
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
