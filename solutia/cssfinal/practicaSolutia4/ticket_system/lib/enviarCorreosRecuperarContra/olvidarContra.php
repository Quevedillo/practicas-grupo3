<?php
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
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

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // Buscar usuario por correo electrónico
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Simulación: aquí deberías generar un token y guardarlo en la base de datos

        // Envío de correo usando PHPMailer
        // require 'vendor/autoload.php';
        // use PHPMailer\PHPMailer\PHPMailer;
        // use PHPMailer\PHPMailer\Exception;

        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'correosistematickets@gmail.com'; // Cambia esto si usas otro correo
            $mail->Password = 'dfvh dxja brej vaqp'; // Tu clave de app de Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinatario y contenido
            $mail->setFrom('correosistematickets@gmail.com', 'Solutia');
            $mail->addAddress($email, $user['username']);
            $mail->isHTML(true);
            $mail->Subject = 'Recuperacion de clave - Solutia';

$mail->isHTML(true);


// Alternativa en texto plano
$mail->Body = "<h3>Hola {$user['username']},</h3><p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p><p><a href='http://" . $_SERVER['HTTP_HOST'] . "/solutia/cssfinal/practicaSolutia4/ticket_system/lib/enviarCorreosRecuperarContra/reset_password.php?email=" . urlencode($email) . "'>Restablecer contraseña</a></p>";


            $mail->send();
            $success = "Se ha enviado un correo de recuperación a tu email.";
        } catch (Exception $e) {
            $error = "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    } else {
        $error = "No se encontró un usuario con ese correo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>
</head>
<body>
    <h2>Recuperar contraseña</h2>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif (isset($success)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Correo electrónico:</label>
        <input type="email" name="email" required>
        <button type="submit">Enviar</button>
    </form>

    <p><a href="../../views/sesion/login.php">Volver al login</a></p>
</body>
</html>
