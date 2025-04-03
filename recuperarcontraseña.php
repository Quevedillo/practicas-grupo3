<?php
session_start();

// Configuración de la base de datos (misma que en login.php)
$host = 'localhost';
$dbname = 'ticket';
$user = 'root';
$pass = '';

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Procesar solicitud de recuperación
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    // Verificar si el email existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Generar token único
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));
        
        // Guardar token en la base de datos
        $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_expires = :expires WHERE id = :id");
        $stmt->execute([
            'token' => $token,
            'expires' => $expires,
            'id' => $user['id']
        ]);
        
        // En producción, aquí enviarías el email con el enlace
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=$token";
        
        $success_message = "Se ha enviado un enlace de recuperación a tu email.";
    } else {
        $error_message = "No existe una cuenta con ese email.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Sistema de Tickets</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos consistentes con login.php */
        :root {
            --primary-color: #4CAF50;
            --dark-color: #333;
            --light-color: #f4f4f9;
            --danger-color: #e74c3c;
            --success-color: #2ecc71;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
        }
        
        .container {
            max-width: 450px;
            margin: 50px auto;
            padding: 20px;
        }
        
        .reset-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .reset-box h1 {
            text-align: center;
            margin-bottom: 25px;
            color: var(--primary-color);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .reset-button {
            width: 100%;
            background: var(--primary-color);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            font-weight: 600;
            text-align: center;
        }
        
        .error-message {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
        
        .success-message {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-box">
            <h1><i class="fas fa-key"></i> Recuperar Contraseña</h1>
            
            <?php if (isset($error_message)): ?>
                <div class="message error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            
            <?php if (isset($success_message)): ?>
                <div class="message success-message"><?php echo htmlspecialchars($success_message); ?></div>
                <div class="message"><small>Enlace de prueba: <?php echo $reset_link; ?></small></div>
            <?php else: ?>
                <form action="password_reset.php" method="POST">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <button type="submit" class="reset-button">Enviar Enlace de Recuperación</button>
                </form>
            <?php endif; ?>
            
            <a href="login.php" class="back-link"><i class="fas fa-arrow-left"></i> Volver al inicio de sesión</a>
        </div>
    </div>
</body>
</html>