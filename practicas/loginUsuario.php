<?php
// backend/login.php

// Database connection
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "ticket";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user input
$username = $_POST['usuario'];
$password = $_POST['contraseña'];

// Prepare and execute query
$stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // Verify password (in a real-world scenario, use password_verify() with hashed passwords)
    if ($password === $user['password']) {
        // Start session and store user information
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // Redirect based on user role
        switch ($user['role']) {
            case 'admin':
                header("Location: admin_dashboard.php");
                break;
            case 'tech':
                header("Location: tech_dashboard.php");
                break;
            case 'client':
                header("Location: client_dashboard.php");
                break;
            default:
                header("Location: index.php");
        }
        exit();
    } else {
        $error = "Contraseña incorrecta";
    }
} else {
    $error = "Usuario no encontrado";
}

// If login fails, redirect back to login page with error message
header("Location: ../loginUsuario.php?error=" . urlencode($error));
exit();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Recuperar Contraseña</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function toggleRecuperar() {
            var recuperarDiv = document.getElementById("recuperarDiv");
            recuperarDiv.style.display = recuperarDiv.style.display === "none" ? "block" : "none";
        }
    </script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <img href="logo" alt="logo" src="./logo.png">
                <h3 class="text-center">Iniciar Sesión</h3>
                <form action="backend/login.php" method="POST">
                    <h6>Sistema de Tickets de Soporte</h6>
                    <label>Usuario:</label>
                    <input type="user" name="usuario" class="form-control my-2" required>
                    <label>Contraseña:</label>
                    <input type="password" name="contraseña" class="form-control my-2" required>
                    <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                    <a href="#" class="d-block text-center mt-2" onclick="toggleRecuperar()">¿Olvidó tu contraseña?</a>
                    <a href="registroUsuario.php" class="btn btn-link w-100 mt-3">Registrarse</a>
                </form>
                <div id="recuperarDiv" style="display: none;" class="mt-3">
                    <form action="backend/recuperar.php" method="POST">
                        <input type="email" name="correo_recuperar" class="form-control my-2" placeholder="Introduce tu correo" required>
                        <button type="submit" class="btn btn-warning w-100">Recuperar contraseña</button>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>