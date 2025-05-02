<?php
session_start();
require '../Conexion/database.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asunto = trim($_POST['asunto'] ?? '');
    $tecnico_id = trim($_POST['tecnico'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (empty($asunto) || empty($tecnico_id) || empty($mensaje)) {
        die("Error: Todos los campos son obligatorios");
    }

    // Verificar que el usuario está autenticado
    if (!isset($_SESSION['id'])) {
        die("Error: No se ha iniciado sesión.");
    }

    try {
        // Obtener el email del técnico
        $stmt = $pdo->prepare("SELECT email, username FROM users WHERE id = ? AND role = 'tech'");
        $stmt->execute([$tecnico_id]);
        $tecnico = $stmt->fetch();

        if (!$tecnico) {
            die("Error: Técnico no encontrado");
        }

        // Obtener el correo electrónico y nombre del usuario logueado
        $user_id = $_SESSION['id'];  // Usamos el id del usuario logueado desde la sesión
        $stmt = $pdo->prepare("SELECT email, username FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            die("Error: Usuario no encontrado");
        }

        // Instanciar PHPMailer y configuración SMTP
        $mail = new PHPMailer(true);
        $mail->isSMTP();

        // Configuración SMTP para Gmail
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $usuario['email'];  // El correo del usuario logueado desde la base de datos
        $mail->Password = 'contraseña_de_aplicación';  // Contraseña de aplicación de Gmail (debe ser proporcionada por el usuario)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configuración de los correos
        $mail->setFrom($usuario['email'], $usuario['username']);  // Usamos el correo y nombre del usuario logueado
        $mail->addAddress($tecnico['email'], $tecnico['username']); // Enviar al técnico
        $mail->Subject = "Mensaje del sistema: " . $asunto;
        $mail->Body = $mensaje;

        // Enviar el mensaje
        if ($mail->send()) {
            header("Location: ../Cliente/clienteTecnico.php?success=1");
            exit;
        } else {
            die("Error al enviar el correo: " . $mail->ErrorInfo);
        }

    } catch (Exception $e) {
        die("Error al enviar el correo: " . $mail->ErrorInfo);
    }
}
?>